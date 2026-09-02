@props(['school' => null])

<form method="POST" action="{{ $school ? route('superadmin.schools.update', $school) : route('superadmin.schools.store') }}">
    @csrf
    @if ($school)
        @method('PUT')
    @endif

    <div class="space-y-4">
        {{-- Nama Sekolah --}}
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Nama Sekolah <span class="text-error-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name', $school->name ?? '') }}"
                placeholder="cth: SD Negeri 1 Jakarta"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('name')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- NPSN --}}
            <div>
                <label for="npsn" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    NPSN
                </label>
                <input type="text" id="npsn" name="npsn" value="{{ old('npsn', $school->npsn ?? '') }}"
                    placeholder="cth: 12345678"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('npsn')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Level --}}
            <div>
                <label for="level" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Level <span class="text-error-500">*</span>
                </label>
                <select id="level" name="level"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Pilih Level</option>
                    <option value="SD" @selected(old('level', $school->level ?? '') === 'SD')>SD</option>
                    <option value="SMP" @selected(old('level', $school->level ?? '') === 'SMP')>SMP</option>
                    <option value="SMA" @selected(old('level', $school->level ?? '') === 'SMA')>SMA</option>
                </select>
                @error('level')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Alamat --}}
        <div>
            <label for="address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Alamat
            </label>
            <input type="text" id="address" name="address" value="{{ old('address', $school->address ?? '') }}"
                placeholder="cth: Jl. Pendidikan No. 1"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            @error('address')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- Telepon --}}
            <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    No. Telepon
                </label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $school->phone ?? '') }}"
                    placeholder="cth: 021-1234567"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('phone')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $school->email ?? '') }}"
                    placeholder="cth: sekolah@email.com"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                @error('email')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Package --}}
        <div>
            <label for="package" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Paket <span class="text-error-500">*</span>
            </label>
            <select id="package" name="package"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="basic" @selected(old('package', $school->package ?? 'basic') === 'basic')>Basic</option>
                <option value="premium" @selected(old('package', $school->package ?? 'basic') === 'premium')>Premium</option>
            </select>
            @error('package')
                <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                {{ $school ? 'Perbarui' : 'Simpan' }}
            </button>
            <a href="{{ route('superadmin.schools.index') }}"
                class="inline-flex items-center justify-center rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                Batal
            </a>
        </div>
    </div>
</form>