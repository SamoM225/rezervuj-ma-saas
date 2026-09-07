<?php

return [
    /*
    | Where temporary slot holds live while a customer fills in the form.
    | Any Laravel cache store works: "redis" (recommended, tiny footprint),
    | "database" (cache + cache_locks tables; fine for shared hosting such as
    | Websupport) or "file". Defaults to the app's cache store.
    */
    'hold_store' => env('SLOT_HOLD_STORE', env('CACHE_STORE', 'database')),
];
