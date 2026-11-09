<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Support\Facades\DB;

class InquiryService
{
    /**
     * Membuat tiket pertanyaan pelanggan baru beserta pesan awal secara atomik.
     *
     * @param  array<string, mixed>  $data
     */
    public function createInquiry(array $data, ?int $userId = null): Inquiry
    {
        return DB::transaction(function () use ($data, $userId): Inquiry {
            $inquiry = Inquiry::create([
                'user_id' => $userId,
                'ticket_number' => 'TCK-'.strtoupper(uniqid()),
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => ! empty($data['subject']) ? $data['subject'] : 'Pertanyaan Layanan Toko',
                'channel' => $data['channel'] ?? 'contact_form',
                'handling_mode' => $data['handling_mode'] ?? 'ai',
                'status' => InquiryStatus::UNREAD,
            ]);

            $inquiry->messages()->create([
                'sender_type' => 'customer',
                'message' => $data['message'],
                'created_at' => now(),
            ]);

            return $inquiry;
        });
    }

    /**
     * Menyimpan balasan admin pada tiket pertanyaan pelanggan secara atomik.
     */
    public function replyToInquiry(Inquiry $inquiry, string $message): Inquiry
    {
        return DB::transaction(function () use ($inquiry, $message): Inquiry {
            $inquiry->messages()->create([
                'sender_type' => 'admin',
                'message' => $message,
                'created_at' => now(),
            ]);

            $inquiry->update([
                'handling_mode' => 'human',
                'status' => InquiryStatus::RESPONDED,
            ]);

            return $inquiry;
        });
    }
}
