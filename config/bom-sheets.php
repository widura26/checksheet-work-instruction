<?php

return [
    'sheets' => [
        'PR' => [
            'label' => 'Purchase Requisition',
            'headers' => [
                'takt' => ['takt', 'Takt'],
                'rev_bom' => ['rev. bom', 'rev_bom', 'revisi'],
                'no' => ['no', 'nomor'],
                'kode_material' => ['kode material', 'kode_material', 'material'],
                'kode_material_stock' => ['kode material stock', 'kode_material_stock', 'stock'],
            ],
            'required' => ['kode_material'],
        ],
        'PO' => [
            'label' => 'Purchase Order',
            'headers' => [
                'takt' => ['takt'],
                'rev_bom' => ['rev. bom'],
                'no' => ['no'],
                'kode_material' => ['kode material'],
                'kode_material_stock' => ['kode material stock'],
            ],
            'required' => ['kode_material'],
        ],
    ],
];
