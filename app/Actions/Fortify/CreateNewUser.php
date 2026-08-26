<?php

namespace App\Actions\Fortify;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        Validator::make($input, [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'school_name'   => ['required', 'string', 'max:255'],
            'school_level'  => ['required', Rule::in(['SD', 'SMP', 'SMA'])],
        ])->validate();

        // Buat sekolah dengan level
        $school = School::create([
            'name'  => $input['school_name'],
            'level' => $input['school_level'],
            'is_active' => true,
        ]);

        // Buat user dengan role ADMIN dan terikat ke sekolah
        return User::create([
            'name'      => $input['name'],
            'email'     => $input['email'],
            'password'  => Hash::make($input['password']),
            'role'      => 'ADMIN',
            'school_id' => $school->id,
        ]);
    }
}