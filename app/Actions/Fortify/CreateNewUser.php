<?php

namespace App\Actions\Fortify;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

// Tambahkan use untuk job notifikasi
use App\Jobs\SendRegistrationNotificationToAdmin;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        Validator::make($input, [
            // User fields
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['sometimes', Rule::in(['SUPERADMIN', 'ADMIN', 'GURU', 'GURU_BK'])],

            // School fields (semua wajib)
            'school_name'   => ['required', 'string', 'max:255'],
            'npsn'          => ['required', 'string', 'size:8', 'unique:schools,npsn'],
            'school_level'  => ['required', Rule::in(['SD', 'SMP', 'SMA'])],
            'address'       => ['required', 'string', 'max:500'],
            'school_phone'  => ['required', 'string', 'max:20'],
            'school_email'  => ['required', 'email', 'max:255', 'unique:schools,email'],
            'school_logo'   => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ])->validate();

        // Upload logo ke S3 / RustFS
        $logoPath = null;
        if (isset($input['school_logo']) && $input['school_logo']->isValid()) {
            $logoPath = $input['school_logo']->store('school-logos', 's3');
        }

        // Buat sekolah
        $school = School::create([
            'name'      => $input['school_name'],
            'npsn'      => $input['npsn'],
            'level'     => $input['school_level'],
            'address'   => $input['address'],
            'phone'     => $input['school_phone'],
            'email'     => $input['school_email'],
            'logo'      => $logoPath,   // tersimpan path S3
            'is_active' => true,
        ]);

        // Buat user (role default ADMIN)
        $user = User::create([
            'name'      => $input['name'],
            'email'     => $input['email'],
            'password'  => Hash::make($input['password']),
            'role'      => $input['role'] ?? 'ADMIN',
            'school_id' => $school->id,
        ]);

        if(config('app.env') === 'production') {
            SendRegistrationNotificationToAdmin::dispatch($user, $school);
        }

        return $user;
    }
}