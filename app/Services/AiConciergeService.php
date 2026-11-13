<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InquiryStatus;
use App\Models\AiKnowledgeEntry;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiConciergeService
{
    /**
     * Proses pesan pelanggan dengan AI Concierge yang dilengkapi pengecekan katalog real-time,
     * Knowledge Base, Guardrails (Batasan AI), serta Human Handoff otomatis.
     *
     * @return array{
     *     handled_by: string,
     *     handling_mode: string,
     *     reply: ?string,
     *     action_btn: ?array{label: string, url: string, external?: bool},
     *     matched_products?: array<int, array<string, mixed>>,
     *     rule_applied?: ?string
     * }
     */
    public function handleCustomerMessage(string $rawMessage, ?Inquiry $inquiry = null, ?User $user = null): array
    {
        $cleanMessage = trim($rawMessage);
        $q = strtolower($cleanMessage);

        // 1. Jika tiket sudah berada dalam mode Human Admin ("tidak sepenuhnya AI yang handle"),
        // AI tidak menimpa percakapan staf manusia.
        if ($inquiry && $inquiry->handling_mode === 'human') {
            return [
                'handled_by' => 'human',
                'handling_mode' => 'human',
                'reply' => null,
                'action_btn' => null,
                'rule_applied' => 'Mode Admin Manusia Aktif',
            ];
        }

        // 2. Cek permintaan eksplisit untuk berbicara dengan Admin / CS Manusia
        if ($this->isExplicitHumanRequest($q)) {
            $handoffReply = 'Tentu Kak, percakapan Anda sedang kami hubungkan ke **Customer Service PUTRIJALVA**. Mohon tunggu sebentar, tim kami akan segera merespons pesan Anda di sini.';

            if ($inquiry) {
                $inquiry->update([
                    'handling_mode' => 'human',
                    'status' => InquiryStatus::UNREAD,
                ]);
                $this->recordAiMessage($inquiry, $handoffReply);
            }

            return [
                'handled_by' => 'handoff',
                'handling_mode' => 'human',
                'reply' => $handoffReply,
                'action_btn' => null,
                'rule_applied' => 'Eskalasi Manual ke Customer Service',
            ];
        }

        // 3. Cek Batasan AI (Guardrails / Boundaries) dari tabel ai_knowledge_entries
        $boundaryMatch = $this->findMatchingKnowledge($q, ['boundary']);
        if ($boundaryMatch) {
            $reply = "**[Batasan Layanan AI - {$boundaryMatch->title}]**\n".$boundaryMatch->content;
            $mode = $boundaryMatch->requires_human_handoff ? 'human' : 'ai';

            if ($inquiry) {
                if ($boundaryMatch->requires_human_handoff) {
                    $inquiry->update([
                        'handling_mode' => 'human',
                        'status' => InquiryStatus::UNREAD,
                    ]);
                } else {
                    $inquiry->update(['status' => InquiryStatus::RESPONDED]);
                }
                $this->recordAiMessage($inquiry, $reply);
            }

            return [
                'handled_by' => $boundaryMatch->requires_human_handoff ? 'handoff' : 'ai_boundary',
                'handling_mode' => $mode,
                'reply' => $reply,
                'action_btn' => null,
                'rule_applied' => $boundaryMatch->title,
            ];
        }

        // 4. Cek Pengecekan Nomor Pesanan & Pelacakan Resi Real-Time dari Database
        $orderResult = $this->checkOrderAndTracking($cleanMessage, $q, $user);
        if ($orderResult !== null) {
            if ($inquiry) {
                $inquiry->update(['status' => InquiryStatus::RESPONDED]);
                $this->recordAiMessage($inquiry, $orderResult['reply']);
            }

            return [
                'handled_by' => 'ai',
                'handling_mode' => 'ai',
                'reply' => $orderResult['reply'],
                'action_btn' => $orderResult['action_btn'],
                'rule_applied' => 'Pengecekan Pesanan & Resi Database',
            ];
        }

        // 4.5 Cek Sapaan Murni Pelanggan (Pure Greeting: "hallo", "halo kak", "selamat siang", "hello", dll.)
        if ($this->isPureGreeting($q)) {
            $isEn = app()->getLocale() === 'en';
            if ($isEn) {
                $customerGreeting = $user ? "Hello **{$user->name}**!" : 'Hello!';
                $greetingFallback = "{$customerGreeting} Welcome to **PUTRIJALVA** luxury fashion boutique ✨ How may we assist you with our collections, size guide, or your order today?";
                $actionLabel = 'Explore Collection';
            } else {
                $customerGreeting = $user ? "Halo Kak **{$user->name}**!" : 'Halo Kak!';
                $greetingFallback = "{$customerGreeting} Selamat datang di butik busana **PUTRIJALVA** ✨ Ada yang bisa kami bantu seputar koleksi busana, panduan ukuran (size chart), atau pesanan Anda hari ini?";
                $actionLabel = 'Buka Katalog Busana';
            }

            $greetingReply = $this->callExternalAiApi($cleanMessage, $greetingFallback, $user, $inquiry) ?? $greetingFallback;

            if ($inquiry) {
                $inquiry->update(['status' => InquiryStatus::RESPONDED]);
                $this->recordAiMessage($inquiry, $greetingReply);
            }

            return [
                'handled_by' => 'ai',
                'handling_mode' => 'ai',
                'reply' => $greetingReply,
                'action_btn' => [
                    'label' => $actionLabel,
                    'url' => route('shop.index'),
                    'external' => false,
                ],
                'rule_applied' => 'Sapaan Ramah Konsultan Butik',
            ];
        }

        // 5. Cek Pengecekan Barang, Harga & Stok Real-Time dari Database Katalog (0 Token - Langsung Database)
        $catalogResult = $this->checkProductAndPriceCatalog($q);
        if ($catalogResult !== null) {
            $finalReply = $catalogResult['reply'];

            // Jika pelanggan meminta rekomendasi / konsultasi gaya dan external AI API aktif,
            // beri sentuhan AI personal yang ringkas dengan referensi produk ready stock di atas
            if (! empty($catalogResult['is_recommendation'])) {
                $aiStylistReply = $this->callExternalAiApi($cleanMessage, $catalogResult['reply'], $user, $inquiry);
                if ($aiStylistReply !== null && trim($aiStylistReply) !== '') {
                    $finalReply = $aiStylistReply;
                }
            }

            if ($inquiry) {
                $inquiry->update(['status' => InquiryStatus::RESPONDED]);
                $this->recordAiMessage($inquiry, $finalReply);
            }

            return [
                'handled_by' => 'ai',
                'handling_mode' => 'ai',
                'reply' => $finalReply,
                'action_btn' => $catalogResult['action_btn'],
                'matched_products' => $catalogResult['matched_products'] ?? [],
                'rule_applied' => ! empty($catalogResult['is_recommendation']) ? 'Rekomendasi Gaya & Produk Terfavorit' : 'Pengecekan Katalog Barang & Harga Real-Time',
            ];
        }

        // 6. Cek Knowledge Base Toko (0 Token - Langsung Database)
        $knowledgeMatch = $this->findMatchingKnowledge($q, ['knowledge']);
        if ($knowledgeMatch) {
            $reply = $knowledgeMatch->content;
            $mode = $knowledgeMatch->requires_human_handoff ? 'human' : 'ai';

            if ($inquiry) {
                if ($knowledgeMatch->requires_human_handoff) {
                    $inquiry->update([
                        'handling_mode' => 'human',
                        'status' => InquiryStatus::UNREAD,
                    ]);
                } else {
                    $inquiry->update(['status' => InquiryStatus::RESPONDED]);
                }
                $this->recordAiMessage($inquiry, $reply);
            }

            return [
                'handled_by' => $knowledgeMatch->requires_human_handoff ? 'handoff' : 'ai',
                'handling_mode' => $mode,
                'reply' => $reply,
                'action_btn' => [
                    'label' => 'Buka Katalog Busana',
                    'url' => route('shop.index'),
                    'external' => false,
                ],
                'rule_applied' => $knowledgeMatch->title,
            ];
        }

        // 7. Sapaan atau Pertanyaan Umum -> Gunakan LLM Eksternal Ringkas atau Salam Bawaan Toko
        $totalActive = Product::where('is_active', true)->where('stock', '>', 0)->count();
        $minPrice = (float) (Product::where('is_active', true)->min('price') ?? 18);
        $maxPrice = (float) (Product::where('is_active', true)->max('price') ?? 120);

        $customerGreeting = $user ? "Halo Kak **{$user->name}**!" : 'Halo Kak!';
        $fallbackReply = "{$customerGreeting} Selamat datang di **PUTRIJALVA** ✨\n\nSaat ini tersedia **{$totalActive} koleksi busana pilihan** siap kirim dengan rentang harga **$ ".number_format($minPrice, 2).' – $ '.number_format($maxPrice, 2)."**.\n\nAda yang bisa kami bantu seputar koleksi busana, ukuran (size), atau pesanan Anda hari ini? Anda juga dapat mengetik **\"Hubungi CS\"** kapan saja jika ingin berbicara langsung dengan tim kami.";

        $externalReply = $this->callExternalAiApi($cleanMessage, $fallbackReply, $user, $inquiry);
        $defaultReply = $externalReply ?? $fallbackReply;

        // Auto-Learn: Simpan otomatis pertanyaan & jawaban baru ke Knowledge Base agar pertanyaan serupa di masa mendatang langsung dijawab dari lokal
        if ($externalReply !== null && ! $this->isPureGreeting($q) && mb_strlen($cleanMessage) >= 10) {
            $this->autoLearnKnowledge($cleanMessage, $externalReply);
        }

        if ($inquiry) {
            $inquiry->update(['status' => InquiryStatus::RESPONDED]);
            $this->recordAiMessage($inquiry, $defaultReply);
        }

        return [
            'handled_by' => 'ai',
            'handling_mode' => 'ai',
            'reply' => $defaultReply,
            'action_btn' => [
                'label' => 'Lihat Semua Koleksi',
                'url' => route('shop.index'),
                'external' => false,
            ],
            'rule_applied' => 'Respons Asisten Katalog Default',
        ];
    }

    /**
     * Buat saran draft balasan pintar untuk Admin di halaman /admin/livechat.
     */
    public function suggestDraftForAdmin(Inquiry $inquiry): string
    {
        $lastCustomerMsg = $inquiry->messages()
            ->whereIn('sender_type', ['customer', 'user'])
            ->latest('id')
            ->first();

        $queryText = $lastCustomerMsg?->message ?? $inquiry->subject ?? '';
        $q = strtolower($queryText);

        $catalogCheck = $this->checkProductAndPriceCatalog($q);
        if ($catalogCheck !== null) {
            $clean = str_replace('**', '', $catalogCheck['reply']);

            return "Halo Kak {$inquiry->name}, terima kasih telah menghubungi PUTRIJALVA Concierge.\n\n{$clean}\n\nApakah ada varian atau ukuran yang ingin kami bantu siapkan?";
        }

        $knowledge = $this->findMatchingKnowledge($q, ['knowledge', 'response_rule', 'boundary']);
        if ($knowledge !== null) {
            return "Halo Kak {$inquiry->name}, terkait pertanyaan Anda mengenai {$knowledge->title}:\n\n{$knowledge->content}\n\nSilakan beri tahu saya jika ada hal lain yang dapat kami bantu.";
        }

        return "Halo Kak {$inquiry->name}, terima kasih telah menghubungi tim Concierge PUTRIJALVA. Pesan Anda mengenai \"{$inquiry->subject}\" sedang kami tindak lanjuti secara langsung. Bagaimana kami dapat membantu melengkapi pesanan Anda hari ini?";
    }

    protected function isExplicitHumanRequest(string $q): bool
    {
        $triggers = [
            'hubungi admin',
            'panggil admin',
            'bicara dengan admin',
            'admin manusia',
            'cs manusia',
            'operator manusia',
            'live agent',
            'staf manusia',
            'bantuan manusia',
            'minta admin',
            'bicara sama cs',
            'chat admin langsung',
        ];

        foreach ($triggers as $trigger) {
            if (str_contains($q, $trigger)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Deteksi apakah pesan pelanggan adalah sapaan murni (pure greeting) tanpa topik spesifik.
     */
    protected function isPureGreeting(string $q): bool
    {
        $clean = trim((string) preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($q)));
        if ($clean === '') {
            return false;
        }

        $words = array_values(array_filter(preg_split('/\s+/', $clean) ?: []));
        if (count($words) === 0 || count($words) > 5) {
            return false;
        }

        $greetings = [
            'halo', 'hallo', 'hello', 'hai', 'hi', 'hei', 'hey',
            'selamat', 'pagi', 'siang', 'sore', 'malam',
            'assalamualaikum', 'assalamu\'alaikum', 'kulonuwun',
            'permisi', 'tes', 'test', 'ping', 'p',
        ];

        $fillers = [
            'kak', 'kakak', 'min', 'admin', 'sis', 'sist', 'gan',
            'putri', 'putrij', 'putrijalva', 'jalva', 'ya', 'dong', 'kah', 'apakah', 'ada', 'orang',
            'semua', 'di', 'sana', 'cs', 'assistant', 'bot',
        ];

        $greetingFound = false;
        foreach ($words as $w) {
            if (in_array($w, $greetings, true)) {
                $greetingFound = true;
            } elseif (! in_array($w, $fillers, true)) {
                return false;
            }
        }

        return $greetingFound;
    }

    /**
     * @param  array<int, string>  $types
     */
    protected function findMatchingKnowledge(string $q, array $types): ?AiKnowledgeEntry
    {
        /** @var Collection<int, AiKnowledgeEntry> $entries */
        $entries = AiKnowledgeEntry::query()
            ->where('is_active', true)
            ->whereIn('type', $types)
            ->get();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($entries as $entry) {
            $keywords = array_filter(array_map('trim', explode(',', strtolower((string) $entry->keywords))));
            $score = 0;

            foreach ($keywords as $kw) {
                if ($kw === '') {
                    continue;
                }

                if (str_contains($kw, ' ')) {
                    // Frasa multi-kata (contoh: "size chart", "tukar size")
                    if (str_contains($q, $kw)) {
                        $score += strlen($kw) * 2;
                    }
                } else {
                    // Kata tunggal: Wajib cocok seluruh kata (whole word boundary) agar huruf 'l' / 's' tidak memicu match palsu pada 'hallo'
                    if (preg_match('/\b'.preg_quote($kw, '/').'\b/i', $q)) {
                        $score += max(strlen($kw), 2);
                    }
                }
            }

            if ($score >= 3 && $score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $entry;
            }
        }

        return $bestMatch;
    }

    /**
     * Ekstrak kata kunci bersih dan relevan dari teks pertanyaan pelanggan untuk disimpan ke Knowledge Base.
     */
    public function extractKeywordsFromText(string $text): string
    {
        $clean = strtolower(trim((string) preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text)));
        $rawWords = array_values(array_filter(preg_split('/\s+/', $clean) ?: []));

        $stopwords = [
            'apa', 'apakah', 'bagaimana', 'kenapa', 'mengapa', 'kapan', 'siapa', 'dimana', 'mana',
            'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'adalah', 'yaitu', 'ini', 'itu',
            'saya', 'kami', 'kita', 'kamu', 'anda', 'kak', 'kakak', 'min', 'admin', 'sis', 'sist',
            'mau', 'tanya', 'tolong', 'dong', 'ya', 'kan', 'ada', 'tidak', 'nggak', 'bukan', 'bisa',
            'kah', 'pun', 'juga', 'sudah', 'telah', 'sedang', 'akan', 'lagi', 'lebih', 'paling',
            'sangat', 'sekali', 'tentang', 'seperti', 'halo', 'hai', 'selamat', 'pagi', 'siang',
            'sore', 'malam', 'permisi', 'gimana', 'beneran', 'masih', 'punya', 'kira', 'makasih',
            'terima', 'kasih',
        ];

        $filteredWords = [];
        foreach ($rawWords as $w) {
            if (mb_strlen($w) >= 3 && ! in_array($w, $stopwords, true)) {
                $filteredWords[] = $w;
            }
        }

        $keywords = [];

        // 1. Tambahkan frasa 2-kata jika memungkinkan (misal: "cara merawat", "dress sutra")
        $filteredCount = count($filteredWords);
        for ($i = 0; $i < min($filteredCount - 1, 3); $i++) {
            $keywords[] = $filteredWords[$i].' '.$filteredWords[$i + 1];
        }

        // 2. Tambahkan kata-kata tunggal unik
        foreach ($filteredWords as $w) {
            if (! in_array($w, $keywords, true)) {
                $keywords[] = $w;
            }
        }

        $uniqueKeywords = array_unique(array_filter($keywords));

        return Str::limit(implode(', ', array_slice($uniqueKeywords, 0, 8)), 250, '');
    }

    /**
     * Simpan pertanyaan dan jawaban secara otomatis ke dalam AI Knowledge Base.
     */
    public function autoLearnKnowledge(string $question, string $answer): ?AiKnowledgeEntry
    {
        $q = strtolower(trim($question));

        // Hindari duplikasi jika pertanyaan sudah memiliki kecocokan di Knowledge Base
        if ($this->findMatchingKnowledge($q, ['knowledge']) !== null) {
            return null;
        }

        $keywords = $this->extractKeywordsFromText($question);
        if ($keywords === '') {
            return null;
        }

        $cleanTitle = 'FAQ: '.Str::limit(trim($question), 70, '');

        try {
            return AiKnowledgeEntry::create([
                'type' => 'knowledge',
                'title' => $cleanTitle,
                'keywords' => $keywords,
                'content' => trim($answer),
                'requires_human_handoff' => false,
                'is_active' => true,
            ]);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Pengecekan Barang, Harga, dan Stok Real-Time langsung dari tabel `products` & `categories`.
     *
     * @return ?array{reply: string, action_btn: array{label: string, url: string, external?: bool}, matched_products: array<int, array<string, mixed>>}
     */
    protected function checkProductAndPriceCatalog(string $q): ?array
    {
        /** @var Collection<int, Product> $allProducts */
        $allProducts = Product::with('category')->where('is_active', true)->get();
        if ($allProducts->isEmpty()) {
            return null;
        }

        // A. Cek apakah pelanggan menyebut nama produk spesifik atau SKU
        /** @var Collection<int, Product> $directMatches */
        $directMatches = $allProducts->filter(function (Product $product) use ($q): bool {
            $nameLower = strtolower($product->name);
            $skuLower = strtolower((string) $product->sku);

            if ($skuLower !== '' && str_contains($q, $skuLower)) {
                return true;
            }

            if (str_contains($q, $nameLower)) {
                return true;
            }

            // Cek kata kunci unik nama produk (minimal 4 huruf dan bukan kata generik)
            $genericWords = ['dress', 'gaun', 'midi', 'maxi', 'baju', 'atasan', 'blouse', 'top', 'shirt', 'kemeja', 'celana', 'pants', 'rok', 'skirt', 'linen', 'silk', 'katun', 'cotton', 'outer', 'blazer', 'set', 'with'];
            $words = preg_split('/\s+/', $nameLower) ?: [];
            foreach ($words as $word) {
                $cleanWord = preg_replace('/[^a-z0-9]/', '', $word) ?? '';
                if (strlen($cleanWord) >= 4 && ! in_array($cleanWord, $genericWords, true) && preg_match('/\b'.preg_quote($cleanWord, '/').'\b/', $q)) {
                    return true;
                }
            }

            return false;
        })->take(3);

        if ($directMatches->isNotEmpty()) {
            if ($directMatches->count() === 1) {
                /** @var Product $p */
                $p = $directMatches->first();
                $stockText = $p->stock > 0 ? "Tersedia (**{$p->stock} pcs** siap kirim)" : '**Stok Habis (Sold Out)**';
                $priceText = $p->formatted_price;
                if ($p->original_price && (float) $p->original_price > (float) $p->price) {
                    $priceText .= " *(Harga normal: {$p->formatted_original_price})*";
                }

                $reply = "Berikut hasil pengecekan katalog real-time untuk **{$p->name}** ({$p->sku}):\n"
                    .'• **Kategori:** '.($p->category->name ?? 'Busana & Pakaian')."\n"
                    ."• **Harga Resmi:** **{$priceText}**\n"
                    ."• **Status Stok:** {$stockText}\n"
                    .'• **Detail:** '.Str::limit((string) $p->description, 120);

                return [
                    'reply' => $reply,
                    'action_btn' => [
                        'label' => "Lihat {$p->name} ({$p->formatted_price})",
                        'url' => route('shop.show', $p->slug),
                        'external' => false,
                    ],
                    'matched_products' => [
                        [
                            'id' => $p->id,
                            'name' => $p->name,
                            'sku' => $p->sku,
                            'price' => $p->formatted_price,
                            'stock' => $p->stock,
                            'url' => route('shop.show', $p->slug),
                        ],
                    ],
                ];
            }

            $lines = ['Berikut hasil pengecekan produk yang cocok di katalog kami:'];
            foreach ($directMatches as $p) {
                $st = $p->stock > 0 ? "Stok: {$p->stock} pcs" : 'Habis';
                $lines[] = "• **{$p->name}** — **{$p->formatted_price}** ({$st})";
            }

            /** @var Product $first */
            $first = $directMatches->first();

            return [
                'reply' => implode("\n", $lines),
                'action_btn' => [
                    'label' => "Buka {$first->name}",
                    'url' => route('shop.show', $first->slug),
                    'external' => false,
                ],
                'matched_products' => $directMatches->map(fn (Product $p): array => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => $p->formatted_price,
                    'stock' => $p->stock,
                    'url' => route('shop.show', $p->slug),
                ])->values()->all(),
            ];
        }

        // B. Cek kata kunci kategori atau niat pengecekan barang/harga/stok
        $categoryKeywordsMap = [
            'dresses' => ['dress', 'gaun', 'midi dress', 'maxi dress', 'slip dress', 'gown', 'dresses'],
            'blouses-tops' => ['blouse', 'top', 'tops', 'atasan', 'kemeja', 'shirt', 'tank', 'baju', 'kaos'],
            'outerwear-blazers' => ['blazer', 'outer', 'outerwear', 'coat', 'trench coat', 'jaket', 'cardigan', 'vest', 'rompi'],
            'pants-trousers' => ['celana', 'pants', 'trousers', 'kulot', 'culotte', 'palazzo', 'bawahan'],
            'skirts' => ['rok', 'skirt', 'skirts', 'midi skirt', 'maxi skirt', 'pleated'],
            'knitwear-sets' => ['knit', 'knitwear', 'rajut', 'co-ord', 'setelan', 'one set', 'set'],
        ];

        $matchedCategorySlug = null;
        foreach ($categoryKeywordsMap as $slug => $synonyms) {
            foreach ($synonyms as $syn) {
                if (str_contains($q, $syn)) {
                    $matchedCategorySlug = $slug;
                    break 2;
                }
            }
        }

        $isRecommendationIntent = $this->containsAny($q, [
            'rekomendasi', 'cocok', 'saran', 'pilihkan', 'carikan', 'kado', 'hadiah', 'bagus yang mana', 'tips', 'ide',
            'pas untuk', 'buat acara', 'kondangan', 'pesta', 'wisuda', 'lamaran', 'ulang tahun',
        ]);

        $isPriceOrCatalogIntent = $isRecommendationIntent || $matchedCategorySlug !== null || $this->containsAny($q, [
            'harga', 'price', 'berapa', 'cek barang', 'cek stok', 'stok', 'stock', 'katalog',
            'koleksi', 'produk', 'barang', 'termurah', 'termahal', 'best seller',
            'terlaris', 'promo', 'diskon', 'discount', 'ready',
        ]);

        if (! $isPriceOrCatalogIntent) {
            return null;
        }

        $query = Product::with('category')->where('is_active', true);
        $filterLabel = 'Koleksi Katalog PUTRIJALVA';

        if ($matchedCategorySlug !== null) {
            $categoryModel = Category::where('slug', $matchedCategorySlug)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
                $filterLabel = "Kategori {$categoryModel->name}";
            }
        }

        // Jika pelanggan meminta rekomendasi atau saran gaya, WAJIB hanya rekomendasikan produk yang READY STOCK (stok > 0)
        if ($isRecommendationIntent) {
            $query->where('stock', '>', 0);
        }

        // Cek filter batas harga (misal: "di bawah 30" atau "dibawah $25" atau "maksimal 50")
        if (preg_match('/(?:di\s*bawah|dibawah|under|kurang\s*dari|maksimal|max)\s*\$?\s*(\d+(?:\.\d+)?)/i', $q, $m)) {
            $maxPrice = (float) $m[1];
            $query->where('price', '<=', $maxPrice);
            $filterLabel .= ' (Di Bawah $ '.number_format($maxPrice, 2).')';
        }

        if (str_contains($q, 'termurah') || str_contains($q, 'paling murah')) {
            $query->orderBy('price', 'asc');
            $filterLabel .= ' — Urutan Termurah';
        } elseif (str_contains($q, 'termahal') || str_contains($q, 'mewah') || str_contains($q, 'premium')) {
            $query->orderBy('price', 'desc');
            $filterLabel .= ' — Koleksi Premium';
        } elseif (str_contains($q, 'diskon') || str_contains($q, 'promo') || str_contains($q, 'sale')) {
            $query->whereNotNull('original_price');
            $filterLabel .= ' — Sedang Promo / Diskon';
        } elseif ($isRecommendationIntent) {
            $query->orderByDesc('is_featured')->orderByDesc('sales_count');
        } else {
            $query->orderBy('sales_count', 'desc');
        }

        /** @var Collection<int, Product> $results */
        $results = $query->take(3)->get();
        if ($results->isEmpty()) {
            return [
                'reply' => "Saat ini belum ada produk yang sesuai dengan kriteria **{$filterLabel}**. Silakan lihat seluruh koleksi busana kami di halaman Shop atau hubungi kami untuk bantuan pencarian khusus.",
                'action_btn' => [
                    'label' => 'Buka Katalog Lengkap',
                    'url' => route('shop.index'),
                    'external' => false,
                ],
                'matched_products' => [],
                'is_recommendation' => $isRecommendationIntent,
            ];
        }

        if ($isRecommendationIntent) {
            $lines = ['Tentu Kak! Untuk melengkapi gaya Kakak agar semakin anggun dan memukau, berikut rekomendasi busana pilihan terfavorit di butik **PUTRIJALVA** yang siap kirim (*ready stock*):'];
            foreach ($results as $p) {
                $descClean = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $p->description)));
                $descShort = Str::limit($descClean, 80);
                $lines[] = "✨ **{$p->name}** — **{$p->formatted_price}**\n   _{$descShort}_";
            }
            $lines[] = "\nKoleksi di atas dibuat dengan material premium pilihan (Linen, Silk, & Katun Organik) yang adem dan berpotongan rapi, serta dilengkapi garansi tukar ukuran (size exchange). Apakah ada model yang paling sesuai dengan selera Kakak?";
        } else {
            $lines = ["Berikut pilihan koleksi busana untuk **{$filterLabel}** di butik PUTRIJALVA:"];
            foreach ($results as $p) {
                $stockBadge = $p->stock > 0 ? "Ready {$p->stock} pcs" : 'Habis';
                $promoInfo = ($p->original_price && (float) $p->original_price > (float) $p->price)
                    ? " *(dari {$p->formatted_original_price})*"
                    : '';
                $lines[] = "• **{$p->name}** — **{$p->formatted_price}**{$promoInfo} | {$stockBadge}";
            }
        }

        $shopUrl = $matchedCategorySlug
            ? route('shop.index', ['category' => $matchedCategorySlug])
            : route('shop.index');

        return [
            'reply' => implode("\n", $lines),
            'action_btn' => [
                'label' => $isRecommendationIntent ? 'Lihat Rekomendasi di Toko' : 'Lihat di Etalase Toko',
                'url' => $shopUrl,
                'external' => false,
            ],
            'matched_products' => $results->map(fn (Product $p): array => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'price' => $p->formatted_price,
                'stock' => $p->stock,
                'url' => route('shop.show', $p->slug),
            ])->values()->all(),
            'is_recommendation' => $isRecommendationIntent,
        ];
    }

    /**
     * Pengecekan nomor pesanan (SHP-... / ORD-...) dan nomor resi kurir dari database.
     *
     * @return ?array{reply: string, action_btn: array{label: string, url: string, external?: bool}}
     */
    protected function checkOrderAndTracking(string $rawMessage, string $q, ?User $user = null): ?array
    {
        $order = null;

        if (preg_match('/\b((?:SHP|ORD)-[A-Z0-9\-]+)\b/i', $rawMessage, $matches)) {
            $orderNum = strtoupper($matches[1]);
            $order = Order::where('order_number', $orderNum)->first();
        } elseif ($user && $this->containsAny($q, ['pesanan saya', 'order saya', 'status pesanan', 'cek resi', 'nomor resi', 'status order'])) {
            $order = $user->orders()->latest()->first();
        }

        if (! $order) {
            return null;
        }

        $resiInfo = $order->tracking_number
            ? "**{$order->tracking_number}**"
            : 'Belum diterbitkan (menunggu penyerahan kurir)';

        $reply = "Berikut status terkini untuk pesanan **{$order->order_number}** a.n. **{$order->customer_name}**:\n"
            ."• **Status Pesanan:** {$order->status->label()}\n"
            ."• **Status Pembayaran:** {$order->paymentStatusLabel()}\n"
            ."• **Total Tagihan:** **{$order->formatted_grand_total}**\n"
            ."• **Nomor Resi Kurir:** {$resiInfo}";

        return [
            'reply' => $reply,
            'action_btn' => [
                'label' => "Buka Detail Pesanan {$order->order_number}",
                'url' => route('order.show', $order->order_number),
                'external' => false,
            ],
        ];
    }

    protected function recordAiMessage(Inquiry $inquiry, string $message): void
    {
        $inquiry->messages()->create([
            'sender_type' => 'ai',
            'message' => $message,
            'created_at' => now(),
        ]);
    }

    /**
     * Panggil API LLM Eksternal (Google Gemini / OpenAI / 9Router / OpenRouter / Groq) apabila AI_CONCIERGE_API_KEY diisi di .env.
     * Jika tidak diisi atau koneksi gagal, otomatis kembali ke respons database lokal.
     */
    protected function callExternalAiApi(string $customerMessage, string $localContext, ?User $user = null, ?Inquiry $inquiry = null): ?string
    {
        $apiKey = trim((string) config('services.ai_concierge.api_key', ''));
        if ($apiKey === '') {
            return null;
        }

        $provider = strtolower((string) config('services.ai_concierge.provider', 'gemini'));
        $model = (string) config('services.ai_concierge.model', 'gemini-2.5-flash');
        $customEndpoint = rtrim(trim((string) config('services.ai_concierge.endpoint', '')), '/');

        // Deteksi otomatis jika menggunakan OpenAI-compatible router (seperti 9router, OpenRouter, LiteLLM, atau key berawalan sk-)
        $isOpenAiCompatible = $provider !== 'gemini'
            || str_starts_with($apiKey, 'sk-')
            || str_contains($customEndpoint, '/v1')
            || str_contains($customEndpoint, 'chat/completions')
            || str_contains($customEndpoint, '9router')
            || str_contains($customEndpoint, 'openrouter')
            || str_contains($model, '/');

        /** @var Collection<int, AiKnowledgeEntry> $boundaryEntries */
        $boundaryEntries = AiKnowledgeEntry::where('is_active', true)
            ->where('type', 'boundary')
            ->get(['title', 'content']);

        $boundaries = $boundaryEntries
            ->map(fn (AiKnowledgeEntry $b): string => "{$b->title}: {$b->content}")
            ->implode("\n- ");

        /** @var Collection<int, AiKnowledgeEntry> $knowledgeRuleEntries */
        $knowledgeRuleEntries = AiKnowledgeEntry::where('is_active', true)
            ->whereIn('type', ['knowledge', 'response_rule'])
            ->get(['title', 'content']);

        $knowledgeRules = $knowledgeRuleEntries
            ->map(fn (AiKnowledgeEntry $k): string => "{$k->title}: {$k->content}")
            ->implode("\n- ");

        /** @var Collection<int, Product> $allCatalogProducts */
        $allCatalogProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(5)
            ->get();

        $catalogSnapshot = $allCatalogProducts
            ->map(fn (Product $p): string => "- {$p->name}: {$p->formatted_price} (Stok: {$p->stock})")
            ->implode("\n");

        $customerName = $user?->name ?? $inquiry?->name ?? 'Pelanggan';
        $isEn = app()->getLocale() === 'en';
        $langInstruction = $isEn
            ? 'Jawab dalam Bahasa Inggris yang elegan dan natural (Respond in refined, concise English).'
            : 'Jawab dalam Bahasa Indonesia yang ramah dan elegan (atau dalam Bahasa Inggris jika pelanggan bertanya dalam Bahasa Inggris).';

        $systemPrompt = "Anda adalah Aria, asisten konsultan butik busana wanita & ready-to-wear PUTRIJALVA (dress, atasan/blouse, blazer, celana, rok, dan setelan rajut).\n"
            ."Jawab sapaan dan pertanyaan pelanggan seputar rekomendasi busana, ukuran (size chart), bahan kain premium, dan bantuan belanja.\n"
            ."Pedoman Bahasa: {$langInstruction}\n\n"
            ."Koleksi Utama:\n{$catalogSnapshot}\n\n"
            ."Aturan:\n"
            ."1. Jawab singkat, hangat, berkelas, dan padat (maksimal 2-3 kalimat).\n"
            ."2. Jika pelanggan hanya menyapa (halo/hai/hello/hi), balas dengan sapaan hangat dan tawarkan bantuan untuk memilih busana yang pas atau mengecek pesanan.\n"
            .'3. Jika pelanggan membutuhkan bantuan pesanan atau komplain, arahkan untuk menghubungi Customer Service kami.';

        try {
            $httpClient = Http::timeout(12);
            if (app()->environment('local', 'testing')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            if (! $isOpenAiCompatible) {
                $url = $customEndpoint !== ''
                    ? $customEndpoint
                    : "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = $httpClient->post($url, [
                    'system_instruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => "Pelanggan ({$customerName}) bertanya: {$customerMessage}"]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 180,
                    ],
                ]);

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');
                    if (is_string($text) && trim($text) !== '') {
                        return trim($text);
                    }
                }

                return null;
            }

            // Format OpenAI Chat Completions (OpenAI, 9Router, OpenRouter, Groq, DeepSeek)
            if ($customEndpoint !== '') {
                if (str_ends_with($customEndpoint, '/chat/completions')) {
                    $url = $customEndpoint;
                } elseif (str_ends_with($customEndpoint, '/v1')) {
                    $url = $customEndpoint.'/chat/completions';
                } else {
                    $url = $customEndpoint.'/v1/chat/completions';
                }
            } else {
                $url = $provider === 'groq'
                    ? 'https://api.groq.com/openai/v1/chat/completions'
                    : 'https://api.openai.com/v1/chat/completions';
            }

            $messagesPayload = [
                ['role' => 'system', 'content' => $systemPrompt],
            ];

            if ($inquiry) {
                $recentHistory = $inquiry->messages()
                    ->latest('id')
                    ->take(2)
                    ->get()
                    ->reverse();

                foreach ($recentHistory as $histMsg) {
                    if ($histMsg->message === $customerMessage && $histMsg->sender_type === 'customer') {
                        continue;
                    }
                    $messagesPayload[] = [
                        'role' => $histMsg->sender_type === 'customer' ? 'user' : 'assistant',
                        'content' => (string) $histMsg->message,
                    ];
                }
            }

            $messagesPayload[] = ['role' => 'user', 'content' => $customerMessage];

            $response = $httpClient->withToken($apiKey)->post($url, [
                'model' => $model,
                'messages' => $messagesPayload,
                'temperature' => 0.5,
                'max_tokens' => 180,
                'stream' => false,
            ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content');
                if (is_string($text) && trim($text) !== '') {
                    return trim($text);
                }

                // Dukungan penuh untuk router AI (seperti 9router) yang mengembalikan format SSE stream (data: {"choices":[{"delta":{"content":"..."}}]})
                $rawBody = $response->body();
                if (str_contains($rawBody, 'data:')) {
                    $assembled = '';
                    $lines = preg_split('/\r\n|\r|\n/', $rawBody) ?: [];
                    foreach ($lines as $line) {
                        $trimmedLine = trim($line);
                        if (! str_starts_with($trimmedLine, 'data:')) {
                            continue;
                        }
                        $jsonPart = trim(substr($trimmedLine, 5));
                        if ($jsonPart === '' || $jsonPart === '[DONE]') {
                            continue;
                        }
                        $decoded = json_decode($jsonPart, true);
                        if (is_array($decoded)) {
                            $deltaContent = $decoded['choices'][0]['delta']['content']
                                ?? $decoded['choices'][0]['message']['content']
                                ?? null;
                            if (is_string($deltaContent)) {
                                $assembled .= $deltaContent;
                            }
                        }
                    }

                    if (trim($assembled) !== '') {
                        return trim($assembled);
                    }
                }
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    /**
     * @param  array<int, string>  $needles
     */
    protected function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
