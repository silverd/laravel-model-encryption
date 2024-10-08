<?php

return [

    // 加解密算法
    'cipher' => env('XES_ENCRYPT_CIPHER', 'aes-256-cbc'),

    // 32位密钥
    'key' => env('XES_ENCRYPT_KEY'),

    // 16位向量
    'iv' => env('XES_ENCRYPT_IV', '1234567890abcdef'),

];
