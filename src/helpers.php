<?php

declare(strict_types=1);

if (!function_exists('captcha')) {
    /**
     * Generate a new captcha
     *
     * @param array $config
     * @return array
     */
    function captcha(array $config = []): array
    {
        $captcha = app('1bitcaptcha');
        
        if (!empty($config)) {
            $captcha->withConfig($config);
        }
        
        return $captcha->makeCode()->getAttr();
    }
}

if (!function_exists('captcha_check')) {
    /**
     * Verify a captcha code
     *
     * @param string $code
     * @param string $uniqid
     * @return bool
     */
    function captcha_check(string $code, string $uniqid): bool
    {
        return app('1bitcaptcha')->check($code, $uniqid);
    }
}

if (!function_exists('captcha_img')) {
    /**
     * Get captcha image HTML
     *
     * @param string|null $id
     * @param array $attributes
     * @return string
     */
    function captcha_img(?string $id = null, array $attributes = []): string
    {
        $captcha = captcha();
        
        $id = $id ?? 'captcha-img';
        $attributes['id'] = $id;
        $attributes['src'] = $captcha['data'];
        
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= $key . '="' . $value . '" ';
        }
        
        $html = '<img ' . trim($attributeString) . '>';
        $html .= '<input type="hidden" name="captcha_uniq" value="' . $captcha['uniq'] . '">';
        
        return $html;
    }
}
