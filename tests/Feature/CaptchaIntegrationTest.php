<?php

namespace OneBit\Captcha\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use OneBit\Captcha\Facades\Captcha;
use OneBit\Captcha\Tests\TestCase\TestCase;

class CaptchaIntegrationTest extends TestCase
{
    /** @test */
    public function it_can_generate_and_verify_captcha()
    {
        // Generate a captcha
        $attr = captcha();
        
        $code = $attr['code'];
        $uniqid = $attr['uniq'];
        
        // Verify that the code is in the cache
        $this->assertTrue(Cache::has($uniqid));
        
        // Verify the captcha
        $this->assertTrue(captcha_check($code, $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }

    /** @test */
    public function it_can_generate_and_verify_captcha_using_facade()
    {
        // Generate a captcha
        $captcha = Captcha::makeCode();
        $attr = Captcha::getAttr();
        
        $code = $attr['code'];
        $uniqid = $attr['uniq'];
        
        // Verify that the code is in the cache
        $this->assertTrue(Cache::has($uniqid));
        
        // Verify the captcha
        $this->assertTrue(Captcha::check($code, $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }

    /** @test */
    public function it_can_generate_captcha_with_custom_config()
    {
        // Generate a captcha with custom config
        $attr = captcha([
            'codelen' => 6,
            'width' => 200,
            'height' => 80,
        ]);
        
        $code = $attr['code'];
        
        // Verify code length
        $this->assertEquals(6, strlen($code));
    }

    /** @test */
    public function it_can_generate_captcha_with_custom_config_using_facade()
    {
        // Generate a captcha with custom config
        Captcha::withConfig([
            'codelen' => 6,
            'width' => 200,
            'height' => 80,
        ])->makeCode();
        
        $code = Captcha::getCode();
        
        // Verify code length
        $this->assertEquals(6, strlen($code));
    }
}
