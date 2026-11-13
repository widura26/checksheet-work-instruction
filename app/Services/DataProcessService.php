<?php

namespace App\Services;

use App\Models\DaftarKebutuhanMaterial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    private const SORTABLE = [
        'no', 'group', 'tindak_lanjut', 'deskripsi', 'kebutuhan', 'qty_pr',
        'kode_material', 'trainset', 'status_pr', 'proyek',
    ];

    private const NUMERIC_SORTS = ['kebutuhan', 'qty_pr'];

    private const DATASET_CACHE_TTL = 300;

    private const ROW_COLUMNS = [
        'wbs',
        'no',
        'group',
        'lot',
        'kebutuhan',
        'sum',
        'sudah_pr',
        'tindak_lanjut',
        'deskripsi_material',
        'kode_material',
    ];

    private function validRowsQuery(): Builder {
        $group = DaftarKebutuhanMaterial::query()->getGrammar()->wrap('group');

        return DaftarKebutuhanMaterial::query()
            ->whereNotNull('group')
            ->whereRaw("TRIM($group) <> ''");
    }

    private function filteredQuery(string $wbs, string $group, string $trainset): Builder {
        $query = $this->validRowsQuery()->select(self::ROW_COLUMNS);

        if ($wbs !== 'Semua')      $query->where('wbs', $wbs);
        if ($group !== 'Semua')    $query->where('group', $group);
        if ($trainset !== 'Semua') $query->where('lot', $trainset);

        return $query;
    }

    private function normalizeRow(DaftarKebutuhanMaterial $row): array {
        $data = $row->toArray();
        $tindakLanjut = (string) ($data['tindak_lanjut'] ?? '');
        $data['tindak_lanjut'] = self::STATUS_RENAME[$tindakLanjut] ?? $tindakLanjut;
        $data['trainset'] = trim((string) ($data['lot'] ?? ''));
        $data['no'] = str_replace(',', '.', trim((string) ($data['no'] ?? '')));
        $data['raw_count'] = 1;

        return $data;
    }

    public function filterOptions(?Collection $rows = null): array {
        if ($rows !== null) {
            $opt = fn ($col) => $rows->pluck($col)->filter()->unique()->sort(SORT_NATURAL)->values()->all();

            return [
                'wbs'      => $opt('wbs'),
                'group'    => $opt('group'),
                'trainset' => $opt('trainset'),
            ];
        }

        $distinct = function (string $column): array {
            return $this->validRowsQuery()
                ->whereNotNull($column)
                ->where($column, '<>', '')
                ->distinct()
                ->orderBy($column)
                ->pluck($column)
                ->filter(fn ($value) => trim((string) $value) !== '')
                ->sort(SORT_NATURAL)
                ->values()
                ->all();
        };

        return [
            'wbs'      => $distinct('wbs'),
            'group'    => $distinct('group'),
            'trainset' => $distinct('lot'),
        ];
    }

    public function dashboard(string $wbs, string $group, string $trainset): array {
        $usePr = $wbs === 'Semua' || $group === 'Semua' || $trainset === 'Semua';
        $totalTrainset = $wbs === 'Semua' || $group === 'Semua';

        $query = $this->filteredQuery($wbs, $group, $trainset);
        $frame = $usePr
            ? $this->collapseQuery($query, $totalTrainset)
            : $this->rawFrame($query);

        $statusCol    = $usePr ? 'status_pr' : 'tindak_lanjut';
        $statusOrder  = $usePr ? self::STATUS_PR_ORDER : self::STATUS_ORDER;
        $statusColors = $usePr ? self::STATUS_PR_COLORS : self::STATUS_COLORS;

        return [
            'frame'        => $frame,
            'statusCol'    => $statusCol,
            'statusOrder'  => $statusOrder,
            'statusColors' => $statusColors,
            'counts'       => $this->countByStatus($frame, $statusCol, $statusOrder),
            'totalItem'    => $frame->sum(fn ($row) => (int) ($row['raw_count'] ?? 1)),
            'groupedItem'  => $frame->count(),
            'jumlahMaterial' => $frame->pluck('kode_material')->unique()->count(),
        ];
    }

    private function rawFrame(Builder $query): Collection {
        return $query->cursor()
            ->map(fn (DaftarKebutuhanMaterial $row) => $this->normalizeRow($row))
            ->collect()
            ->values();
    }

    /**
     * Stream rows and collapse them by No + Kode Material + Group.
     *
     * Only the collapsed groups are kept in memory (not every raw row), so the
     * dashboard stays usable when the imported file grows.
     */
    private function collapseQuery(Builder $query, bool $totalTrainset): Collection {
        $groups = [];

        foreach ($query->cursor() as $row) {
            $data = $this->normalizeRow($row);
            $key = implode('|', [$data['no'], $data['kode_material'], $data['group']]);

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'first'     => $data,
                    'tl'        => [],
                    'trainsets' => [],
                    'kebutuhan' => 0.0,
                    'qty_pr'    => 0.0,
                    'raw_count' => 0,
                ];
            }

            $groups[$key]['raw_count']++;

            if (trim((string) $data['tindak_lanjut']) !== '') {
                $groups[$key]['tl'][$data['tindak_lanjut']] = true;
            }

            if ($data['trainset'] !== '') {
                $groups[$key]['trainsets'][$data['trainset']] = true;
            }

            $groups[$key]['kebutuhan'] += (float) ($data['sum'] ?? $data['kebutuhan'] ?? 0);
            $groups[$key]['qty_pr'] += (float) ($data['sudah_pr'] ?? 0);
        }

        $rows = [];

        foreach ($groups as $group) {
            $first = $group['first'];
            $tl = array_keys($group['tl']);

            usort($tl, fn ($a, $b) => $totalTrainset
                ? strcmp($a, $b)
                : strcmp($this->tlSortKey($a), $this->tlSortKey($b)));

            $trainsets = array_keys($group['trainsets']);
            usort($trainsets, fn ($a, $b) => $this->trainsetNo($a) <=> $this->trainsetNo($b));

            $tlJoined = implode(', ', $tl);

            $rows[] = [
                'no'                 => $first['no'],
                'group'              => $first['group'],
                'tindak_lanjut'      => $tlJoined,
                'deskripsi_material' => $first['deskripsi_material'],
                'kebutuhan'          => $group['kebutuhan'],
                'qty_pr_bom'         => $group['qty_pr'],
                'kode_material'      => $first['kode_material'],
                'trainset'           => implode(', ', $trainsets),
                'status_pr'          => $this->checkPrStatus($tlJoined),
                'raw_count'          => $group['raw_count'],
            ];
        }

        return collect($rows);
    }

    public function countByStatus(Collection $frame, string $statusCol, array $order): array {
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

    public function checkPrStatus(?string $text): string {
        if ($text === null || trim($text) === '') {
            return 'BELUM PR';
        }

        $arr = collect(explode(',', $text))
            ->map(fn ($s) => strtoupper(trim($s)))
            ->unique()->values()->all();
        $has = fn ($s) => in_array($s, $arr, true);

        $hasPr = $has('PR BARU') || $has('PR EKSISTING');
        $hasWaitingPr = $has('BELUM PR') || $has('MENUNGGU EVALUASI PEMAKAIAN');
        $hasStock = $has('MENGGUNAKAN STOK');

        if ($hasPr && $hasWaitingPr)                                                       return 'PR PARSIAL';
        if ($hasStock && $hasPr)                                                           return 'STOK DAN PR';
        if ($arr === ['MENGGUNAKAN STOK'])                                                 return 'STOK';
        if ($hasPr)                                                                        return 'SUDAH PR';
        if ($hasWaitingPr)                                                                 return 'BELUM PR';

        return 'STATUS LAIN / TIDAK DIKENAL';
    }

    private function tlSortKey(string $t): string {
        $p = preg_split('/\s+/', trim($t));
        return count($p) > 1 ? $p[1] : $t;
    }

    private function trainsetNo(string $t): int {
        return preg_match('/\d+/', $t, $m) ? (int) $m[0] : 0;
    }

    public function dataset(string $wbs, string $group, string $trainset): array {
        $version = (int) Cache::get('mp:version', 1);
        $key = 'mp:dataset:v' . $version . ':' . md5($wbs . '|' . $group . '|' . $trainset);

        return Cache::remember(
            $key,
            self::DATASET_CACHE_TTL,
            fn () => $this->buildDataset($wbs, $group, $trainset)
        );
    }

    private function buildDataset(string $wbs, string $group, string $trainset): array {
        return $this->dashboard($wbs, $group, $trainset)['frame']
            ->map(fn ($r) => [
                'no'            => $r['no'],
                'group'         => $r['group'],
                'tindak_lanjut' => $r['tindak_lanjut'],
                'deskripsi'     => $r['deskripsi_material'],
                'kebutuhan'     => $r['sum'] ?? $r['kebutuhan'] ?? 0,
                'qty_pr'        => $r['qty_pr_bom'] ?? ($r['sudah_pr'] ?? 0),
                'kode_material' => $r['kode_material'],
                'trainset'      => $r['trainset'],
                'status_pr'     => $r['status_pr'] ?? $this->checkPrStatus($r['tindak_lanjut']),
                'proyek'        => $wbs !== 'Semua' ? $wbs : ($r['wbs'] ?? ''),
                'raw_count'     => (int) ($r['raw_count'] ?? 1),
            ])
            ->values()
            ->all();
    }

    public function filteredDataset(
        string $wbs,
        string $group,
        string $trainset,
        array $statuses = [],
        string $search = '',
        string $sort = 'no',
        string $direction = 'asc'
    ): Collection {
        $rows = collect($this->dataset($wbs, $group, $trainset));

        if (!empty($statuses)) {
            $rows = $rows->filter(fn ($r) => in_array($r['status_pr'], $statuses, true))->values();
        }

        if (trim($search) !== '') {
            $rows = $rows->filter(fn ($r) => $this->matchesSearch($r, $search))->values();
        }

        return $this->sortRows($rows, $sort, $direction);
    }

    public function paginateDataset(
        string $wbs,
        string $group,
        string $trainset,
        array $statuses = [],
        string $search = '',
        string $sort = 'no',
        string $direction = 'asc',
        int $page = 1,
        int $perPage = 10
    ): array {
        $rows = collect($this->dataset($wbs, $group, $trainset));

        if (!empty($statuses)) {
            $rows = $rows->filter(fn ($r) => in_array($r['status_pr'], $statuses, true))->values();
        }

        $summary = $this->summarize($rows);

        if (trim($search) !== '') {
            $rows = $rows->filter(fn ($r) => $this->matchesSearch($r, $search))->values();
        }

        $rows = $this->sortRows($rows, $sort, $direction);

        $perPage = max(1, min($perPage, 100));
        $total = $rows->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $lastPage));

        return [
            'data'    => $rows->slice(($page - 1) * $perPage, $perPage)->values()->all(),
            'meta'    => [
                'page'      => $page,
                'per_page'  => $perPage,
                'total'     => $total,
                'last_page' => $lastPage,
            ],
            'summary' => $summary,
        ];
    }

    public function summarize(Collection $rows): array {
        $komponen = (string) config('bom-sheets.groups.komponen', 'KOMPONEN');
        $piecePart = (string) config('bom-sheets.groups.piece_part', 'PIECE PART');

        $matrix = [];
        $totals = [];
        $rawTotal = 0;

        foreach (self::STATUS_PR_ORDER as $status) {
            $matrix[$status] = ['komponen' => 0, 'piece_part' => 0];
            $totals[$status] = 0;
        }

        $groupCounts = [];

        foreach ($rows as $row) {
            $status = $row['status_pr'] ?? 'STATUS LAIN / TIDAK DIKENAL';
            $rawCount = (int) ($row['raw_count'] ?? 1);

            if (!isset($matrix[$status])) {
                $matrix[$status] = ['komponen' => 0, 'piece_part' => 0];
                $totals[$status] = 0;
            }

            $bucket = $row['group'] === $komponen ? 'komponen' : 'piece_part';
            $matrix[$status][$bucket] += $rawCount;
            $totals[$status] += $rawCount;
            $rawTotal += $rawCount;

            $group = $row['group'] ?: '(kosong)';
            $groupCounts[$group] = ($groupCounts[$group] ?? 0) + $rawCount;
        }

        ksort($groupCounts);

        return [
            'totalItem'      => $rawTotal,
            'groupedItem'    => $rows->count(),
            'jumlahMaterial' => $rows->pluck('kode_material')->filter()->unique()->count(),
            'statusMatrix'   => $matrix,
            'statusTotals'   => $totals,
            'groupCounts'    => $groupCounts,
            'groupLabels'    => [
                'komponen'   => $komponen,
                'piece_part' => $piecePart,
            ],
        ];
    }

    private function matchesSearch(array $row, string $search): bool {
        $needle = mb_strtolower(trim($search));

        if ($needle === '') {
            return true;
        }

        foreach (['no', 'group', 'tindak_lanjut', 'deskripsi', 'kode_material', 'trainset', 'status_pr', 'proyek'] as $key) {
            if (str_contains(mb_strtolower((string) ($row[$key] ?? '')), $needle)) {
                return true;
            }
        }

        return str_contains((string) ($row['kebutuhan'] ?? ''), $needle)
            || str_contains((string) ($row['qty_pr'] ?? ''), $needle);
    }

    private function sortRows(Collection $rows, string $sort, string $direction): Collection {
        if (!in_array($sort, self::SORTABLE, true)) {
            $sort = 'no';
        }

        $descending = strtolower($direction) === 'desc';

        return $rows->sort(function (array $a, array $b) use ($sort, $descending) {
            $left = $a[$sort] ?? null;
            $right = $b[$sort] ?? null;

            if (in_array($sort, self::NUMERIC_SORTS, true)) {
                $comparison = (float) $left <=> (float) $right;
            } else {
                $comparison = strnatcasecmp((string) $left, (string) $right);
            }

            return $descending ? -$comparison : $comparison;
        })->values();
    }
}
