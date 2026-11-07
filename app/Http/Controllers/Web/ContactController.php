<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        $inquiry = Inquiry::create([
            'user_id' => auth()->id(),
            'ticket_number' => 'TCK-' . strtoupper(uniqid()),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'Pertanyaan Layanan Toko',
            'channel' => 'contact_form',
            'status' => 'unread',
        ]);

        $inquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Terima kasih, pesan Anda telah terkirim ke tim Concierge kami.');
    }
}
