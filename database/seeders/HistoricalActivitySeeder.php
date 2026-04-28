<?php

namespace Database\Seeders;

use App\Models\Bulletin;
use App\Models\Category;
use App\Models\LabUsageRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class HistoricalActivitySeeder extends Seeder
{
    /**
     * Seed historical activity data from July 2025 until April 2026.
     */
    public function run(): void
    {
        $categoryNames = ['Data Engineering', 'Digital Forensik'];
        $categoryIds = [];

        foreach ($categoryNames as $name) {
            $categoryIds[] = Category::firstOrCreate(['name' => $name])->id;
        }

        $dataEngineeringId = $categoryIds[0] ?? null;
        $digitalForensikId = $categoryIds[1] ?? null;

        $cohortNims = [
            '2022' => $this->buildNimRange(2211501001, 2211501056),
            '2023' => $this->buildNimRange(2311501001, 2311501056),
            '2024' => $this->buildNimRange(2411501001, 2411501056),
            '2025' => $this->buildNimRange(2511501001, 2511501056),
        ];

        $allNims = [];

        foreach ($cohortNims as $nims) {
            $allNims = array_merge($allNims, $nims);
        }

        $mahasiswaNames = $this->loadMahasiswaNamesByNim(array_values(array_unique($allNims)));

        $purposes = [
            'Praktikum mata kuliah',
            'Analisis dataset penelitian',
            'Validasi model dan evaluasi',
            'Pengolahan citra digital',
            'Pembuatan laporan praktikum',
            'Uji coba pipeline data',
            'Konsultasi dan asistensi laboratorium',
            'Training penggunaan perangkat lunak',
            'Eksplorasi data untuk studi kasus',
        ];

        $skripsiPurposes = [
            'Pengerjaan skripsi: pengumpulan dan validasi data',
            'Pengerjaan skripsi: eksperimen model',
            'Pengerjaan skripsi: analisis hasil dan revisi',
            'Bimbingan skripsi dan konsultasi NDU',
            'Penyusunan laporan bab hasil skripsi',
        ];

        $monthStart = Carbon::create(2025, 7, 1)->startOfMonth();
        $monthEnd = Carbon::create(2026, 4, 1)->startOfMonth();

        $monthIndex = 0;
        $cohortPointers = [
            '2022' => 0,
            '2023' => 0,
            '2024' => 0,
            '2025' => 0,
        ];
        // Proporsi peminjaman: 2022 paling banyak, lalu 2023, lalu 2024, dan 2025 paling sedikit.
        $cohortWeightPattern = [
            '2022', '2022', '2022', '2022', '2022', '2022', '2022', '2022', '2022', '2022', '2022', '2022',
            '2023', '2023', '2023', '2023', '2023',
            '2024', '2024',
            '2025',
        ];
        $minuteSet = [0, 10, 20, 30, 40, 50];

        while ($monthStart->lessThanOrEqualTo($monthEnd)) {
            for ($bulletinNo = 1; $bulletinNo <= 3; $bulletinNo++) {
                $categoryId = $categoryIds[($monthIndex + $bulletinNo) % count($categoryIds)];
                $publishedAt = $monthStart->copy()->day(min(28, 2 + ($bulletinNo * 8)))->setTime(9 + $bulletinNo, 0);
                $isDataEngineering = (int) $categoryId === (int) $dataEngineeringId;
                $bulletinTitle = $this->buildBulletinTitle($monthStart, $bulletinNo, $isDataEngineering);
                $bulletinContent = $this->buildBulletinContent($monthStart, $bulletinNo, $isDataEngineering, $publishedAt);

                Bulletin::updateOrCreate(
                    ['title' => $bulletinTitle],
                    [
                        'content' => $bulletinContent,
                        'category_id' => $categoryId,
                        'published_at' => $publishedAt,
                    ]
                );
            }

            $dayCursor = $monthStart->copy()->startOfMonth();
            $dayLimit = $monthStart->copy()->endOfMonth();

            while ($dayCursor->lessThanOrEqualTo($dayLimit)) {
                $recordsPerDay = 7 + (($monthIndex + $dayCursor->day) % 3); // sekitar 8 orang per hari (7-9).

                for ($recordNo = 0; $recordNo < $recordsPerDay; $recordNo++) {
                    $cohortLabel = $cohortWeightPattern[($monthIndex + $dayCursor->day + $recordNo) % count($cohortWeightPattern)];
                    $nims = $cohortNims[$cohortLabel];
                    $nimIndex = $cohortPointers[$cohortLabel] % count($nims);
                    $nim = (string) $nims[$nimIndex];
                    $cohortPointers[$cohortLabel]++;

                    $usageSelector = ($recordNo + $dayCursor->day + $monthIndex) % 10;
                    $recordCategoryId = $usageSelector < 8
                        ? ($dataEngineeringId ?? $categoryIds[0])
                        : ($digitalForensikId ?? $categoryIds[array_key_last($categoryIds)]);

                    if ($cohortLabel === '2022') {
                        if ((($recordNo + $monthIndex + $dayCursor->day) % 10) < 8) {
                            $purpose = $skripsiPurposes[($recordNo + $dayCursor->day + $monthIndex) % count($skripsiPurposes)];
                        } else {
                            $purpose = $purposes[($recordNo + $dayCursor->day + $monthIndex) % count($purposes)];
                        }
                    } else {
                        $purpose = $purposes[($recordNo + $dayCursor->day + $monthIndex) % count($purposes)];
                    }

                    $hour = 8 + (($recordNo + $monthIndex + $dayCursor->day) % 9);
                    $minute = $minuteSet[($recordNo + $monthIndex + $dayCursor->day) % count($minuteSet)];
                    $startTime = $dayCursor->copy()->setTime($hour, $minute);
                    $endTime = $startTime->copy()->addHours(2 + (($recordNo + $monthIndex) % 3));

                    LabUsageRecord::updateOrCreate(
                        [
                            'user_id_number' => $nim,
                            'start_time' => $startTime,
                            'purpose' => $purpose,
                        ],
                        [
                            'user_name' => $mahasiswaNames[$nim] ?? 'Mahasiswa '.$cohortLabel.' - '.$nim,
                            'category_id' => $recordCategoryId,
                            'end_time' => $endTime,
                        ]
                    );
                }

                $dayCursor->addDay();
            }

            $monthStart->addMonth();
            $monthIndex++;
        }
    }

    private function buildBulletinTitle(Carbon $monthStart, int $bulletinNo, bool $isDataEngineering): string
    {
        $monthLabel = $monthStart->translatedFormat('F Y');

        $dataEngineeringTitles = [
            'Kelas Praktik ETL '. $monthLabel .': Mahasiswa Simulasikan Integrasi Data Klinik',
            'Workshop Dashboard '. $monthLabel .': Visualisasi Data Layanan Kesehatan Berbasis Evidence',
            'Kolaborasi Riset '. $monthLabel .': Optimasi Pipeline Data Rekam Medis untuk Proyek Tugas Akhir',
        ];

        $digitalForensicsTitles = [
            'Sesi Digital Forensik '. $monthLabel .': Praktik Akuisisi Bukti dan Chain of Custody',
            'Studi Kasus '. $monthLabel .': Analisis Artefak Log pada Simulasi Insiden Siber Kampus',
            'Klinik Skripsi '. $monthLabel .': Pendampingan Validasi Bukti Digital untuk Penelitian Mahasiswa',
        ];

        $index = max(0, min(2, $bulletinNo - 1));

        return $isDataEngineering ? $dataEngineeringTitles[$index] : $digitalForensicsTitles[$index];
    }

    private function buildBulletinContent(Carbon $monthStart, int $bulletinNo, bool $isDataEngineering, Carbon $publishedAt): string
    {
        $monthLabel = $monthStart->translatedFormat('F Y');
        $dateLabel = $publishedAt->translatedFormat('d F Y');

        if ($isDataEngineering) {
            return "<p>Pada <strong>{$dateLabel}</strong>, Laboratorium Data Engineering menyelenggarakan sesi aktivitas rutin periode <strong>{$monthLabel}</strong> yang diikuti mahasiswa lintas angkatan. Sesi difokuskan pada praktik langsung pengolahan data kesehatan, mulai dari proses pembersihan data, perancangan skema, hingga evaluasi kualitas data sebelum digunakan pada tahap analisis.</p>\n"
                ."<p>Pada pertemuan ini, mahasiswa diminta membangun mini-pipeline menggunakan dataset simulasi layanan klinik. Pendamping laboratorium menekankan pentingnya dokumentasi proses agar setiap transformasi data dapat ditelusuri dengan baik saat proses review maupun penyusunan laporan tugas akhir.</p>\n"
                ."<p>Hasil evaluasi menunjukkan peningkatan ketepatan validasi data pada mayoritas kelompok. Beberapa kelompok juga mempresentasikan dashboard ringkas untuk memetakan tren kunjungan dan durasi layanan. Aktivitas ini menjadi bagian dari penguatan kompetensi analitik terapan yang relevan dengan kebutuhan industri kesehatan digital.</p>\n"
                ."<p><em>Catatan kegiatan {$bulletinNo}/{$monthLabel}: mahasiswa diminta melanjutkan perbaikan pipeline dan menyerahkan logbook eksperimen pada sesi asistensi berikutnya.</em></p>";
        }

        return "<p>Laboratorium Digital Forensik pada periode <strong>{$monthLabel}</strong> melaksanakan sesi praktik yang berfokus pada penanganan bukti digital secara terstruktur. Kegiatan yang berlangsung pada <strong>{$dateLabel}</strong> ini menyoroti tahapan akuisisi data, verifikasi integritas berkas, serta penyusunan kronologi temuan awal dari perangkat simulasi.</p>\n"
            ."<p>Mahasiswa berlatih menggunakan skenario insiden sederhana untuk mengidentifikasi artefak penting, termasuk jejak aktivitas pengguna, riwayat akses file, dan pola perubahan metadata. Selain aspek teknis, tim pendamping juga mengulas praktik chain of custody agar proses pemeriksaan tetap akuntabel dari awal hingga akhir.</p>\n"
            ."<p>Dari hasil diskusi, mahasiswa dinilai semakin mampu membedakan data relevan dan noise selama investigasi. Sesi ditutup dengan umpan balik terhadap draft laporan forensik, terutama pada bagian argumentasi temuan agar lebih kuat, objektif, dan mudah diverifikasi.</p>\n"
            ."<p><em>Catatan kegiatan {$bulletinNo}/{$monthLabel}: setiap peserta menyiapkan revisi laporan teknis untuk dipresentasikan pada pertemuan laboratorium berikutnya.</em></p>";
    }

    /**
     * Build NIM list from numeric range.
     *
     * @return array<int, string>
     */
    private function buildNimRange(int $start, int $end): array
    {
        $nims = [];

        for ($nim = $start; $nim <= $end; $nim++) {
            $nims[] = (string) $nim;
        }

        return $nims;
    }

    /**
     * Load mahasiswa names from external DB by NIM, similar to login lookup flow.
     *
     * @param array<int, string> $nims
     * @return array<string, string>
     */
    private function loadMahasiswaNamesByNim(array $nims): array
    {
        if (empty($nims)) {
            return [];
        }

        $connection = (string) config('services.unisa_auth.mahasiswa_connection', 'simlitabmas');
        $preferredTable = (string) config('services.unisa_auth.mahasiswa_table', 'ak_v_mahasiswa');

        $tables = array_values(array_unique([
            $preferredTable,
            'ak_mahasiswa',
            'mh_v_nama',
        ]));

        $nameColumns = ['namalengkap', 'nama_lengkap', 'nama', 'nm_mhs'];
        $resolved = [];
        $remainingNims = $nims;

        foreach ($tables as $table) {
            if ($table === '' || empty($remainingNims)) {
                continue;
            }

            foreach ($nameColumns as $nameColumn) {
                try {
                    $rows = DB::connection($connection)
                        ->table($table)
                        ->select(['nim', DB::raw("{$nameColumn} as resolved_name")])
                        ->whereIn('nim', $remainingNims)
                        ->get();
                } catch (Throwable) {
                    continue;
                }

                foreach ($rows as $row) {
                    $nim = isset($row->nim) ? trim((string) $row->nim) : '';
                    $name = isset($row->resolved_name) ? trim((string) $row->resolved_name) : '';

                    if ($nim !== '' && $name !== '' && ! isset($resolved[$nim])) {
                        $resolved[$nim] = $name;
                    }
                }

                if (! empty($resolved)) {
                    $remainingNims = array_values(array_filter(
                        $remainingNims,
                        static fn (string $nim): bool => ! isset($resolved[$nim])
                    ));
                }
            }
        }

        return $resolved;
    }
}
