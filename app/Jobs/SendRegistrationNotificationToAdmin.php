<?php

namespace App\Jobs;

use App\Helpers\WhatsAppHelper;
use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRegistrationNotificationToAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 30;

    protected string $adminPhone = '6281947556108';

    public function __construct(
        protected User $user,
        protected School $school
    ) {}

    public function handle(WhatsAppHelper $whatsapp): void
    {
        // Refresh data dari database untuk memastikan fresh
        $user = User::with('school')->find($this->user->id);
        $school = School::find($this->school->id);

        if (! $user || ! $school) {
            Log::warning('Notifikasi pendaftaran gagal: user atau sekolah tidak ditemukan', [
                'user_id'   => $this->user->id ?? null,
                'school_id' => $this->school->id ?? null,
            ]);
            return;
        }

        $pesan = "📢 *PENDAFTARAN BARU DI PORTAL SEKOLAH*\n\n"
            . "Admin baru telah mendaftar:\n"
            . "👤 Nama: {$user->name}\n"
            . "📧 Email: {$user->email}\n"
            . "🏫 Sekolah: {$school->name}\n"
            . "📌 NPSN: {$school->npsn}\n"
            . "🏷️ Level: {$school->level}\n"
            . "📍 Alamat: {$school->address}\n"
            . "📞 Telp: {$school->phone}\n"
            . "📧 Email Sekolah: {$school->email}\n\n"
            . "Segera lakukan verifikasi data jika diperlukan.";

        $result = $whatsapp->sendText($this->adminPhone, $pesan);

        if (! $result['success']) {
            Log::warning('Gagal mengirim notifikasi pendaftaran ke admin', [
                'admin_phone'   => $this->adminPhone,
                'user_id'       => $user->id,
                'school_id'     => $school->id,
                'response'      => $result,
            ]);

            throw new \RuntimeException('WhatsApp API gagal: ' . ($result['data']['message'] ?? 'unknown error'));
        }

        Log::info('Notifikasi pendaftaran berhasil dikirim ke admin', [
            'admin_phone' => $this->adminPhone,
            'user_id'     => $user->id,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job notifikasi pendaftaran gagal permanen', [
            'user_id'   => $this->user->id ?? null,
            'school_id' => $this->school->id ?? null,
            'error'     => $exception->getMessage(),
            'trace'     => $exception->getTraceAsString(),
        ]);
    }
}