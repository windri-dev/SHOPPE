<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductTranslationService
{
    /**
     * Terjemahkan nama dan deskripsi produk dari Bahasa Indonesia ke Bahasa Inggris dengan AI.
     *
     * @return array{name_en: string, description_en: string}
     */
    public function translateToEnglish(string $name, ?string $description = null): array
    {
        $desc = trim((string) $description);

        $aiResult = $this->callTranslationAi($name, $desc);
        if ($aiResult !== null && ! empty($aiResult['name_en'])) {
            return [
                'name_en' => trim($aiResult['name_en']),
                'description_en' => trim($aiResult['description_en'] ?? $desc),
            ];
        }

        // Fallback anggun jika API AI sedang offline
        return $this->fallbackTranslation($name, $desc);
    }

    /**
     * @return ?array{name_en: string, description_en: string}
     */
    protected function callTranslationAi(string $name, string $description): ?array
    {
        $apiKey = trim((string) config('services.ai_concierge.api_key', ''));
        if ($apiKey === '') {
            return null;
        }

        $provider = strtolower((string) config('services.ai_concierge.provider', 'gemini'));
        $model = (string) config('services.ai_concierge.model', 'ag/gemini-3.8-flash-low');
        $customEndpoint = rtrim(trim((string) config('services.ai_concierge.endpoint', '')), '/');

        $systemPrompt = "You are a professional luxury fashion copywriter and translator for the contemporary women's apparel boutique PUTRI.J.\n"
            ."Translate Indonesian fashion product names and descriptions into elegant, polished, and sophisticated English e-commerce copy.\n"
            ."Rules:\n"
            ."1. Maintain upscale boutique tone (e.g., 'French Washed Linen', 'Mulberry Silk', 'Tailored Blazer', 'Draped Midi Dress').\n"
            .'2. Output MUST be ONLY a valid raw JSON object without any Markdown formatting or backticks: {"name_en": "...", "description_en": "..."}';

        $userPrompt = "Translate this product:\nName (ID): {$name}\nDescription (ID): {$description}";

        try {
            $httpClient = Http::timeout(10);
            if (app()->environment('local', 'testing')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            if ($customEndpoint !== '') {
                if (str_ends_with($customEndpoint, '/chat/completions')) {
                    $url = $customEndpoint;
                } elseif (str_ends_with($customEndpoint, '/v1')) {
                    $url = $customEndpoint.'/chat/completions';
                } else {
                    $url = $customEndpoint.'/v1/chat/completions';
                }
            } else {
                $url = 'https://api.openai.com/v1/chat/completions';
            }

            $response = $httpClient->withToken($apiKey)->post($url, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 300,
                'stream' => false,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if (is_string($content) && trim($content) !== '') {
                    $cleanJson = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content)) ?? '');
                    $parsed = json_decode($cleanJson, true);
                    if (is_array($parsed) && ! empty($parsed['name_en'])) {
                        return [
                            'name_en' => (string) $parsed['name_en'],
                            'description_en' => (string) ($parsed['description_en'] ?? $description),
                        ];
                    }
                }
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    /**
     * @return array{name_en: string, description_en: string}
     */
    protected function fallbackTranslation(string $name, string $description): array
    {
        $dictionary = [
            'gaun' => 'dress',
            'baju' => 'apparel',
            'atasan' => 'top',
            'kemeja' => 'shirt',
            'celana' => 'trousers',
            'rok' => 'skirt',
            'setelan' => 'set',
            'rajut' => 'knitwear',
            'panjang' => 'maxi',
            'pendek' => 'mini',
            'katun' => 'cotton',
            'sutra' => 'silk',
            'adem' => 'breathable',
            'lembut' => 'soft',
            'nyaman' => 'comfortable',
            'elegan' => 'elegant',
            'mewah' => 'luxury',
            'anggun' => 'graceful',
            'motif' => 'pattern',
            'polos' => 'solid',
        ];

        $words = preg_split('/\s+/', $name) ?: [];
        $translatedWords = [];
        foreach ($words as $w) {
            $lower = strtolower($w);
            $translatedWords[] = $dictionary[$lower] ?? $w;
        }

        $translatedName = Str::title(implode(' ', $translatedWords));

        return [
            'name_en' => $translatedName !== '' ? $translatedName : $name,
            'description_en' => $description !== '' ? $description : '',
        ];
    }
}
