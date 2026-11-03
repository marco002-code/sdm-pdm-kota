<?php

namespace App\Console\Commands;

use App\Services\NpsnLookupService;
use Illuminate\Console\Command;

class SyncUnitNpsnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unit:sync-npsn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan dan lengkapi Identitas Unit Kerja (Alamat, Kelurahan, Kecamatan, Akreditasi) by NPSN';

    /**
     * Execute the console command.
     */
    public function handle(NpsnLookupService $service): int
    {
        $this->info('Memulai sinkronisasi identitas unit kerja by NPSN...');
        $count = $service->syncAll();
        $this->info("Berhasil menyinkronkan {$count} unit kerja AUM!");

        return Command::SUCCESS;
    }
}
