<?php

return [
    'docs' => [
        'entry' => env('ERP_ENTRY_DOCUMENT', 'FGR'),
        'entry_series' => env('ERP_ENTRY_SERIES', 'PV'),
    ],

    'default_warehouse_id' => env('ERP_DEFAULT_WAREHOUSE_ID', 1),
    'default_salesman_id' => env('ERP_DEFAULT_SALESMAN_ID', 1),

    // Fallbacks apenas para quando o fornecedor Sage não tiver estes dados definidos.
    'default_payment_id' => env('ERP_DEFAULT_PAYMENT_ID'),
    'default_tender_id' => env('ERP_DEFAULT_TENDER_ID'),
];
