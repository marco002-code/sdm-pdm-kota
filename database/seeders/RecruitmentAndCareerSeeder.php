<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\HeadmasterCandidate;
use App\Models\HeadmasterSelection;
use App\Models\RankPromotion;
use App\Models\Recruitment;
use App\Models\RecruitmentApplicant;
use App\Models\Unit;
use App\Models\User;
use App\Models\ViceHeadmasterNomination;
use Illuminate\Database\Seeder;

class RecruitmentAndCareerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $sdSapen = Unit::where('name', 'like', '%Sapen%')->first() ?? Unit::first();
        $sdNgupasan = Unit::where('name', 'like', '%Ngupasan%')->first() ?? Unit::skip(1)->first();
        $smpMuh2 = Unit::where('name', 'like', '%SMP%')->first() ?? Unit::skip(2)->first();
        $smaMuh1 = Unit::where('name', 'like', '%SMA%')->first() ?? Unit::skip(3)->first();

        // ==========================================
        // 1. SEED DATA REKRUTMEN (Berdasarkan pdmjogja.org)
        // ==========================================
        $recruitment = Recruitment::create([
            'title'                => 'Rekrutmen Calon Guru & Pegawai Sekolah/Madrasah Muhammadiyah Kota Yogyakarta 2026',
            'batch_code'           => 'SELEKSI-GTT-PTT-2026-I',
            'year'                 => 2026,
            'start_date'           => '2026-04-15',
            'end_date'             => '2026-05-10',
            'test_date'            => '2026-05-18', // Sesuai ralat jadwal di pengumuman pdmjogja.org
            'announcement_date'    => '2026-06-05',
            'target_employee_type' => 'SEMUA',
            'positions'            => [
                ['name' => 'Guru Kelas SD', 'category' => 'GTT', 'level' => 'SD', 'quota' => 12],
                ['name' => 'Guru PAI / ISMUBA', 'category' => 'GTT', 'level' => 'SD/SMP', 'quota' => 6],
                ['name' => 'Guru Bimbingan Konseling (BK)', 'category' => 'GTT', 'level' => 'SMP/SMA', 'quota' => 4],
                ['name' => 'Guru Matematika', 'category' => 'GTT', 'level' => 'SMP/SMA', 'quota' => 5],
                ['name' => 'Staf Administrasi / Tata Usaha', 'category' => 'PTT', 'level' => 'SEMUA', 'quota' => 8],
                ['name' => 'Laboran & Teknisi IT', 'category' => 'PTT', 'level' => 'SMP/SMA', 'quota' => 3],
            ],
            'requirements'         => "1. Warga Negara Indonesia, beragama Islam, taat beribadah.\n2. Memiliki Kartu Tanda Anggota Muhammadiyah (KTAM/NBM) atau bersedia membuat.\n3. Kualifikasi S1 kependidikan linear untuk Guru, IPK min 3.00.\n4. Mampu membaca Al-Qur'an dengan tartil dan lancar.\n5. Berkelakuan baik dan loyal kepada Persyarikatan Muhammadiyah.",
            'description'          => 'Program seleksi terpadu Majelis Pendidikan Dasar, Menengah dan Pendidikan Nonformal PDM Kota Yogyakarta untuk pemenuhan kebutuhan guru dan tendik di 76 Amal Usaha Muhammadiyah.',
            'status'               => 'ujian_penjajagan',
            'created_by'           => $admin?->id,
        ]);

        $sampleApplicants = [
            [
                'name' => 'Ahmad Fauzi Ridwan, S.Pd.', 'nik' => '3471011508980001', 'nbm' => '1288901', 'gender' => 'L',
                'pos' => 'Guru Kelas SD', 'edu' => 'S1', 'major' => 'PGSD', 'univ' => 'Universitas Ahmad Dahlan', 'gpa' => 3.72,
                'written' => 85.0, 'aik' => 88.0, 'worship' => 90.0, 'interview' => 86.0, 'micro' => 88.0,
                'status' => 'lulus', 'pref' => $sdSapen?->id, 'placed' => $sdSapen?->id, 'sk' => 'SK-GTT/012/PDM/VI/2026'
            ],
            [
                'name' => 'Siti Nur Khasanah, S.Pd.I.', 'nik' => '3471025503990002', 'nbm' => '1288902', 'gender' => 'P',
                'pos' => 'Guru PAI / ISMUBA', 'edu' => 'S1', 'major' => 'Pendidikan Agama Islam', 'univ' => 'UIN Sunan Kalijaga', 'gpa' => 3.80,
                'written' => 88.0, 'aik' => 92.0, 'worship' => 94.0, 'interview' => 90.0, 'micro' => 89.0,
                'status' => 'lulus', 'pref' => $smpMuh2?->id, 'placed' => $smpMuh2?->id, 'sk' => 'SK-GTT/013/PDM/VI/2026'
            ],
            [
                'name' => 'Muhammad Rizqi Pratama, S.Kom.', 'nik' => '3471032204970003', 'nbm' => null, 'gender' => 'L',
                'pos' => 'Laboran & Teknisi IT', 'edu' => 'S1', 'major' => 'Teknik Informatika', 'univ' => 'Universitas Muhammadiyah Yogyakarta', 'gpa' => 3.65,
                'written' => 82.0, 'aik' => 78.0, 'worship' => 80.0, 'interview' => 84.0, 'micro' => 0.0,
                'status' => 'lulus', 'pref' => $smaMuh1?->id, 'placed' => $smaMuh1?->id, 'sk' => 'SK-PTT/005/PDM/VI/2026'
            ],
            [
                'name' => 'Dewi Anggraini, S.Pd.', 'nik' => '3471046811980004', 'nbm' => '1288904', 'gender' => 'P',
                'pos' => 'Guru Matematika', 'edu' => 'S1', 'major' => 'Pendidikan Matematika', 'univ' => 'Universitas Ahmad Dahlan', 'gpa' => 3.55,
                'written' => 78.0, 'aik' => 80.0, 'worship' => 82.0, 'interview' => 80.0, 'micro' => 82.0,
                'status' => 'cadangan', 'pref' => $smpMuh2?->id, 'placed' => null, 'sk' => null
            ],
            [
                'name' => 'Hendra Gunawan, A.Md.', 'nik' => '3471051201960005', 'nbm' => null, 'gender' => 'L',
                'pos' => 'Staf Administrasi / Tata Usaha', 'edu' => 'D3', 'major' => 'Manajemen Perkantoran', 'univ' => 'Universitas Negeri Yogyakarta', 'gpa' => 3.40,
                'written' => 70.0, 'aik' => 65.0, 'worship' => 72.0, 'interview' => 74.0, 'micro' => 0.0,
                'status' => 'ujian_penjajagan', 'pref' => $sdNgupasan?->id, 'placed' => null, 'sk' => null
            ],
            [
                'name' => 'Annisa Rahmawati, S.Pd.', 'nik' => '3471064509990006', 'nbm' => '1288906', 'gender' => 'P',
                'pos' => 'Guru Bimbingan Konseling (BK)', 'edu' => 'S1', 'major' => 'Bimbingan dan Konseling', 'univ' => 'Universitas Ahmad Dahlan', 'gpa' => 3.68,
                'written' => 80.0, 'aik' => 84.0, 'worship' => 85.0, 'interview' => 82.0, 'micro' => 85.0,
                'status' => 'wawancara_microteaching', 'pref' => $smaMuh1?->id, 'placed' => null, 'sk' => null
            ],
        ];

        foreach ($sampleApplicants as $i => $app) {
            $regNum = 'REG-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $applicant = RecruitmentApplicant::create([
                'recruitment_id'        => $recruitment->id,
                'registration_number'   => $regNum,
                'nik'                   => $app['nik'],
                'nbm'                   => $app['nbm'],
                'full_name'             => $app['name'],
                'gender'                => $app['gender'],
                'birth_place'           => 'Yogyakarta',
                'birth_date'            => '1998-05-15',
                'email'                 => strtolower(str_replace([' ', ',', '.'], '', substr($app['name'], 0, 10))) . '@gmail.com',
                'phone_number'          => '08122' . rand(1000000, 9999999),
                'highest_education'     => $app['edu'],
                'major'                 => $app['major'],
                'university'            => $app['univ'],
                'gpa'                   => $app['gpa'],
                'applied_position'      => $app['pos'],
                'preferred_unit_id'     => $app['pref'],
                'placed_unit_id'        => $app['placed'],
                'employee_type'         => str_contains($app['pos'], 'Guru') ? 'GTT' : 'PTT',
                'administration_status' => 'lolos',
                'written_test_score'    => $app['written'],
                'aik_test_score'        => $app['aik'],
                'worship_test_score'    => $app['worship'],
                'interview_score'       => $app['interview'],
                'microteaching_score'   => $app['micro'],
                'selection_status'      => $app['status'],
                'sk_number'             => $app['sk'],
                'sk_date'               => $app['sk'] ? '2026-06-10' : null,
            ]);
            $applicant->calculateFinalScore();
            $applicant->save();
        }

        // ==========================================
        // 2. SEED DATA KENAIKAN PANGKAT & ALIH STATUS
        // ==========================================
        $gttEmp = Employee::where('employee_type', 'GTT')->first();
        $pttEmp = Employee::where('employee_type', 'PTT')->first();
        $gtyEmp = Employee::where('employee_type', 'GTY')->first();
        $gtyEmp2 = Employee::where('employee_type', 'GTY')->skip(1)->first();

        if ($gttEmp) {
            RankPromotion::create([
                'employee_id'            => $gttEmp->id,
                'unit_id'                => $gttEmp->unit_id,
                'promotion_type'         => 'alih_status_gty',
                'current_rank'           => 'Guru Tidak Tetap',
                'proposed_rank'          => 'Penata Muda / III/a (GTY)',
                'current_employee_type'  => 'GTT',
                'proposed_employee_type' => 'GTY',
                'years_of_service'       => 4,
                'performance_score'      => 89.5,
                'credit_points'          => 120.0,
                'new_education_degree'   => 'S1 Pendidikan',
                'ortom_involvement'      => 'Pengurus Pimpinan Cabang Pemuda Muhammadiyah Kota Yogyakarta, Aktif Kokam.',
                'status'                 => 'disetujui_pdm',
                'reviewer_notes'         => 'Memenuhi syarat masa pengabdian 4 tahun, lulus Baitul Arqam PDM, portofolio mengajar sangat baik.',
                'sk_number'              => '042/KEP/III.0/D/2026',
                'sk_date'                => '2026-05-15',
                'effective_date'         => '2026-06-01',
                'verified_by'            => $admin?->id,
            ]);
        }

        if ($gtyEmp) {
            RankPromotion::create([
                'employee_id'            => $gtyEmp->id,
                'unit_id'                => $gtyEmp->unit_id,
                'promotion_type'         => 'reguler_berkala',
                'current_rank'           => 'Penata Muda Tk. I / III/b',
                'proposed_rank'          => 'Penata / III/c',
                'current_employee_type'  => 'GTY',
                'proposed_employee_type' => 'GTY',
                'years_of_service'       => 8,
                'performance_score'      => 92.0,
                'credit_points'          => 205.5,
                'new_education_degree'   => null,
                'ortom_involvement'      => 'Majelis Tabligh Pimpinan Ranting Muhammadiyah, Pembina Hizbul Wathan.',
                'status'                 => 'sidang_majelis',
                'reviewer_notes'         => 'Berkas PAK sedang direview oleh Tim Penilai Majelis Dikdasmen PDM.',
                'sk_number'              => null,
                'sk_date'                => null,
                'effective_date'         => null,
                'verified_by'            => null,
            ]);
        }

        if ($gtyEmp2) {
            RankPromotion::create([
                'employee_id'            => $gtyEmp2->id,
                'unit_id'                => $gtyEmp2->unit_id,
                'promotion_type'         => 'penyesuaian_ijazah',
                'current_rank'           => 'Penata Muda / III/a',
                'proposed_rank'          => 'Penata Muda Tk. I / III/b',
                'current_employee_type'  => 'GTY',
                'proposed_employee_type' => 'GTY',
                'years_of_service'       => 6,
                'performance_score'      => 88.0,
                'credit_points'          => 160.0,
                'new_education_degree'   => 'Magister Pendidikan (M.Pd.) - UAD',
                'ortom_involvement'      => 'Kader Nasyiatul Aisyiyah Kota Yogyakarta.',
                'status'                 => 'diverifikasi_sekolah',
                'reviewer_notes'         => 'Ijazah S2 telah diverifikasi keabsahannya oleh Kepala Sekolah.',
                'sk_number'              => null,
                'sk_date'                => null,
                'effective_date'         => null,
                'verified_by'            => null,
            ]);
        }

        if ($pttEmp) {
            RankPromotion::create([
                'employee_id'            => $pttEmp->id,
                'unit_id'                => $pttEmp->unit_id,
                'promotion_type'         => 'alih_status_pty',
                'current_rank'           => 'Pegawai Tidak Tetap (PTT)',
                'proposed_rank'          => 'Pengatur Muda / II/a (PTY)',
                'current_employee_type'  => 'PTT',
                'proposed_employee_type' => 'PTY',
                'years_of_service'       => 5,
                'performance_score'      => 87.5,
                'credit_points'          => 85.0,
                'new_education_degree'   => 'D3 Administrasi Perkantoran',
                'ortom_involvement'      => 'Anggota Komando Kesiapsiagaan Angkatan Muda Muhammadiyah (KOKAM).',
                'status'                 => 'diajukan',
                'reviewer_notes'         => null,
                'sk_number'              => null,
                'sk_date'                => null,
                'effective_date'         => null,
                'verified_by'            => null,
            ]);
        }

        // ==========================================
        // 3. SEED DATA PROMOSI KEPALA SEKOLAH
        // ==========================================
        // Contoh sesuai pengumuman PDM: SD Muhammadiyah Ngupasan 1 Yogyakarta & SMP Muhammadiyah 2
        $selNgupasan = HeadmasterSelection::create([
            'unit_id'           => $sdNgupasan?->id,
            'period_years'      => '2026 - 2030',
            'announcement_date' => '2026-05-01',
            'closing_date'      => '2026-06-01',
            'fit_proper_date'   => '2026-06-15',
            'pleno_date'        => '2026-06-25',
            'status'            => 'selesai',
            'notes'             => 'Regenerasi kepemimpinan SD Muhammadiyah Ngupasan 1 masa jabatan 2026-2030.',
        ]);

        $candidateGty = Employee::where('unit_id', $sdNgupasan?->id)->where('employee_type', 'GTY')->first() ?? Employee::where('employee_type', 'GTY')->skip(2)->first();
        $candidateGty2 = Employee::where('employee_type', 'GTY')->skip(3)->first();

        if ($candidateGty) {
            $cand1 = HeadmasterCandidate::create([
                'headmaster_selection_id' => $selNgupasan->id,
                'employee_id'             => $candidateGty->id,
                'nbm'                     => $candidateGty->nbm ?? '1098231',
                'vision_mission_rps'      => 'Mewujudkan SD Muhammadiyah Ngupasan 1 sebagai Sekolah Unggul Berwawasan Global dan Berkarakter Qurani melalui Digitalisasi Pembelajaran & Penguatan ISMUBA.',
                'recommendation_pcm'      => 'Surat Rekomendasi PCM Gondomanan No. 12/PCM-GD/A/2026',
                'recommendation_komite'   => 'Rekomendasi Komite Sekolah No. 05/KOM/VI/2026',
                'managerial_score'        => 92.0,
                'supervision_score'       => 90.0,
                'entrepreneurship_score'  => 88.0,
                'aik_score'               => 95.0,
                'presentation_score'      => 93.0,
                'pleno_decision'          => 'direkomendasikan',
                'is_elected'              => true,
                'sk_number'               => '105/KEP/III.0/D/2026',
                'sk_date'                 => '2026-07-01',
                'inauguration_date'       => '2026-07-08', // Tanggal pelantikan sesuai berita pdmjogja.org
            ]);
            $cand1->calculateTotalScore();
            $cand1->save();
        }

        if ($candidateGty2) {
            $cand2 = HeadmasterCandidate::create([
                'headmaster_selection_id' => $selNgupasan->id,
                'employee_id'             => $candidateGty2->id,
                'nbm'                     => $candidateGty2->nbm ?? '1098232',
                'vision_mission_rps'      => 'Optimalisasi Budaya Sekolah Ramah Anak & Berprestasi Akademik.',
                'recommendation_pcm'      => 'Surat Rekomendasi PCM No. 14/PCM/A/2026',
                'recommendation_komite'   => 'Rekomendasi Komite Sekolah No. 06/KOM/VI/2026',
                'managerial_score'        => 84.0,
                'supervision_score'       => 82.0,
                'entrepreneurship_score'  => 80.0,
                'aik_score'               => 88.0,
                'presentation_score'      => 85.0,
                'pleno_decision'          => 'cadangan',
                'is_elected'              => false,
            ]);
            $cand2->calculateTotalScore();
            $cand2->save();
        }

        // Seleksi yang sedang berjalan (SMP Muhammadiyah 2)
        $selSmp = HeadmasterSelection::create([
            'unit_id'           => $smpMuh2?->id,
            'period_years'      => '2026 - 2030',
            'announcement_date' => '2026-08-01',
            'closing_date'      => '2026-09-15',
            'fit_proper_date'   => '2026-09-25',
            'pleno_date'        => null,
            'status'            => 'penjaringan',
            'notes'             => 'Proses penjaringan bakal calon Kepala SMP Muhammadiyah 2 masa bakti 2026-2030.',
        ]);

        // ==========================================
        // 4. SEED DATA PROMOSI WAKIL KEPALA SEKOLAH
        // ==========================================
        $gtyWaka1 = Employee::where('employee_type', 'GTY')->skip(4)->first();
        $gtyWaka2 = Employee::where('employee_type', 'GTY')->skip(5)->first();
        $gtyWaka3 = Employee::where('employee_type', 'GTY')->skip(6)->first();

        if ($gtyWaka1) {
            ViceHeadmasterNomination::create([
                'unit_id'                   => $sdNgupasan?->id,
                'employee_id'               => $gtyWaka1->id,
                'field'                     => 'kurikulum_ismuba',
                'period_years'              => '2026 - 2030',
                'recommendation_headmaster' => 'Diusulkan berdasarkan kompetensi perancangan Kurikulum Merdeka dan penguatan Al-Islam Kemuhammadiyahan.',
                'status'                    => 'disetujui',
                'sk_number'                 => '112/KEP/III.0/D/2026',
                'sk_date'                   => '2026-07-15',
                'notes'                     => 'Disetujui dalam rapat koordinasi Majelis Dikdasmen PDM.',
            ]);
        }

        if ($gtyWaka2) {
            ViceHeadmasterNomination::create([
                'unit_id'                   => $sdNgupasan?->id,
                'employee_id'               => $gtyWaka2->id,
                'field'                     => 'kesiswaan',
                'period_years'              => '2026 - 2030',
                'recommendation_headmaster' => 'Aktif membina ekstrakurikuler Hizbul Wathan dan Tapak Suci Putra Muhammadiyah.',
                'status'                    => 'disetujui',
                'sk_number'                 => '113/KEP/III.0/D/2026',
                'sk_date'                   => '2026-07-15',
                'notes'                     => 'Memenuhi kualifikasi kepemimpinan kesiswaan.',
            ]);
        }

        if ($gtyWaka3) {
            ViceHeadmasterNomination::create([
                'unit_id'                   => $smpMuh2?->id,
                'employee_id'               => $gtyWaka3->id,
                'field'                     => 'sarpras',
                'period_years'              => '2026 - 2030',
                'recommendation_headmaster' => 'Memiliki dedikasi tinggi dalam pengelolaan aset dan sarana prasarana sekolah.',
                'status'                    => 'verifikasi_majelis',
                'sk_number'                 => null,
                'sk_date'                   => null,
                'notes'                     => 'Menunggu sidang pleno Majelis Dikdasmen PDM.',
            ]);
        }
    }
}
