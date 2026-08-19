<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tools;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $jenisList = ['tool', 'consumable', 'apd'];

        $rawData = <<<'DATA'
22B22BI10020XXX00	2100	PC	Bolt Head Socket, SUS M10 x 20mm	
A15CW0460-1020	2100	STK	ROUND BAR	 ASTM A576 GR 1020_DIA: 46 X 6000
22D17KR00015XXX08	2100	PC	Skun Ring Insulated, 1,5mmsq M8	
D76AB1201	2100	PC	BASE HOLDER MT 3	 MT3T-Q26-45
D76AB1301	2100	PC	STEPPED SPACER	 SP26-12-30
D76AB1401	2100	PC	BORING HEAD	 12-ZMAC-25-40V
22D17KW00250XXK00	2100	M	Heatshrink Tube, 25mm Black	
22B85QY20004XXX00	2100	PC	Clamp Conduit Pipe type omega M34	
22B58TH04266MDX01	2100	PC	ST STM32F042K6T6	
D78UC01400	2100	PC	REFILL KUAS ROL CAT	 UKURAN 4 INCH ( DIAMETER 3 CM, PANJANG 10 CM )
22C07BA90256XXX00	2100	SET	Crimping Dies DR-0256BJ	
22D02BA22030XXX01	2100	KG	Cat Sekunder, Powder Coating RAL 9005 *	 Sand Tex Glosiness <(><<)>20%  (!!!)
D78UC02001	2100	PC	ROL CAT KECIL	 PANJANG 60 CM, LEBAR ROL 10 CM
22E04BE06038XXX01	2100	SET	EURO ASIA NC Multi-Functional Bus Bar *	 Machine BPM-603NC-8S
22B22BI01035XXX00	2100	PC	Bolt Head Socket , SUS M10 x 35	
D31WH01082	2100	L	ENGINE OIL CUTSOL SP	 JUMBO_@20L
C94US11001	2100	UN	ENGINE PENGGERAK SOLAR + PULLY	 KAPASITAS : 3000 RPM
DATA;

        foreach (explode("\n", trim($rawData)) as $line) {
            $parts = preg_split('/\s+/', $line, 4); // pecah 4 kolom

            if (count($parts) < 4) {
                continue;
            }

            [$kode, $ignore, $satuan, $descSpec] = $parts;

            // Pecah deskripsi dan spesifikasi (jika ada tab atau double space)
            $split = preg_split("/\s{2,}|\t/", $descSpec);
            $deskripsi = trim($split[0] ?? '');
            $spesifikasi = trim($split[1] ?? '');
            $projectId = Project::withoutGlobalScope('onlyWithStock')->inRandomOrder()->value('project_id');
            $tool = Tools::create([
                'kode_material' => $kode,
                'public_id' => \Illuminate\Support\Str::uuid(),
                'deskripsi' => $deskripsi,
                'spesifikasi' => $spesifikasi,

                'satuan' => $satuan,
                'vendor' => 'vendor',
                'keterangan_vendor' => null, // Add this line
                'posisi_ct_id' => rand(1, 2),
                'tanggal_ditambahkan' => now(),
                'jenis_material' => $jenisList[array_rand($jenisList)],
            ]);
            $tool->stockProject()->attach($projectId, [
                'qty' => 10,
                'posisi_ct_id' => $tool->posisi_ct_id,
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
