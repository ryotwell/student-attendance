<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan sekolah.
     */
    public function index()
    {
        $user = auth()->user();

        // Pastikan hanya ADMIN yang dapat mengakses
        abort_unless($user->role === 'ADMIN', 403);

        // Ambil sekolah berdasarkan school_id milik ADMIN
        $school = $user->school;

        // Jika ADMIN belum memiliki sekolah
        abort_if(
            !$school,
            404,
            'Sekolah belum terhubung dengan akun ini.'
        );

        return view(
            'admin.school-settings.index',
            compact('school')
        );
    }

    /**
     * Memperbarui pengaturan sekolah.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Pastikan hanya ADMIN yang dapat mengubah
        abort_unless($user->role === 'ADMIN', 403);

        // Ambil sekolah milik ADMIN yang sedang login
        $school = $user->school;

        // Pastikan sekolah tersedia
        abort_if(
            !$school,
            404,
            'Sekolah belum terhubung dengan akun ini.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'npsn' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'level' => [
                    'required',
                    'in:SD,SMP,SMA',
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:500',
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'logo' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048',
                ],
            ],
            [
                'name.required' =>
                    'Nama sekolah wajib diisi.',

                'name.string' =>
                    'Nama sekolah harus berupa teks.',

                'name.max' =>
                    'Nama sekolah maksimal 255 karakter.',

                'level.required' =>
                    'Jenjang sekolah wajib dipilih.',

                'level.in' =>
                    'Jenjang sekolah tidak valid.',

                'address.max' =>
                    'Alamat sekolah maksimal 500 karakter.',

                'phone.max' =>
                    'Nomor telepon maksimal 20 karakter.',

                'email.email' =>
                    'Format email sekolah tidak valid.',

                'email.max' =>
                    'Email sekolah maksimal 255 karakter.',

                'logo.image' =>
                    'File logo harus berupa gambar.',

                'logo.mimes' =>
                    'Logo harus berformat JPG, JPEG, atau PNG.',

                'logo.max' =>
                    'Ukuran logo maksimal 2 MB.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan Path Logo Lama
        |--------------------------------------------------------------------------
        |
        | Kita simpan terlebih dahulu path logo lama.
        | Logo lama baru dihapus setelah logo baru berhasil di-upload
        | dan database berhasil diperbarui.
        |
        */

        $oldLogo = $school->logo;

        /*
        |--------------------------------------------------------------------------
        | Upload Logo Baru ke S3 / RustFS
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');

            if ($file->isValid()) {

                /*
                |--------------------------------------------------------------------------
                | Upload ke S3 / RustFS
                |--------------------------------------------------------------------------
                */

                $newLogo = $file->store(
                    'school-logos',
                    's3'
                );

                /*
                |--------------------------------------------------------------------------
                | Simpan path logo baru ke data yang akan di-update
                |--------------------------------------------------------------------------
                */

                $validated['logo'] = $newLogo;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Data Sekolah
        |--------------------------------------------------------------------------
        */

        $school->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Hapus Logo Lama
        |--------------------------------------------------------------------------
        |
        | Tidak menggunakan Storage::exists().
        |
        | Ini penting karena pada konfigurasi S3/RustFS Anda,
        | operasi exists() menyebabkan:
        |
        | League\Flysystem\UnableToCheckFileExistence
        |
        */

        if (
            $request->hasFile('logo') &&
            $oldLogo &&
            $oldLogo !== $school->logo
        ) {
            try {

                Storage::disk('s3')->delete($oldLogo);

            } catch (\Throwable $e) {

                /*
                |--------------------------------------------------------------------------
                | Jangan gagalkan proses update sekolah
                |--------------------------------------------------------------------------
                |
                | Jika logo lama gagal dihapus dari S3/RustFS,
                | data sekolah dan logo baru tetap tersimpan.
                |
                */

                report($e);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('school-settings.index')
            ->with(
                'success',
                'Pengaturan sekolah berhasil diperbarui.'
            );
    }
}