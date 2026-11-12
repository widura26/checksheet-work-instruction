<?php

namespace App\Services;

use App\Models\DaftarKebutuhanMaterial;
use Illuminate\Support\Collection;

class DataProcessService {
    public const STATUS_ORDER = [
        'PR EKSISTING', 'PR BARU', 'BELUM PR', 'MENUNGGU KOMAT',
        'MENGGUNAKAN STOK', 'MENUNGGU EVALUASI PEMAKAIAN',
    ];

    public const STATUS_PR_ORDER = [
        'BELUM PR', 'PR PARSIAL', 'SUDAH PR', 'STOK DAN PR', 'STOK',
        'STATUS LAIN / TIDAK DIKENAL',
    ];

    public const STATUS_COLORS = [
        'PR EKSISTING' => '#636EFA', 'PR BARU' => '#00CC96', 'BELUM PR' => '#EF553B',
        'MENUNGGU KOMAT' => '#FFA15A', 'MENGGUNAKAN STOK' => '#AB63FA',
        'MENUNGGU EVALUASI PEMAKAIAN' => '#FF9896',
    ];

    public const STATUS_PR_COLORS = [
        'BELUM PR' => '#EF553B', 'PR PARSIAL' => '#FFA15A', 'SUDAH PR' => '#00CC96',
        'STOK DAN PR' => '#636EFA', 'STOK' => '#AB63FA',
        'STATUS LAIN / TIDAK DIKENAL' => '#7F7F7F',
    ];

    private const STATUS_RENAME = ['MENGGUNAKAN STOK GUDANG' => 'MENGGUNAKAN STOK'];

    public function prepare(): Collection
    {
        $group = DaftarKebutuhanMaterial::query()->getGrammar()->wrap('group');

        return DaftarKebutuhanMaterial::query()
            ->whereNotNull('tindak_lanjut')
            ->whereRaw("TRIM(tindak_lanjut) <> ''")
            ->whereNotNull('group')
            ->whereRaw("TRIM($group) <> ''")
            ->get()
            ->map(function ($row) {
                $r = $row->toArray();
                $r['tindak_lanjut'] = self::STATUS_RENAME[$r['tindak_lanjut']] ?? $r['tindak_lanjut'];
                $r['trainset'] = trim((string) ($r['lot'] ?? ''));
                return $r;
            });
    }

    public function filterOptions(Collection $rows): array
    {
        $opt = fn ($col) => $rows->pluck($col)->filter()->unique()->sort(SORT_NATURAL)->values()->all();

        return [
            'wbs'      => $opt('wbs'),
            'group'    => $opt('group'),
            'trainset' => $opt('trainset'),
        ];
    }

    public function dashboard(string $wbs, string $group, string $trainset): array
    {
        $data = $this->prepare();

        if ($wbs !== 'Semua')      $data = $data->where('wbs', $wbs);
        if ($group !== 'Semua')    $data = $data->where('group', $group);
        if ($trainset !== 'Semua') $data = $data->where('trainset', $trainset);

        if ($wbs === 'Semua' || $group === 'Semua') {
            $frame = $this->collapse($data, totalTrainset: true);
            $usePr = true;
        } elseif ($trainset === 'Semua') {
            $frame = $this->collapse($data, totalTrainset: false);
            $usePr = true;
        } else {
            $frame = $data->values();
            $usePr = false;
        }

        $statusCol    = $usePr ? 'status_pr' : 'tindak_lanjut';
        $statusOrder  = $usePr ? self::STATUS_PR_ORDER : self::STATUS_ORDER;
        $statusColors = $usePr ? self::STATUS_PR_COLORS : self::STATUS_COLORS;

        return [
            'frame'        => $frame,
            'statusCol'    => $statusCol,
            'statusOrder'  => $statusOrder,
            'statusColors' => $statusColors,
            'counts'       => $this->countByStatus($frame, $statusCol, $statusOrder),
            'totalItem'    => $frame->count(),
            'jumlahMaterial' => $frame->pluck('kode_material')->unique()->count(),
        ];
    }

    public function countByStatus(Collection $frame, string $statusCol, array $order): array
    {
        $groups = $frame->pluck('group')->unique()->sort()->values()->all();
        $tally  = $frame->countBy(fn ($r) => $r[$statusCol] . '|' . $r['group']);

        $matrix = [];
        foreach ($order as $status) {
            foreach ($groups as $g) {
                $matrix[$status][$g] = $tally[$status . '|' . $g] ?? 0;
            }
        }

        return ['groups' => $groups, 'matrix' => $matrix];
    }

    public function collapse(Collection $data, bool $totalTrainset = false): Collection
    {
        return $data
            ->map(function ($r) {
                $r['no'] = str_replace(',', '.', trim((string) $r['no']));
                return $r;
            })
            ->groupBy(fn ($r) => implode('|', [$r['no'], $r['kode_material'], $r['group']]))
            ->map(function (Collection $g) use ($totalTrainset) {
                $first = $g->first();

                $tl = $g->pluck('tindak_lanjut')->unique()->values()->all();
                usort($tl, fn ($a, $b) => $totalTrainset
                    ? strcmp($a, $b)
                    : strcmp($this->tlSortKey($a), $this->tlSortKey($b)));

                $trainset = $g->pluck('trainset')->filter()->unique()->values()->all();
                usort($trainset, fn ($a, $b) => $this->trainsetNo($a) <=> $this->trainsetNo($b));

                $kebutuhan = $g->sum(fn ($r) => $r['sum'] ?? $r['kebutuhan'] ?? 0);
                $qtyPr     = $g->sum(fn ($r) => ($r['qty_pr_ve'] ?? 0) + ($r['qty_pr_ms'] ?? 0));

                $tlJoined = implode(', ', $tl);

                return [
                    'no'                     => $first['no'],
                    'group'                  => $first['group'],
                    'tindak_lanjut'          => $tlJoined,
                    'deskripsi_material'     => $first['deskripsi_material'],
                    'kebutuhan'              => $kebutuhan,
                    'qty_pr_bom'             => $qtyPr,
                    'kode_material'          => $first['kode_material'],
                    'trainset'               => implode(', ', $trainset),
                    'status_pr'              => $this->checkPrStatus($tlJoined),
                ];
            })
            ->values();
    }

    public function checkPrStatus(?string $text): string
    {
        if ($text === null || trim($text) === '') {
            return 'BELUM PR';
        }

        $arr = collect(explode(',', $text))
            ->map(fn ($s) => strtoupper(trim($s)))
            ->unique()->values()->all();
        $has = fn ($s) => in_array($s, $arr, true);

        if ($has('PR BARU') && ($has('BELUM PR') || $has('MENUNGGU EVALUASI PEMAKAIAN'))) return 'PR PARSIAL';
        if ($has('MENGGUNAKAN STOK') && ($has('PR BARU') || $has('PR EKSISTING')))         return 'STOK DAN PR';
        if ($arr === ['MENGGUNAKAN STOK'])                                                 return 'STOK';
        if ($has('PR BARU') || $has('PR EKSISTING'))                                       return 'SUDAH PR';
        if ($has('BELUM PR') || $has('MENUNGGU EVALUASI PEMAKAIAN'))                       return 'BELUM PR';

        return 'STATUS LAIN / TIDAK DIKENAL';
    }

    private function tlSortKey(string $t): string
    {
        $p = preg_split('/\s+/', trim($t));
        return count($p) > 1 ? $p[1] : $t;
    }

    private function trainsetNo(string $t): int
    {
        return preg_match('/\d+/', $t, $m) ? (int) $m[0] : 0;
    }

    public function dataset(string $wbs, string $group, string $trainset): array
    {
        return $this->dashboard($wbs, $group, $trainset)['frame']
            ->map(fn ($r) => [
                'no'            => $r['no'],
                'group'         => $r['group'],
                'tindak_lanjut' => $r['tindak_lanjut'],
                'deskripsi'     => $r['deskripsi_material'],
                'kebutuhan'     => $r['sum'] ?? $r['kebutuhan'] ?? 0,
                'qty_pr'        => $r['qty_pr_bom'] ?? (($r['qty_pr_ve'] ?? 0) + ($r['qty_pr_ms'] ?? 0)),
                'kode_material' => $r['kode_material'],
                'trainset'      => $r['trainset'],
                // mode baris mentah belum punya status_pr -> hitung dari tindak_lanjut-nya
                'status_pr'     => $r['status_pr'] ?? $this->checkPrStatus($r['tindak_lanjut']),
                'proyek'        => $wbs !== 'Semua' ? $wbs : ($r['wbs'] ?? ''),
            ])
            ->values()
            ->all();
    }
}

?>
