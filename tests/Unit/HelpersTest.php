<?php

namespace OneBit\Captcha\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use OneBit\Captcha\Tests\TestCase\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function captcha_function_returns_captcha_attributes()
    {
        $attr = captcha();

        $this->assertIsArray($attr);
        $this->assertArrayHasKey('code', $attr);
        $this->assertArrayHasKey('uniq', $attr);
        $this->assertArrayHasKey('data', $attr);
        
        $this->assertNotEmpty($attr['code']);
        $this->assertNotEmpty($attr['uniq']);
        $this->assertStringStartsWith('data:image/png;base64,', $attr['data']);
    }

    /** @test */
    public function captcha_function_accepts_custom_config()
    {
        $config = [
            'width' => 200,
            'height' => 80,
            'fontsize' => 25,
            'codelen' => 6,
        ];

        $attr = captcha($config);

        $this->assertIsArray($attr);
        $this->assertEquals(6, strlen($attr['code']));
    }

    /** @test */
    public function captcha_check_function_verifies_captcha_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify the code
        $this->assertTrue(captcha_check($code, $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }

    /** @test */
    public function captcha_check_function_returns_false_for_invalid_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify with wrong code
        $this->assertFalse(captcha_check('WRONG', $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }
}
