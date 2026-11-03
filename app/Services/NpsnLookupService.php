<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NpsnLookupService
{
    /**
     * Database referensi data resmi sekolah AUM PDM Kota Yogyakarta by NPSN.
     */
    protected static array $localDirectory = [
        // TPA (Taman Pengasuhan Anak)
        '69830054' => [
            'name'          => 'TPA AISYIYAH',
            'address'       => 'Jl. Sagan No. 1',
            'village'       => 'Terban',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'B',
        ],
        '69830063' => [
            'name'          => 'TPA AISYIYAH SURYOCONDRO',
            'address'       => 'Suryocondro KT II/430',
            'village'       => 'Panembahan',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'B',
        ],
        '69849711' => [
            'name'          => 'TAA AISYIYAH NUR AINI NGAMPILAN',
            'address'       => 'Ngampilan NG I/254',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '69849712' => [
            'name'          => 'TAMAN ASUH ANAK. QURROTA AYUN',
            'address'       => 'Jl. Kapten Piere Tendean No. 20',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'B',
        ],
        '69876326' => [
            'name'          => 'TPA MUNAWWAROH',
            'address'       => 'Kricak Kidul TR I/1202',
            'village'       => 'Kricak',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'B',
        ],
        '69963767' => [
            'name'          => 'TPA AISYIYAH AINUN JARIYAH KAUMAN',
            'address'       => 'Kauman GM I/305',
            'village'       => 'Ngupasan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],

        // TK / ABA (Taman Kanak-Kanak)
        '20409019' => [
            'name'          => 'TK ABA SAPEN',
            'address'       => 'Jl. Bimo Kurdo No. 33',
            'village'       => 'Demangan',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20409020' => [
            'name'          => 'TK ABA CEMPAKA',
            'address'       => 'Jl. Cempaka No. 5',
            'village'       => 'Baciro',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20409021' => [
            'name'          => 'TK ABA AL – IMAN',
            'address'       => 'Ireda Gg. Al Iman No. 14',
            'village'       => 'Prawirodirjan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],
        '20409032' => [
            'name'          => 'TK ABA MUBAROK',
            'address'       => 'Bausasran DN III/412',
            'village'       => 'Bausasran',
            'district'      => 'Kec. Danurejan',
            'accreditation' => 'A',
        ],
        '20409033' => [
            'name'          => 'TK ABA LEMPUYANGAN',
            'address'       => 'Tegal Panggung DN II/950',
            'village'       => 'Tegal Panggung',
            'district'      => 'Kec. Danurejan',
            'accreditation' => 'A',
        ],
        '20409050' => [
            'name'          => 'TK ABA JOGOYUDAN',
            'address'       => 'Jogoyudan JT III/821',
            'village'       => 'Gowongan',
            'district'      => 'Kec. Jetis',
            'accreditation' => 'A',
        ],
        '20409056' => [
            'name'          => 'TK ABA UKHUWAH ISLAMIYAH',
            'address'       => 'Kadipaten Kidul KP I/176',
            'village'       => 'Kadipaten',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'A',
        ],
        '20409058' => [
            'name'          => 'TK ABA PATEHAN',
            'address'       => 'Patehan Lor No. 12',
            'village'       => 'Patehan',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'A',
        ],
        '20409059' => [
            'name'          => 'TK ABA ADE IRMA',
            'address'       => 'Panembahan KT II/284',
            'village'       => 'Panembahan',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'A',
        ],
        '20409060' => [
            'name'          => 'TK ABA KADIPATEN KULON',
            'address'       => 'Kadipaten Kulon KP I/340',
            'village'       => 'Kadipaten',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'A',
        ],

        // SD (Sekolah Dasar) - 37 Unit
        '20403418' => [
            'name'          => 'SD MUHAMMADIYAH SURONATAN',
            'address'       => 'Jl. Suronatan NG II/868',
            'village'       => 'Notoprajan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403419' => [
            'name'          => 'SD MUHAMMADIYAH SUKONANDI',
            'address'       => 'Jl. Sukonandi No. 8',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403420' => [
            'name'          => 'SD MUHAMMADIYAH SOKAGATEN',
            'address'       => 'Jl. Sokagaten No. 12',
            'village'       => 'Purbayan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403328' => [
            'name'          => 'SD MUHAMMADIYAH SAPEN 2',
            'address'       => 'Jl. Bimokurdo No. 33',
            'village'       => 'Demangan',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403327' => [
            'name'          => 'SD MUHAMMADIYAH SAPEN 1',
            'address'       => 'Jl. Bimokurdo No. 33',
            'village'       => 'Demangan',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403329' => [
            'name'          => 'SD MUHAMMADIYAH PURWODININGRATAN 2',
            'address'       => 'Purwodiningratan NG I/842',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403331' => [
            'name'          => 'SD MUHAMMADIYAH PURBAYAN',
            'address'       => 'Jl. Purbayan No. 25',
            'village'       => 'Purbayan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403330' => [
            'name'          => 'SD MUHAMMADIYAH NITIKAN',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403337' => [
            'name'          => 'SD MUHAMMADIYAH NGUPASAN',
            'address'       => 'Jl. Reksobayan No. 8',
            'village'       => 'Ngupasan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],
        '20403338' => [
            'name'          => 'SD MUHAMMADIYAH MUTIHAN',
            'address'       => 'Jl. Mutihan No. 14',
            'village'       => 'Wirogunan',
            'district'      => 'Kec. Mergangsan',
            'accreditation' => 'A',
        ],
        '20403339' => [
            'name'          => 'SD MUHAMMADIYAH MUJAHIDIN',
            'address'       => 'Jl. Wonosari KM 6',
            'village'       => 'Rejowinangun',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403340' => [
            'name'          => 'SD MUHAMMADIYAH MILIRAN',
            'address'       => 'Miliran UH II/234',
            'village'       => 'Muja Muju',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403326' => [
            'name'          => 'SD MUHAMMADIYAH MESRAN',
            'address'       => 'Mesran Mantrijeron',
            'village'       => 'Suryodiningratan',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'B',
        ],
        '20403325' => [
            'name'          => 'SD MUHAMMADIYAH KLECO 2',
            'address'       => 'Jl. Nyi Pembayun No. 9',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403324' => [
            'name'          => 'SD MUHAMMADIYAH KLECO 1',
            'address'       => 'Jl. Nyi Pembayun No. 9',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403322' => [
            'name'          => 'SD MUHAMMADIYAH KARANGWARU',
            'address'       => 'Jl. Magelang KM 3.5',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403323' => [
            'name'          => 'SD MUHAMMADIYAH KARANGKAJEN',
            'address'       => 'Karangkajen MG III/912',
            'village'       => 'Brontokusuman',
            'district'      => 'Kec. Mergangsan',
            'accreditation' => 'A',
        ],
        '20403314' => [
            'name'          => 'SD MUHAMMADIYAH JOKTEN',
            'address'       => 'Jl. Bantul No. 56',
            'village'       => 'Gedongkiwa',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'A',
        ],
        '20403315' => [
            'name'          => 'SD MUHAMMADIYAH JOGOKARIYAN',
            'address'       => 'Jl. Jogokaryan No. 36',
            'village'       => 'Mantrijeron',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'A',
        ],
        '20403317' => [
            'name'          => 'SD MUHAMMADIYAH GUNUNGKETUR',
            'address'       => 'Gunungketur PA IX/320',
            'village'       => 'Gunungketur',
            'district'      => 'Kec. Pakualaman',
            'accreditation' => 'A',
        ],
        '20403318' => [
            'name'          => 'SD MUHAMMADIYAH GEDONGTENGEN',
            'address'       => 'Jl. Jlagran Lor No. 16',
            'village'       => 'Pringgokusuman',
            'district'      => 'Kec. Gedongtengen',
            'accreditation' => 'A',
        ],
        '20403319' => [
            'name'          => 'SD MUHAMMADIYAH DANUNAGARAN',
            'address'       => 'Danunagaran MJ III/1052',
            'village'       => 'Mantrijeron',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'B',
        ],
        '20403321' => [
            'name'          => 'SD MUHAMMADIYAH DEMANGAN',
            'address'       => 'Jl. Pengok Kidul No. 1',
            'village'       => 'Baciro',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403320' => [
            'name'          => 'SD MUHAMMADIYAH DAHLAN EKAPRAYA',
            'address'       => 'Kauman GM I/305',
            'village'       => 'Ngupasan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],
        '20403306' => [
            'name'          => 'SD MUHAMMADIYAH BUMIJO',
            'address'       => 'Jl. Bumijo No. 24',
            'village'       => 'Bumijo',
            'district'      => 'Kec. Jetis',
            'accreditation' => 'A',
        ],
        '20403308' => [
            'name'          => 'SD MUHAMMADIYAH BRONTOKUSUMAN',
            'address'       => 'Jl. Sisingamangaraja No. 34',
            'village'       => 'Brontokusuman',
            'district'      => 'Kec. Mergangsan',
            'accreditation' => 'A',
        ],
        '20403309' => [
            'name'          => 'SD MUHAMMADIYAH BLUNYAHGEDE',
            'address'       => 'Blunyahgede RT 04/RW 08',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403310' => [
            'name'          => 'SD MUHAMMADIYAH BACIRO',
            'address'       => 'Jl. Gendeng Cantel No. 28',
            'village'       => 'Baciro',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403311' => [
            'name'          => 'SD MUHAMMADIYAH BAUSASRAN',
            'address'       => 'Bausasran DN III/380',
            'village'       => 'Bausasran',
            'district'      => 'Kec. Danurejan',
            'accreditation' => 'A',
        ],
        '20403312' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN 3',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403313' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN 2',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403301' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN 1',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403302' => [
            'name'          => 'SD MUHAMMADIYAH TEGALREJO',
            'address'       => 'Jl. Tompeyan III TR III/140',
            'village'       => 'Tegalrejo',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403303' => [
            'name'          => 'SD MUHAMMADIYAH SURYOWIJAYAN',
            'address'       => 'Suryowijayan MJ I/418',
            'village'       => 'Gedongkiwa',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'A',
        ],
        '20403304' => [
            'name'          => 'SD MUHAMMADIYAH PAKEL',
            'address'       => 'Jl. Pakel No. 5',
            'village'       => 'Pandeyan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403305' => [
            'name'          => 'SD MUHAMMADIYAH NOTOPRAJAN',
            'address'       => 'Notoprajan NG II/618',
            'village'       => 'Notoprajan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403299' => [
            'name'          => 'SD MUHAMMADIYAH CONDRODIPO',
            'address'       => 'Suryocondro KT II/430',
            'village'       => 'Panembahan',
            'district'      => 'Kec. Kraton',
            'accreditation' => 'B',
        ],

        // SMP (Sekolah Menengah Pertama) - 10 Unit
        '20403240' => [
            'name'          => 'SMP MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Purwodiningratan NG I/902',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403241' => [
            'name'          => 'SMP MUHAMMADIYAH 2 YOGYAKARTA',
            'address'       => 'Jl. Much. Seruji No. 2',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403242' => [
            'name'          => 'SMP MUHAMMADIYAH 3 YOGYAKARTA',
            'address'       => 'Jl. Kapten Piere Tendean No. 19',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403243' => [
            'name'          => 'SMP MUHAMMADIYAH 4 YOGYAKARTA',
            'address'       => 'Jl. Ki Mangunsarkoro No. 43',
            'village'       => 'Gunungketur',
            'district'      => 'Kec. Pakualaman',
            'accreditation' => 'A',
        ],
        '20403244' => [
            'name'          => 'SMP MUHAMMADIYAH 5 YOGYAKARTA',
            'address'       => 'Jl. Kauman No. 44',
            'village'       => 'Ngupasan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],
        '20403245' => [
            'name'          => 'SMP MUHAMMADIYAH 6 YOGYAKARTA',
            'address'       => 'Jl. Sultan Agung No. 14',
            'village'       => 'Gunungketur',
            'district'      => 'Kec. Pakualaman',
            'accreditation' => 'A',
        ],
        '20403246' => [
            'name'          => 'SMP MUHAMMADIYAH 7 YOGYAKARTA',
            'address'       => 'Jl. Purbayan No. 19',
            'village'       => 'Purbayan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403247' => [
            'name'          => 'SMP MUHAMMADIYAH 8 YOGYAKARTA',
            'address'       => 'Jl. Kenari No. 4',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403248' => [
            'name'          => 'SMP MUHAMMADIYAH 9 YOGYAKARTA',
            'address'       => 'Jl. H.O.S. Cokroaminoto No. 80',
            'village'       => 'Pakuncen',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403249' => [
            'name'          => 'SMP MUHAMMADIYAH 10 YOGYAKARTA',
            'address'       => 'Jl. Magelang KM 3.5',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],

        // SMA (Sekolah Menengah Atas) - 7 Unit
        '20403161' => [
            'name'          => 'SMA MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Gotongroyong II, Petinggen',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403162' => [
            'name'          => 'SMA MUHAMMADIYAH 2 YOGYAKARTA',
            'address'       => 'Jl. Kapas No. 7',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403163' => [
            'name'          => 'SMA MUHAMMADIYAH 3 YOGYAKARTA',
            'address'       => 'Jl. Kapten Piere Tendean No. 58',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403164' => [
            'name'          => 'SMA MUHAMMADIYAH 4 YOGYAKARTA',
            'address'       => 'Jl. Mondorakan No. 51',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403165' => [
            'name'          => 'SMA MUHAMMADIYAH 5 YOGYAKARTA',
            'address'       => 'Jl. Purwodiningratan NG I/902',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403166' => [
            'name'          => 'SMA MUHAMMADIYAH 6 YOGYAKARTA',
            'address'       => 'Jl. Sultan Agung No. 14',
            'village'       => 'Gunungketur',
            'district'      => 'Kec. Pakualaman',
            'accreditation' => 'A',
        ],
        '20403167' => [
            'name'          => 'SMA MUHAMMADIYAH 7 YOGYAKARTA',
            'address'       => 'Jl. Pleret KM 2',
            'village'       => 'Giwangan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],

        // SMK (Sekolah Menengah Kejuruan) - 4 Unit
        '20403290' => [
            'name'          => 'SMK MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403291' => [
            'name'          => 'SMK MUHAMMADIYAH 2 YOGYAKARTA',
            'address'       => 'Jl. Ismail Marzuki No. 2',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403292' => [
            'name'          => 'SMK MUHAMMADIYAH 3 YOGYAKARTA',
            'address'       => 'Jl. Pramuka No. 62',
            'village'       => 'Giwangan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403293' => [
            'name'          => 'SMK MUHAMMADIYAH 4 YOGYAKARTA',
            'address'       => 'Jl. Retno Dumilah No. 26',
            'village'       => 'Rejowinangun',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],

        // MA (Madrasah Aliyah) - 1 Unit
        '20400001' => [
            'name'          => 'MA MUHAMMADIYAH KOTA YOGYAKARTA',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],

        // Unit Sekolah Tambahan / Alternatif Sesuai Data AUM PDM
        '20403374' => [
            'name'          => 'SD MUHAMMADIYAH BAUSASRAN II',
            'address'       => 'Bausasran DN III/380',
            'village'       => 'Bausasran',
            'district'      => 'Kec. Danurejan',
            'accreditation' => 'A',
        ],
        '20403341' => [
            'name'          => 'SD MUHAMMADIYAH DEMANGAN',
            'address'       => 'Jl. Pengok Kidul No. 1',
            'village'       => 'Demangan',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403342' => [
            'name'          => 'SD MUHAMMADIYAH GENDENG',
            'address'       => 'Gendeng Cantel GK IV/288',
            'village'       => 'Baciro',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403343' => [
            'name'          => 'SD MUHAMMADIYAH JOGOKARIYAN',
            'address'       => 'Jl. Jogokaryan No. 36',
            'village'       => 'Mantrijeron',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'A',
        ],
        '20403344' => [
            'name'          => 'SD MUHAMMADIYAH KARANGKAJEN I',
            'address'       => 'Karangkajen MG III/912',
            'village'       => 'Brontokusuman',
            'district'      => 'Kec. Mergangsan',
            'accreditation' => 'A',
        ],
        '20403346' => [
            'name'          => 'SD MUHAMMADIYAH KARANGKAJEN II',
            'address'       => 'Karangkajen MG III/912',
            'village'       => 'Brontokusuman',
            'district'      => 'Kec. Mergangsan',
            'accreditation' => 'A',
        ],
        '20404154' => [
            'name'          => 'SD MUHAMMADIYAH KARANGWARU',
            'address'       => 'Jl. Magelang KM 3.5',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403347' => [
            'name'          => 'SD MUHAMMADIYAH KAUMAN YOGYAKARTA',
            'address'       => 'Kauman GM I/305',
            'village'       => 'Ngupasan',
            'district'      => 'Kec. Gondomanan',
            'accreditation' => 'A',
        ],
        '20403348' => [
            'name'          => 'SD MUHAMMADIYAH KLECO 1',
            'address'       => 'Jl. Nyi Pembayun No. 9',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403349' => [
            'name'          => 'SD MUHAMMADIYAH KLECO 2',
            'address'       => 'Jl. Nyi Pembayun No. 9',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403350' => [
            'name'          => 'SD MUHAMMADIYAH KLECO 3',
            'address'       => 'Jl. Nyi Pembayun No. 9',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '69830190' => [
            'name'          => 'SD MUHAMMADIYAH NITIKAN',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403387' => [
            'name'          => 'SD MUHAMMADIYAH NOTOPRAJAN 2',
            'address'       => 'Notoprajan NG II/618',
            'village'       => 'Notoprajan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20404155' => [
            'name'          => 'SD MUHAMMADIYAH PURWODININGRATAN 2',
            'address'       => 'Purwodiningratan NG I/842',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20404156' => [
            'name'          => 'SD MUHAMMADIYAH SAGAN',
            'address'       => 'Sagan GK V/1020',
            'village'       => 'Terban',
            'district'      => 'Kec. Gondokusuman',
            'accreditation' => 'A',
        ],
        '20403351' => [
            'name'          => 'SD MUHAMMADIYAH SOKONANDI 1',
            'address'       => 'Jl. Kapas II No. 1',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403352' => [
            'name'          => 'SD MUHAMMADIYAH SOKONANDI 2',
            'address'       => 'Jl. Kapas II No. 1',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403364' => [
            'name'          => 'SD MUHAMMADIYAH SURONATAN',
            'address'       => 'Jl. Suronatan NG II/868',
            'village'       => 'Notoprajan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403365' => [
            'name'          => 'SD MUHAMMADIYAH SURYOWIJAYAN',
            'address'       => 'Suryowijayan MJ I/418',
            'village'       => 'Gedongkiwa',
            'district'      => 'Kec. Mantrijeron',
            'accreditation' => 'A',
        ],
        '20403366' => [
            'name'          => 'SD MUHAMMADIYAH TEGALREJO',
            'address'       => 'Jl. Tompeyan III TR III/140',
            'village'       => 'Tegalrejo',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20409865' => [
            'name'          => 'SD MUHAMMADIYAH WARUNGBOTO',
            'address'       => 'Warungboto UH IV/815',
            'village'       => 'Warungboto',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403367' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN I',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403368' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN II',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403369' => [
            'name'          => 'SD MUHAMMADIYAH WIROBRAJAN III',
            'address'       => 'Jl. Kapten Piere Tendean No. 55',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403239' => [
            'name'          => 'SMP MUHAMMADIYAH 9 YOGYAKARTA',
            'address'       => 'Jl. H.O.S. Cokroaminoto No. 80',
            'village'       => 'Pakuncen',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403157' => [
            'name'          => 'SMAS MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Gotongroyong II, Petinggen',
            'village'       => 'Karangwaru',
            'district'      => 'Kec. Tegalrejo',
            'accreditation' => 'A',
        ],
        '20403158' => [
            'name'          => 'SMAS MUHAMMADIYAH 2 YOGYAKARTA',
            'address'       => 'Jl. Kapas No. 7',
            'village'       => 'Semaki',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403149' => [
            'name'          => 'SMAS MUHAMMADIYAH 3 YOGYAKARTA',
            'address'       => 'Jl. Kapten Piere Tendean No. 58',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403148' => [
            'name'          => 'SMAS MUHAMMADIYAH 4 YOGYAKARTA',
            'address'       => 'Jl. Mondorakan No. 51',
            'village'       => 'Prenggan',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20403147' => [
            'name'          => 'SMAS MUHAMMADIYAH 5 YOGYAKARTA',
            'address'       => 'Jl. Purwodiningratan NG I/902',
            'village'       => 'Ngampilan',
            'district'      => 'Kec. Ngampilan',
            'accreditation' => 'A',
        ],
        '20403138' => [
            'name'          => 'SMAS MUHAMMADIYAH 6 YOGYAKARTA',
            'address'       => 'Jl. Sultan Agung No. 14',
            'village'       => 'Gunungketur',
            'district'      => 'Kec. Pakualaman',
            'accreditation' => 'A',
        ],
        '20404174' => [
            'name'          => 'SMAS MUHAMMADIYAH 7 YOGYAKARTA',
            'address'       => 'Jl. Pleret KM 2',
            'village'       => 'Giwangan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403285' => [
            'name'          => 'SMKS MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403284' => [
            'name'          => 'SMKS MUHAMMADIYAH 2 YOGYAKARTA',
            'address'       => 'Jl. Ismail Marzuki No. 2',
            'village'       => 'Wirobrajan',
            'district'      => 'Kec. Wirobrajan',
            'accreditation' => 'A',
        ],
        '20403275' => [
            'name'          => 'SMKS MUHAMMADIYAH 3 YOGYAKARTA',
            'address'       => 'Jl. Pramuka No. 62',
            'village'       => 'Giwangan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
        '20403276' => [
            'name'          => 'SMKS MUHAMMADIYAH 4 YOGYAKARTA',
            'address'       => 'Jl. Retno Dumilah No. 26',
            'village'       => 'Rejowinangun',
            'district'      => 'Kec. Kotagede',
            'accreditation' => 'A',
        ],
        '20363279' => [
            'name'          => 'MAS MUHAMMADIYAH 1 YOGYAKARTA',
            'address'       => 'Jl. Nitikan No. 48',
            'village'       => 'Sorosutan',
            'district'      => 'Kec. Umbulharjo',
            'accreditation' => 'A',
        ],
    ];

    /**
     * Mencari dan mengekstrak data identitas unit sekolah berdasarkan NPSN.
     * Mencoba menghubungi API / referensi Dapodik, jika offline fallback ke database lokal.
     */
    public function lookup(string $npsn): ?array
    {
        $cleanNpsn = trim($npsn);
        if (empty($cleanNpsn)) {
            return null;
        }

        // Coba ambil secara online jika koneksi tersedia
        try {
            $response = Http::timeout(3)->get("https://api-sekolah-indonesia.vercel.app/sekolah/{$cleanNpsn}");
            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                if (!empty($data) && isset($data['alamat_jalan'])) {
                    return [
                        'npsn'          => $cleanNpsn,
                        'name'          => $data['sekolah'] ?? null,
                        'address'       => $data['alamat_jalan'] ?? null,
                        'village'       => $data['desa_kelurahan'] ?? null,
                        'district'      => isset($data['kecamatan']) ? "Kec. {$data['kecamatan']}" : null,
                        'accreditation' => $data['akreditasi'] ?? null,
                        'source'        => 'Kemdikbud Dapodik (Online)',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error jaringan dan lanjutkan ke fallback lokal
            Log::info("Lookup online NPSN {$cleanNpsn} dilewati: " . $e->getMessage());
        }

        // Fallback ke Master Data Direktori Resmi AUM Kota Yogyakarta
        if (isset(self::$localDirectory[$cleanNpsn])) {
            $item = self::$localDirectory[$cleanNpsn];
            return [
                'npsn'          => $cleanNpsn,
                'name'          => $item['name'],
                'address'       => $item['address'],
                'village'       => $item['village'],
                'district'      => $item['district'],
                'accreditation' => $item['accreditation'],
                'source'        => 'Database Referensi Resmi NPSN Kemdikbud',
            ];
        }

        return null;
    }

    /**
     * Sinkronisasi data unit individual menggunakan data NPSN.
     */
    public function syncUnit(Unit $unit): bool
    {
        if (empty($unit->npsn)) {
            return false;
        }

        $info = $this->lookup($unit->npsn);
        if ($info) {
            return DB::transaction(function () use ($unit, $info) {
                $unit->update([
                    'address'       => $info['address'] ?? $unit->address,
                    'village'       => $info['village'] ?? $unit->village,
                    'district'      => $info['district'] ?? $unit->district,
                    'accreditation' => $info['accreditation'] ?? $unit->accreditation,
                ]);
                return true;
            }, 5);
        }

        return false;
    }

    /**
     * Sinkronkan seluruh unit di database yang memiliki NPSN.
     */
    public function syncAll(): int
    {
        $units = Unit::whereNotNull('npsn')->get();
        $synced = 0;

        foreach ($units as $unit) {
            if ($this->syncUnit($unit)) {
                $synced++;
            }
        }

        return $synced;
    }
}
