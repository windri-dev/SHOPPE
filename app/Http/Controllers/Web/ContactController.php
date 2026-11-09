<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\SubmitInquiryRequest;
use App\Http\Requests\Contact\SubmitLivechatMessageRequest;
use App\Models\Inquiry;
use App\Services\AiConciergeService;
use App\Services\InquiryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        protected InquiryService $inquiryService,
        protected AiConciergeService $aiConciergeService
    ) {}

    public function index(): View
    {
        return view('pages.contact');
    }

    public function submit(SubmitInquiryRequest $request): RedirectResponse
    {
        $userId = auth()->id();
        $this->inquiryService->createInquiry(
            $request->validated(),
            $userId ? (int) $userId : null
        );

        return back()->with('success', 'Terima kasih, pesan Anda telah terkirim ke tim Concierge kami.');
    }

    public function livechatSubmit(SubmitLivechatMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = auth()->user();

        $name = $user ? $user->name : (! empty($validated['name']) ? $validated['name'] : 'Tamu LiveChat');
        $email = $user ? $user->email : (! empty($validated['email']) ? $validated['email'] : 'guest@shoppe.com');
        $ticketNumber = $validated['ticket_number'] ?? null;
        $requestHuman = (bool) ($validated['request_human'] ?? false);

        $inquiry = null;
        if ($ticketNumber) {
            $inquiry = Inquiry::where('ticket_number', $ticketNumber)->first();
        }

        if ($inquiry) {
            $inquiry->messages()->create([
                'sender_type' => 'customer',
                'message' => $validated['message'],
                'created_at' => now(),
            ]);
            $inquiry->update(['status' => InquiryStatus::UNREAD]);
        } else {
            $inquiry = $this->inquiryService->createInquiry([
                'name' => $name,
                'email' => $email,
                'phone' => $user?->phone,
                'subject' => 'Live Chat Concierge Web',
                'channel' => 'livechat',
                'handling_mode' => $requestHuman ? 'human' : 'ai',
                'message' => $validated['message'],
            ], $user ? (int) $user->id : null);
        }

        if ($requestHuman) {
            $handoffMessage = 'Baik Kak, percakapan ini telah saya alihkan ke **Staf Admin Concierge (Manusia)** kami. Mohon tunggu sebentar, tim kami akan segera membalas pesan Anda secara langsung di ruang obrolan ini.';
            $inquiry->update([
                'handling_mode' => 'human',
                'status' => InquiryStatus::UNREAD,
            ]);
            $inquiry->messages()->create([
                'sender_type' => 'ai',
                'message' => $handoffMessage,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_number' => $inquiry->ticket_number,
                    'message' => $validated['message'],
                    'handled_by' => 'handoff',
                    'handling_mode' => 'human',
                    'ai_reply' => $handoffMessage,
                    'action_btn' => null,
                    'matched_products' => [],
                    'rule_applied' => 'Eskalasi Manual ke Admin Manusia',
                ],
            ]);
        }

        $aiResult = $this->aiConciergeService->handleCustomerMessage(
            $validated['message'],
            $inquiry,
            $user
        );

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $inquiry->ticket_number,
                'message' => $validated['message'],
                'handled_by' => $aiResult['handled_by'],
                'handling_mode' => $aiResult['handling_mode'],
                'ai_reply' => $aiResult['reply'],
                'action_btn' => $aiResult['action_btn'],
                'matched_products' => $aiResult['matched_products'] ?? [],
                'rule_applied' => $aiResult['rule_applied'] ?? null,
            ],
        ]);
    }

    public function livechatThread(string $ticketNumber): JsonResponse
    {
        $inquiry = Inquiry::with('messages')->where('ticket_number', $ticketNumber)->first();

        if (! $inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $inquiry->ticket_number,
                'handling_mode' => $inquiry->handling_mode,
                'status' => $inquiry->status->value,
                'messages' => $inquiry->messages->map(fn ($msg): array => [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'message' => $msg->message,
                    'time' => $msg->created_at?->format('H:i') ?? now()->format('H:i'),
                ])->values()->all(),
            ],
        ]);
    }
}
