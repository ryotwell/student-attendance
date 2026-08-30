@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard Super Admin" />

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Ringkasan Sistem</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Data seluruh sekolah dan pengguna</p>
        </div>

        {{-- Kartu ringkasan --}}
        <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            {{-- Total Sekolah --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-900/30 dark:text-brand-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalSchools }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Total Sekolah (Aktif: {{ $activeSchools }})
                </div>
            </div>

            {{-- Total Admin --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalAdmins }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Admin</div>
            </div>

            {{-- Total Guru & BK --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-500 dark:bg-purple-900/30 dark:text-purple-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalTeachers + $totalBk }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Total Guru & BK (Guru: {{ $totalTeachers }}, BK: {{ $totalBk }})
                </div>
            </div>

            {{-- Total Siswa --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-500 dark:bg-green-900/30 dark:text-green-400">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalStudents }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Siswa</div>
            </div>
        </div>

        {{-- Kartu tambahan (dua baris) --}}
        <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalAcademicYears }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Tahun Ajaran (Aktif: {{ $activeAcademicYears }})</div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ $totalClasses }}</div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Kelas</div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                    {{ $levelStats->where('level', 'SD')->first()->total ?? 0 }}
                </div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Sekolah SD</div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                    {{ $levelStats->where('level', 'SMP')->first()->total ?? 0 }}
                </div>
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Sekolah SMP</div>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Grafik Siswa -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">10 Sekolah dengan Siswa Terbanyak</h4>
                <div style="position: relative; height: 200px; width: 100%;">
                    <canvas id="studentChart"></canvas>
                </div>
            </div>

            <!-- Grafik Guru -->
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
                <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">10 Sekolah dengan Guru Terbanyak</h4>
                <div style="position: relative; height: 200px; width: 100%;">
                    <canvas id="teacherChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Aktivitas terbaru --}}
        <div class="mt-8 rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Sekolah Terbaru</h4>
            </div>
            <div class="max-h-[200px] overflow-y-auto">
                @forelse ($recentSchools as $school)
                    <div class="flex items-center gap-3 border-b border-gray-100 px-5 py-3 last:border-0 dark:border-gray-800">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $school->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $school->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada sekolah.</div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Pastikan data ada dan tidak kosong
                const studentData = @json($schoolStudentStats);
                const teacherData = @json($schoolTeacherStats);

                // Fungsi untuk membuat grafik dengan aman
                function createChart(canvasId, labels, data, label, color) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    // Hapus grafik sebelumnya jika ada (untuk menghindari duplikasi)
                    const existingChart = Chart.getChart(canvasId);
                    if (existingChart) {
                        existingChart.destroy();
                    }

                    // Jika data kosong, tampilkan pesan
                    if (!labels || labels.length === 0) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#999';
                        ctx.textAlign = 'center';
                        ctx.fillText('Tidak ada data', canvas.width / 2, canvas.height / 2);
                        return;
                    }

                    new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: label,
                                data: data,
                                backgroundColor: color,
                                borderColor: color,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            }
                        }
                    });
                }

                // Buat grafik siswa
                if (studentData && studentData.length > 0) {
                    createChart(
                        'studentChart',
                        studentData.map(item => item.name),
                        studentData.map(item => item.students_count),
                        'Jumlah Siswa',
                        'rgba(59, 130, 246, 0.6)'
                    );
                } else {
                    // Tampilkan pesan jika data kosong
                    const canvas = document.getElementById('studentChart');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#999';
                        ctx.textAlign = 'center';
                        ctx.fillText('Belum ada data siswa', canvas.width / 2, canvas.height / 2);
                    }
                }

                // Buat grafik guru
                if (teacherData && teacherData.length > 0) {
                    createChart(
                        'teacherChart',
                        teacherData.map(item => item.name),
                        teacherData.map(item => item.users_count),
                        'Jumlah Guru',
                        'rgba(16, 185, 129, 0.6)'
                    );
                } else {
                    const canvas = document.getElementById('teacherChart');
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#999';
                        ctx.textAlign = 'center';
                        ctx.fillText('Belum ada data guru', canvas.width / 2, canvas.height / 2);
                    }
                }
            });
        </script>
    @endpush
@endsection