<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class IssueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'issue 
                            {action=list : Aksi yang dijalankan (list, show, reply, resolve, create, stats, github)}
                            {ticket? : Nomor tiket atau ID inquiry}
                            {--status= : Filter status (unread, responded, resolved)}
                            {--message= : Pesan balasan saat membalas tiket}
                            {--name= : Nama pelapor saat membuat issue baru}
                            {--email= : Email pelapor saat membuat issue baru}
                            {--subject= : Subjek issue baru}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CLI Manajemen Issues / Tiket Pelanggan dan GitHub Issues untuk PUTRIJALVA';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = strtolower($this->argument('action'));

        return match ($action) {
            'list', 'all' => $this->listIssues(),
            'show', 'view', 'detail' => $this->showIssue(),
            'reply', 'respond' => $this->replyIssue(),
            'resolve', 'close' => $this->resolveIssue(),
            'create', 'new' => $this->createIssue(),
            'stats', 'summary' => $this->showStats(),
            'github', 'gh' => $this->manageGitHubIssues(),
            default => $this->unknownAction($action),
        };
    }

    /**
     * Tampilkan daftar issue / tiket pelanggan.
     */
    protected function listIssues(): int
    {
        $statusFilter = $this->option('status');

        $query = Inquiry::with('messages')->latest();

        if ($statusFilter) {
            $query->where('status', strtolower($statusFilter));
        }

        $issues = $query->get();

        if ($issues->isEmpty()) {
            $this->info('Tidak ada issue/tiket pelanggan yang ditemukan.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("=== DAFTAR ISSUES & TIKET PELANGGAN (Total: {$issues->count()}) ===");

        $tableData = $issues->map(function (Inquiry $issue) {
            $statusLabel = match ($issue->status) {
                InquiryStatus::UNREAD => '<fg=red;options=bold>UNREAD</>',
                InquiryStatus::RESPONDED => '<fg=yellow>RESPONDED</>',
                InquiryStatus::RESOLVED => '<fg=green>RESOLVED</>',
                default => (string) ($issue->status->value ?? $issue->status),
            };

            return [
                'ID' => $issue->id,
                'Ticket #' => $issue->ticket_number,
                'Pelanggan' => Str::limit($issue->name, 20),
                'Email' => Str::limit($issue->email, 25),
                'Subjek' => Str::limit($issue->subject, 30),
                'Saluran' => strtoupper($issue->channel ?? 'WEB'),
                'Pesan' => $issue->messages->count(),
                'Status' => $statusLabel,
                'Waktu' => $issue->created_at?->format('d/m/Y H:i') ?? '-',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Ticket #', 'Pelanggan', 'Email', 'Subjek', 'Saluran', 'Pesan', 'Status', 'Waktu'],
            $tableData
        );

        $this->line('Gunakan <fg=cyan>php artisan issue show <ticket_number></> untuk membaca detail & percakapan.');
        $this->line('Gunakan <fg=cyan>php artisan issue reply <ticket_number></> untuk mengirim balasan.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Tampilkan detail dan riwayat percakapan dari tiket issue.
     */
    protected function showIssue(): int
    {
        $ticket = $this->argument('ticket') ?: $this->ask('Masukkan Nomor Tiket atau ID Issue:');

        if (! $ticket) {
            $this->error('Nomor tiket wajib diisi.');

            return self::FAILURE;
        }

        $issue = $this->findInquiry($ticket);

        if (! $issue) {
            $this->error("Tiket issue '{$ticket}' tidak ditemukan.");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('==============================================================');
        $this->info("DETAIL ISSUE / TIKET: {$issue->ticket_number}");
        $this->info('==============================================================');
        $this->line("<fg=gray>ID Database   :</> {$issue->id}");
        $this->line("<fg=gray>Nama Pelapor  :</> <fg=yellow;options=bold>{$issue->name}</>");
        $this->line("<fg=gray>Email         :</> {$issue->email}");
        $this->line('<fg=gray>Telepon       :</> '.($issue->phone ?: '-'));
        $this->line("<fg=gray>Subjek        :</> <options=bold>{$issue->subject}</>");
        $this->line('<fg=gray>Saluran       :</> '.strtoupper($issue->channel ?? 'WEB'));
        $this->line('<fg=gray>Status        :</> '.$this->formatStatusBadge($issue->status));
        $this->line('<fg=gray>Dibuat Pada   :</> '.($issue->created_at?->format('d F Y, H:i:s') ?? '-'));
        $this->newLine();

        $this->info("--- RIWAYAT PERCAKAPAN ({$issue->messages->count()} pesan) ---");

        if ($issue->messages->isEmpty()) {
            $this->line('<fg=gray>(Belum ada pesan tercatat)</>');
        } else {
            foreach ($issue->messages as $index => $msg) {
                $isCustomer = ($msg->sender_type === 'customer' || $msg->sender_type === 'user');
                $senderBadge = $isCustomer
                    ? '<fg=cyan;options=bold>[PELANGGAN]</>'
                    : '<fg=green;options=bold>[ADMIN STORE]</>';

                $time = $msg->created_at ? Carbon::parse($msg->created_at)->format('d M Y H:i') : '-';

                $this->line("{$senderBadge} <fg=gray>({$time})</>:");
                $this->line("  {$msg->message}");
                $this->newLine();
            }
        }

        return self::SUCCESS;
    }

    /**
     * Balas pesan tiket issue langsung dari terminal.
     */
    protected function replyIssue(): int
    {
        $ticket = $this->argument('ticket') ?: $this->ask('Masukkan Nomor Tiket atau ID Issue:');

        if (! $ticket) {
            $this->error('Nomor tiket wajib diisi.');

            return self::FAILURE;
        }

        $issue = $this->findInquiry($ticket);

        if (! $issue) {
            $this->error("Tiket issue '{$ticket}' tidak ditemukan.");

            return self::FAILURE;
        }

        $replyMessage = $this->option('message') ?: $this->ask("Tulis balasan untuk {$issue->name} ({$issue->ticket_number}):");

        if (blank($replyMessage)) {
            $this->error('Pesan balasan tidak boleh kosong.');

            return self::FAILURE;
        }

        $issue->messages()->create([
            'sender_type' => 'admin',
            'message' => $replyMessage,
            'created_at' => now(),
        ]);

        $issue->update([
            'status' => InquiryStatus::RESPONDED,
        ]);

        $this->info("✓ Balasan berhasil dikirim ke tiket {$issue->ticket_number}.");
        $this->line('Status tiket otomatis diubah menjadi: <fg=yellow>RESPONDED</>');

        return self::SUCCESS;
    }

    /**
     * Tandai tiket issue selesai (resolved).
     */
    protected function resolveIssue(): int
    {
        $ticket = $this->argument('ticket') ?: $this->ask('Masukkan Nomor Tiket atau ID Issue:');

        if (! $ticket) {
            $this->error('Nomor tiket wajib diisi.');

            return self::FAILURE;
        }

        $issue = $this->findInquiry($ticket);

        if (! $issue) {
            $this->error("Tiket issue '{$ticket}' tidak ditemukan.");

            return self::FAILURE;
        }

        $issue->update([
            'status' => InquiryStatus::RESOLVED,
        ]);

        $this->info("✓ Tiket issue {$issue->ticket_number} berhasil diselesaikan (RESOLVED).");

        return self::SUCCESS;
    }

    /**
     * Buat issue / tiket baru dari CLI.
     */
    protected function createIssue(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama Pelanggan:');
        $email = $this->option('email') ?: $this->ask('Email Pelanggan:');
        $subject = $this->option('subject') ?: $this->ask('Subjek / Masalah:');
        $message = $this->option('message') ?: $this->ask('Isi Pesan / Keluhan:');

        if (! $name || ! $email || ! $subject || ! $message) {
            $this->error('Semua data (nama, email, subjek, pesan) wajib diisi.');

            return self::FAILURE;
        }

        $ticketNumber = 'INQ-'.date('Ymd').'-'.strtoupper(Str::random(4));

        $inquiry = Inquiry::create([
            'ticket_number' => $ticketNumber,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'channel' => 'cli',
            'status' => InquiryStatus::UNREAD,
        ]);

        $inquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => $message,
            'created_at' => now(),
        ]);

        $this->info("✓ Issue/tiket baru berhasil dibuat dengan nomor: <fg=green;options=bold>{$ticketNumber}</>");

        return self::SUCCESS;
    }

    /**
     * Ringkasan statistik issues / tiket.
     */
    protected function showStats(): int
    {
        $total = Inquiry::count();
        $unread = Inquiry::where('status', 'unread')->count();
        $responded = Inquiry::where('status', 'responded')->count();
        $resolved = Inquiry::where('status', 'resolved')->count();

        $this->newLine();
        $this->info('=== STATISTIK ISSUES & TIKET CUSTOMER CARE ===');
        $this->line("Total Tiket Masuk : <options=bold>{$total}</>");
        $this->line("Belum Dibaca      : <fg=red;options=bold>{$unread}</>");
        $this->line("Sudah Dibalas     : <fg=yellow;options=bold>{$responded}</>");
        $this->line("Selesai (Resolved): <fg=green;options=bold>{$resolved}</>");
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Integrasi GitHub Issues CLI (via gh).
     */
    protected function manageGitHubIssues(): int
    {
        $this->info('=== GITHUB ISSUES INTEGRATION ===');

        $subaction = $this->argument('ticket') ?: 'list';

        $process = new Process(['gh', 'issue', $subaction]);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->warn('GitHub CLI report: '.trim($process->getErrorOutput() ?: $process->getOutput()));
            $this->line('Tip: Pastikan Anda sudah login GitHub CLI dengan: <fg=cyan>gh auth login</>');

            return self::FAILURE;
        }

        $this->line($process->getOutput());

        return self::SUCCESS;
    }

    /**
     * Cari inquiry berdasarkan ticket_number atau ID.
     */
    protected function findInquiry(string $identifier): ?Inquiry
    {
        return Inquiry::where('ticket_number', $identifier)
            ->orWhere('id', $identifier)
            ->with('messages')
            ->first();
    }

    /**
     * Format status badge untuk CLI.
     */
    protected function formatStatusBadge(InquiryStatus|string|null $status): string
    {
        $val = $status instanceof InquiryStatus ? $status->value : (string) $status;

        return match ($val) {
            'unread' => '<fg=red;options=bold>UNREAD (Belum Dibaca)</>',
            'responded' => '<fg=yellow;options=bold>RESPONDED (Sudah Dibalas)</>',
            'resolved' => '<fg=green;options=bold>RESOLVED (Selesai)</>',
            default => strtoupper($val),
        };
    }

    /**
     * Tampilkan pesan bantuan jika aksi tidak dikenali.
     */
    protected function unknownAction(string $action): int
    {
        $this->error("Aksi '{$action}' tidak dikenali.");
        $this->line('Aksi yang tersedia:');
        $this->line(' - <fg=cyan>php artisan issue list</>                 : Lihat semua issue tiket pelanggan');
        $this->line(' - <fg=cyan>php artisan issue show <ticket></>          : Lihat detail & pesan tiket');
        $this->line(' - <fg=cyan>php artisan issue reply <ticket></>         : Balas tiket langsung dari CLI');
        $this->line(' - <fg=cyan>php artisan issue resolve <ticket></>       : Tandai tiket selesai');
        $this->line(' - <fg=cyan>php artisan issue create</>                 : Buat tiket baru dari CLI');
        $this->line(' - <fg=cyan>php artisan issue stats</>                  : Lihat ringkasan metrik tiket');
        $this->line(' - <fg=cyan>php artisan issue github</>                 : Lihat daftar GitHub Issues repo');

        return self::FAILURE;
    }
}
