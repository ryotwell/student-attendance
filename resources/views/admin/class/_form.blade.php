@props(['class' => null])


<form method="POST"
    action="{{ $class 
        ? route('classes.update', $class) 
        : route('classes.store') }}">


    @csrf


    @if($class)
        @method('PUT')
    @endif



    <x-common.component-card 
        :title="$class ? 'Edit Kelas' : 'Tambah Kelas'">


        {{-- Nama Kelas --}}
        <div>

            <label 
                for="name"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                Nama Kelas
                <span class="text-error-500">*</span>

            </label>



            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name',$class->name ?? '') }}"
                placeholder="Contoh: 7 A"
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm"
            >


            @error('name')
                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>
            @enderror

        </div>




        {{-- Tahun Ajaran --}}
        <div>


            <label
                for="academic_year_id"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">

                Tahun Ajaran
                <span class="text-error-500">*</span>

            </label>



            <select
                id="academic_year_id"
                name="academic_year_id"
                class="h-11 w-full rounded-lg border border-gray-300 px-4">


                <option value="">
                    Pilih Tahun Ajaran
                </option>



                @foreach($academicYears as $academicYear)

                    <option 
                        value="{{ $academicYear->id }}"
                        
                        @selected(
                            old(
                                'academic_year_id',
                                $class->academic_year_id ?? ''
                            )
                            ==
                            $academicYear->id
                        )>


                        {{ $academicYear->name }}
                        -
                        {{ $academicYear->semester }}

                        (
                        {{ $academicYear->is_active 
                            ? 'Aktif'
                            : 'Tidak Aktif'
                        }}
                        )

                    </option>

                @endforeach


            </select>


            @error('academic_year_id')

                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>

            @enderror


        </div>






        {{-- Wali Kelas --}}
        <div>


            <label
                for="user_id"
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">


                Wali Kelas

            </label>




            <select
                id="user_id"
                name="user_id"
                class="h-11 w-full rounded-lg border border-gray-300 px-4">


                <option value="">
                    Pilih Wali Kelas
                </option>




                @foreach($users as $user)

                    <option
                        value="{{ $user->id }}"

                        @selected(
                            old(
                                'user_id',
                                $class->user_id ?? ''
                            )
                            ==
                            $user->id
                        )>


                        {{ $user->name }}
                        -
                        {{ $user->email }}


                    </option>


                @endforeach


            </select>



            @error('user_id')

                <p class="mt-1.5 text-sm text-error-500">
                    {{ $message }}
                </p>

            @enderror


        </div>







        {{-- Button --}}
        <div class="flex items-center gap-4">


            <button
                type="submit"
                class="bg-brand-500 hover:bg-brand-600 rounded-lg px-5 py-3 text-sm font-medium text-white">


                {{ $class ? 'Perbarui' : 'Simpan' }}


            </button>




            <a href="{{ route('classes.index') }}"
                class="rounded-lg px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300">


                Batal


            </a>


        </div>



    </x-common.component-card>


</form>