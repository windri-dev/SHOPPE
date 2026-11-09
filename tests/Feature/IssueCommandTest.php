<?php

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueCommandTest extends TestCase
{
    use RefreshDatabase;

    protected Inquiry $testInquiry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testInquiry = Inquiry::create([
            'ticket_number' => 'INQ-20261107-TEST',
            'name' => 'Adinda Permata',
            'email' => 'adinda@example.com',
            'phone' => '+62 812 9988 7766',
            'subject' => 'Pertanyaan mengenai ukuran cincin Lira',
            'channel' => 'web',
            'status' => InquiryStatus::UNREAD,
        ]);

        $this->testInquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => 'Halo min, apakah cincin Lira tersedia dalam ukuran 7?',
            'created_at' => now(),
        ]);
    }

    /**
     * Test list issues menampilkan daftar tiket.
     */
    public function test_cli_can_list_issues(): void
    {
        $this->artisan('issue list')
            ->expectsOutputToContain('DAFTAR ISSUES & TIKET PELANGGAN')
            ->assertExitCode(0);
    }

    /**
     * Test show issue menampilkan rincian dan riwayat pesan.
     */
    public function test_cli_can_show_issue_details(): void
    {
        $this->artisan('issue show INQ-20261107-TEST')
            ->expectsOutputToContain('DETAIL ISSUE / TIKET: INQ-20261107-TEST')
            ->expectsOutputToContain('Adinda Permata')
            ->expectsOutputToContain('Halo min, apakah cincin Lira tersedia dalam ukuran 7?')
            ->assertExitCode(0);
    }

    /**
     * Test reply issue mengirimkan balasan admin dan mengubah status menjadi responded.
     */
    public function test_cli_can_reply_to_issue(): void
    {
        $this->artisan('issue reply INQ-20261107-TEST --message="Halo Adinda, ukuran 7 tersedia dan siap dikirim!"')
            ->expectsOutputToContain('Balasan berhasil dikirim ke tiket INQ-20261107-TEST')
            ->assertExitCode(0);

        $this->testInquiry->refresh();
        $this->assertEquals(InquiryStatus::RESPONDED, $this->testInquiry->status);

        $this->assertDatabaseHas('inquiry_messages', [
            'inquiry_id' => $this->testInquiry->id,
            'sender_type' => 'admin',
            'message' => 'Halo Adinda, ukuran 7 tersedia dan siap dikirim!',
        ]);
    }

    /**
     * Test resolve issue menandai tiket selesai.
     */
    public function test_cli_can_resolve_issue(): void
    {
        $this->artisan('issue resolve INQ-20261107-TEST')
            ->expectsOutputToContain('berhasil diselesaikan (RESOLVED)')
            ->assertExitCode(0);

        $this->testInquiry->refresh();
        $this->assertEquals(InquiryStatus::RESOLVED, $this->testInquiry->status);
    }

    /**
     * Test create issue membuat tiket baru langsung dari CLI.
     */
    public function test_cli_can_create_new_issue(): void
    {
        $this->artisan('issue create --name="Rian Santoso" --email="rian@example.com" --subject="Keluhan Keterlambatan Pengiriman" --message="Pesanan #ORD-990 belum sampai"')
            ->expectsOutputToContain('Issue/tiket baru berhasil dibuat')
            ->assertExitCode(0);

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Rian Santoso',
            'email' => 'rian@example.com',
            'subject' => 'Keluhan Keterlambatan Pengiriman',
            'status' => 'unread',
        ]);
    }

    /**
     * Test stats menampilkan ringkasan metrik tiket.
     */
    public function test_cli_can_show_issue_stats(): void
    {
        $this->artisan('issue stats')
            ->expectsOutputToContain('STATISTIK ISSUES & TIKET CUSTOMER CARE')
            ->expectsOutputToContain('Total Tiket Masuk')
            ->assertExitCode(0);
    }

    /**
     * Test unknown action mengembalikan error dan daftar bantuan.
     */
    public function test_cli_unknown_action_shows_help(): void
    {
        $this->artisan('issue foo')
            ->expectsOutputToContain("Aksi 'foo' tidak dikenali")
            ->expectsOutputToContain('php artisan issue list')
            ->assertExitCode(1);
    }
}
