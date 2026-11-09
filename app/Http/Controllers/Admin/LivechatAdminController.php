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
use App\Services\AiConciergeService;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LivechatAdminController extends Controller
{
    public function __construct(
        protected InquiryService $inquiryService,
        protected AiConciergeService $aiConciergeService
    ) {}

    public function index(): View
    {
        $inquiries = Inquiry::with('messages')->latest()->get();
        $unreadCount = Inquiry::where('status', InquiryStatus::UNREAD)->count();
        $knowledgeEntries = AiKnowledgeEntry::latest()->get();

        return view('admin.livechat', compact('inquiries', 'unreadCount', 'knowledgeEntries'));
    }

    public function reply(ReplyInquiryRequest $request, Inquiry $inquiry): RedirectResponse|JsonResponse
    {
        $updatedInquiry = $this->inquiryService->replyToInquiry($inquiry, $request->validated('message'));
        $latestMessage = $updatedInquiry->messages()->latest('id')->first();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan berhasil dikirim (Mode Admin Manusia aktif).',
                'data' => [
                    'id' => $latestMessage?->id,
                    'sender_type' => $latestMessage?->sender_type,
                    'message' => $latestMessage?->message,
                    'created_at' => $latestMessage?->created_at?->format('H:i') ?? now()->format('H:i'),
                    'created_at_full' => $latestMessage?->created_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                    'inquiry_status' => $updatedInquiry->status->value,
                    'inquiry_status_label' => $updatedInquiry->status->label(),
                    'handling_mode' => $updatedInquiry->handling_mode,
                ],
            ]);
        }

        return back()->with('success', "Balasan terkirim ke tiket {$inquiry->ticket_number}.");
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
