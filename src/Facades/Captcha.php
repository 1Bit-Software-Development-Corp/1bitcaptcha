<?php

declare(strict_types=1);

namespace OneBit\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \OneBit\Captcha\Contracts\Captcha withConfig(array $config)
 * @method static \OneBit\Captcha\Contracts\Captcha makeCode()
 * @method static array getAttr()
 * @method static bool check(string $code, string $uniqid)
 * @method static string getCode()
 * @method static string getUniqid()
 * @method static string getData()
 * 
 * @see \OneBit\Captcha\Services\Captcha
 */
class Captcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return '1bitcaptcha';
    }
}
