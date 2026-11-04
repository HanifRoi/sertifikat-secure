<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Participant;
use Faker\Factory as Faker;

class ParticipantSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $kampus = [
            'Universitas Putra Bangsa',
            'Universitas Jenderal Soedirman',
            'Universitas Amikom Purwokerto',
            'Institut Teknologi Telkom Purwokerto',
            'Universitas Muhammadiyah Purwokerto',
        ];

        $smk = [
            'SMK Telkom Purwokerto',
            'SMK N 1 Purwokerto',
            'SMK N 2 Purwokerto',
            'SMK N 1 Banyumas',
        ];

        $jurusanKampus = ['Ilmu Komputer', 'Teknik Informatika', 'Sistem Informasi'];
        $jurusanSmk = ['Rekayasa Perangkat Lunak', 'Teknik Komputer Jaringan', 'Multimedia'];

        for($i = 0; $i < 30; $i++) {
            $isKampus = $faker->boolean(50); // 50% peluang Kampus, 50% SMK

            $instansi = $isKampus ? $faker->randomElement($kampus) : $faker->randomElement($smk);
            $jurusan = $isKampus ? $faker->randomElement($jurusanKampus) : $faker->randomElement($jurusanSmk);
            $program = $isKampus ? 'Magang Industri' : 'Praktik Kerja Lapangan (PKL)';

            $startDate = $faker->dateTimeBetween('2026-01-01', '2026-02-28')->format('Y-m-d');
            $endDate = $faker->dateTimeBetween('2026-04-01', '2026-05-31')->format('Y-m-d');

            Participant::create([
                'identifier_number' => $faker->unique()->numerify('#######'),
                // GANTI DI SINI: Gabungkan nama depan dan belakang agar bebas dari gelar akademis
                'name'              => $faker->firstName . ' ' . $faker->lastName,
                'institution'       => $instansi,
                'study_program'     => $jurusan,
                'program'           => $program,
                'start_date'        => $startDate,
                'end_date'          => $endDate,
            ]);
        }
    }
}