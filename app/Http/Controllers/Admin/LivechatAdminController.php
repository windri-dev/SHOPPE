<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReplyInquiryRequest;
use App\Http\Requests\Admin\StoreAiKnowledgeRequest;
use App\Http\Requests\Admin\ToggleInquiryModeRequest;
use App\Http\Requests\Admin\UpdateInquiryStatusRequest;
use App\Models\AiKnowledgeEntry;
use App\Models\Inquiry;
use App\Models\Order;
use App\Services\AiConciergeService;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LivechatAdminController extends Controller
{
    public function __construct(
        protected InquiryService $inquiryService,
        protected AiConciergeService $aiConciergeService
    ) {}

    public function index(): View
    {
        $inquiries = Inquiry::with('messages')
            ->select('inquiries.*')
            ->selectSub(function ($query) {
                $query->from('inquiry_messages')
                    ->whereColumn('inquiry_messages.inquiry_id', 'inquiries.id')
                    ->selectRaw('max(created_at)');
            }, 'latest_message_at')
            ->orderByRaw('COALESCE(latest_message_at, inquiries.created_at) DESC')
            ->get();
        $unreadCount = Inquiry::where('status', InquiryStatus::UNREAD)->count();
        $knowledgeEntries = AiKnowledgeEntry::latest()->get();

        return view('admin.livechat', compact('inquiries', 'unreadCount', 'knowledgeEntries'));
    }

    public function inquiries(): JsonResponse
    {
        $inquiries = Inquiry::with('messages')
            ->select('inquiries.*')
            ->selectSub(function ($query) {
                $query->from('inquiry_messages')
                    ->whereColumn('inquiry_messages.inquiry_id', 'inquiries.id')
                    ->selectRaw('max(created_at)');
            }, 'latest_message_at')
            ->orderByRaw('COALESCE(latest_message_at, inquiries.created_at) DESC')
            ->get()
            ->map(function ($inq) {
                $lastMsg = $inq->messages->last();
                $lastCustomerMsg = $inq->messages->whereIn('sender_type', ['customer', 'user'])->last();
                $preview = ($lastMsg && $lastMsg->sender_type === 'admin')
                    ? 'Admin: ' . $lastMsg->message
                    : ($lastCustomerMsg ? $lastCustomerMsg->message : ($lastMsg ? $lastMsg->message : 'Belum ada pesan'));
                $timeObj = $lastMsg ? $lastMsg->created_at : $inq->created_at;
                $formattedTime = $timeObj ? ($timeObj instanceof \Carbon\Carbon ? $timeObj->format('H:i') : \Carbon\Carbon::parse($timeObj)->format('H:i')) : '';

                return [
                    'id' => $inq->id,
                    'ticket_number' => $inq->ticket_number,
                    'name' => $inq->name,
                    'email' => $inq->email,
                    'phone' => $inq->phone ?? '',
                    'channel' => $inq->channel,
                    'status' => $inq->status->value,
                    'status_label' => $inq->status->label(),
                    'handling_mode' => $inq->handling_mode ?? 'ai',
                    'subject' => $inq->subject,
                    'preview' => $preview,
                    'last_sender' => $lastMsg?->sender_type ?? '',
                    'last_time' => $formattedTime,
                    'messages_count' => $inq->messages->count(),
                    'messages' => $inq->messages->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'sender_type' => $m->sender_type,
                            'message' => $m->message,
                            'created_at' => $m->created_at?->format('H:i') ?? '',
                            'created_at_full' => $m->created_at?->format('d M Y H:i') ?? '',
                        ];
                    }),
                    'reply_url' => route('admin.inquiries.reply', $inq),
                    'status_url' => route('admin.inquiries.status', $inq),
                    'mode_url' => route('admin.inquiries.mode', $inq),
                ];
            });

        $unreadCount = Inquiry::where('status', InquiryStatus::UNREAD)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'inquiries' => $inquiries,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    public function messages(Inquiry $inquiry): JsonResponse
    {
        $messages = $inquiry->messages()->orderBy('created_at', 'asc')->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'sender_type' => $m->sender_type,
                'message' => $m->message,
                'created_at' => $m->created_at?->format('H:i') ?? '',
                'created_at_full' => $m->created_at?->format('d M Y H:i') ?? '',
            ];
        });

        // Find customer orders by user_id or email
        $orders = collect();
        if ($inquiry->user_id) {
            $orders = Order::where('user_id', $inquiry->user_id)->latest()->take(3)->get();
        } elseif ($inquiry->email) {
            $orders = Order::where('customer_email', $inquiry->email)->latest()->take(3)->get();
        }

        $formattedOrders = $orders->map(function ($o) {
            return [
                'order_number' => $o->order_number,
                'status' => $o->status->value,
                'status_label' => $o->status->label(),
                'grand_total_formatted' => $o->formatted_grand_total,
                'items_count' => $o->items->count(),
                'created_at' => $o->created_at?->format('d M Y') ?? '',
                'url' => route('admin.orders.index', ['search' => $o->order_number]),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $inquiry->id,
                'ticket_number' => $inquiry->ticket_number,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'subject' => $inquiry->subject,
                'channel' => $inquiry->channel,
                'status' => $inquiry->status->value,
                'status_label' => $inquiry->status->label(),
                'handling_mode' => $inquiry->handling_mode,
                'messages' => $messages,
                'recent_orders' => $formattedOrders,
            ],
        ]);
    }

    public function reply(ReplyInquiryRequest $request, Inquiry $inquiry): RedirectResponse|JsonResponse
    {
        $updatedInquiry = $this->inquiryService->replyToInquiry($inquiry, $request->validated('message'));
        $latestMessage = $updatedInquiry->messages()->latest('id')->first();

        $savedKnowledge = null;
        if ($request->boolean('save_as_knowledge')) {
            $lastCustomerMsg = $inquiry->messages()
                ->whereIn('sender_type', ['customer', 'user'])
                ->latest('id')
                ->first();
            $questionText = $lastCustomerMsg?->message ?? $inquiry->subject ?? 'Pertanyaan Pelanggan';

            $title = trim((string) $request->input('knowledge_title', ''));
            if ($title === '') {
                $title = 'FAQ: '.Str::limit(trim($questionText), 70, '');
            }

            $keywords = trim((string) $request->input('knowledge_keywords', ''));
            if ($keywords === '') {
                $keywords = $this->aiConciergeService->extractKeywordsFromText($questionText);
            }

            $savedKnowledge = AiKnowledgeEntry::create([
                'type' => 'knowledge',
                'title' => $title,
                'keywords' => $keywords,
                'content' => $request->validated('message'),
                'requires_human_handoff' => false,
                'is_active' => true,
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $savedKnowledge
                    ? 'Balasan berhasil dikirim & disimpan ke Knowledge Base!'
                    : 'Balasan berhasil dikirim (Mode Admin Manusia aktif).',
                'data' => [
                    'id' => $latestMessage?->id,
                    'sender_type' => $latestMessage?->sender_type,
                    'message' => $latestMessage?->message,
                    'created_at' => $latestMessage?->created_at?->format('H:i') ?? now()->format('H:i'),
                    'created_at_full' => $latestMessage?->created_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                    'inquiry_status' => $updatedInquiry->status->value,
                    'inquiry_status_label' => $updatedInquiry->status->label(),
                    'handling_mode' => $updatedInquiry->handling_mode,
                    'knowledge_saved' => $savedKnowledge !== null,
                    'knowledge_entry' => $savedKnowledge,
                ],
            ]);
        }

        $flashMessage = $savedKnowledge
            ? "Balasan terkirim ke tiket {$inquiry->ticket_number} dan disimpan ke Knowledge Base."
            : "Balasan terkirim ke tiket {$inquiry->ticket_number}.";

        return back()->with('success', $flashMessage);
    }

    public function updateStatus(UpdateInquiryStatusRequest $request, Inquiry $inquiry): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $inquiry->update([
            'status' => InquiryStatus::from($validated['status']),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status tiket diperbarui menjadi {$inquiry->status->label()}.",
                'data' => [
                    'ticket_number' => $inquiry->ticket_number,
                    'status' => $inquiry->status->value,
                    'status_label' => $inquiry->status->label(),
                ],
            ]);
        }

        return back()->with('success', "Status tiket {$inquiry->ticket_number} diperbarui.");
    }

    public function toggleMode(ToggleInquiryModeRequest $request, Inquiry $inquiry): RedirectResponse|JsonResponse
    {
        $mode = $request->validated('handling_mode');
        $inquiry->update(['handling_mode' => $mode]);

        $modeLabel = $mode === 'human' ? 'Admin Manusia (Manual)' : 'AI Stylist Aria (Otomatis)';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Mode penanganan tiket diubah ke {$modeLabel}.",
                'data' => [
                    'ticket_number' => $inquiry->ticket_number,
                    'handling_mode' => $inquiry->handling_mode,
                    'handling_mode_label' => $modeLabel,
                ],
            ]);
        }

        return back()->with('success', "Mode penanganan tiket {$inquiry->ticket_number} diubah ke {$modeLabel}.");
    }

    public function aiDraft(Inquiry $inquiry): JsonResponse
    {
        $draft = $this->aiConciergeService->suggestDraftForAdmin($inquiry);

        return response()->json([
            'success' => true,
            'data' => [
                'draft' => $draft,
            ],
        ]);
    }

    public function storeKnowledge(StoreAiKnowledgeRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $entry = AiKnowledgeEntry::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'keywords' => $validated['keywords'] ?? null,
            'content' => $validated['content'],
            'requires_human_handoff' => $request->boolean('requires_human_handoff'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Knowledge & Aturan AI '{$entry->title}' berhasil ditambahkan.",
                'data' => $entry,
            ]);
        }

        return back()->with('success', "Knowledge & Aturan AI '{$entry->title}' berhasil ditambahkan.");
    }

    public function updateKnowledge(StoreAiKnowledgeRequest $request, AiKnowledgeEntry $entry): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $entry->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'keywords' => $validated['keywords'] ?? null,
            'content' => $validated['content'],
            'requires_human_handoff' => $request->boolean('requires_human_handoff'),
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Knowledge & Aturan AI '{$entry->title}' berhasil diperbarui.",
                'data' => $entry,
            ]);
        }

        return back()->with('success', "Knowledge & Aturan AI '{$entry->title}' berhasil diperbarui.");
    }

    public function destroyKnowledge(AiKnowledgeEntry $entry): RedirectResponse
    {
        $title = $entry->title;
        $entry->delete();

        return back()->with('success', "Aturan AI '{$title}' telah dihapus.");
    }
}
