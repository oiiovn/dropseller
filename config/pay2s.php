<?php

return [
    'transactions_url' => env('PAY2S_TRANSACTIONS_URL', 'https://my.pay2s.vn/userapi/transactions'),
    'secret_key' => env('PAY2S_SECRET_KEY', '1ece1f568539eeb7b971578c32a317369defbf0e64b8336124bdc47399a3419e'),
    'bank_accounts' => env('PAY2S_BANK_ACCOUNTS', '008338298888,46241987'),
    'fetch_begin' => env('PAY2S_FETCH_BEGIN', '22/08/2025'),
    'fetch_end' => env('PAY2S_FETCH_END', '20/11/2029'),
];
