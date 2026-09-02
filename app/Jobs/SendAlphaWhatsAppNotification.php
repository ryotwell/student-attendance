<?php

namespace App\Jobs;

use App\Helpers\WhatsAppHelper;
use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAlphaWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public Attendance $attendance
    ) {}

    public function handle(WhatsAppHelper $whatsapp): void
    {
        $fresh = Attendance::with(['studentEnrollment.student', 'schedule.subject'])
            ->find($this->attendance->id);

        if (! $fresh) {
            Log::info('Lewati notifikasi Alpha: attendance sudah terhapus', [
                'attendance_id' => $this->attendance->id,
            ]);

            return;
        }

        $this->attendance = $fresh;

        if ($this->attendance->status !== 'ALPHA') {
            return;
        }

        $student = $this->attendance->student;

        if (! $student || ! $student->parent_phone) {
            Log::info('Lewati notifikasi Alpha: nomor orang tua tidak tersedia', [
                'attendance_id' => $this->attendance->id,
                'student_id' => $student->id ?? null,
            ]);

            return;
        }

        $subjectName = $this->attendance->schedule->subject_name ?? 'mata pelajaran';
        $tanggal = $this->attendance->date->translatedFormat('d F Y');

        $pesan = "Assalamu'alaikum Bapak/Ibu.\n\n"
            . "Kami menyampaikan bahwa ananda *{$student->name}* tercatat *ALPHA* (tanpa keterangan) "
            . "pada pelajaran {$subjectName} tanggal {$tanggal}.\n\n"
            . "Mohon perhatian dan konfirmasinya. Terima kasih.";

        $result = $whatsapp->sendText($student->parent_phone, $pesan);

        if (! $result['success']) {
            Log::warning('Gagal mengirim notifikasi WA Alpha', [
                'attendance_id' => $this->attendance->id,
                'student_id' => $student->id,
                'response' => $result,
            ]);

            throw new \RuntimeException('WhatsApp API gagal: ' . ($result['data']['message'] ?? 'unknown error'));
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job notifikasi WA Alpha gagal permanen', [
            'attendance_id' => $this->attendance->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}