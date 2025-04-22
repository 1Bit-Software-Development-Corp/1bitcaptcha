<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Captcha Character Set
    |--------------------------------------------------------------------------
    |
    | The characters that will be used to generate the captcha code.
    |
    */
    'charset' => 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789',

    /*
    |--------------------------------------------------------------------------
    | Captcha Code Length
    |--------------------------------------------------------------------------
    |
    | The number of characters in the captcha code.
    |
    */
    'codelen' => 4,

    /*
    |--------------------------------------------------------------------------
    | Captcha Image Dimensions
    |--------------------------------------------------------------------------
    |
    | The width and height of the captcha image in pixels.
    |
    */
    'width' => 130,
    'height' => 50,

    /*
    |--------------------------------------------------------------------------
    | Captcha Font
    |--------------------------------------------------------------------------
    |
    | The path to the font file used to render the captcha text.
    | After publishing the assets, this will point to the public font file.
    |
    */
    'font' => public_path('vendor/1bitcaptcha/font/icon.ttf'),

    /*
    |--------------------------------------------------------------------------
    | Captcha Font Size
    |--------------------------------------------------------------------------
    |
    | The font size used to render the captcha text.
    |
    */
    'fontsize' => 20,

    /*
    |--------------------------------------------------------------------------
    | Captcha Cache Time
    |--------------------------------------------------------------------------
    |
    | The number of seconds the captcha code will be stored in the cache.
    |
    */
    'cachetime' => 300,

    /*
    |--------------------------------------------------------------------------
    | Noise Settings
    |--------------------------------------------------------------------------
    |
    | The number of noise lines and points to add to the captcha image.
    |
    */
    'noise_lines' => 6,
    'noise_points' => 100,
];
