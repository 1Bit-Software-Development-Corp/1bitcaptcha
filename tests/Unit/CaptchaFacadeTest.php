<?php

namespace OneBit\Captcha\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use OneBit\Captcha\Facades\Captcha;
use OneBit\Captcha\Tests\TestCase\TestCase;

class CaptchaFacadeTest extends TestCase
{
    /** @test */
    public function it_can_set_custom_config()
    {
        $config = [
            'width' => 200,
            'height' => 80,
            'fontsize' => 25,
        ];

        $captcha = Captcha::withConfig($config);

        $this->assertNotNull($captcha);
    }

    /** @test */
    public function it_can_generate_code()
    {
        $captcha = Captcha::makeCode();
        $attr = Captcha::getAttr();

        $this->assertNotNull($captcha);
        $this->assertIsArray($attr);
        $this->assertArrayHasKey('code', $attr);
        $this->assertArrayHasKey('uniq', $attr);
        $this->assertArrayHasKey('data', $attr);
    }

    /** @test */
    public function it_can_get_code()
    {
        Captcha::makeCode();
        $code = Captcha::getCode();

        $this->assertNotEmpty($code);
    }

    /** @test */
    public function it_can_get_uniqid()
    {
        Captcha::makeCode();
        $uniqid = Captcha::getUniqid();

        $this->assertNotEmpty($uniqid);
    }

    /** @test */
    public function it_can_get_data()
    {
        Captcha::makeCode();
        $data = Captcha::getData();

        $this->assertNotEmpty($data);
        $this->assertStringStartsWith('data:image/png;base64,', $data);
    }

    /** @test */
    public function it_can_verify_captcha_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify the code
        $this->assertTrue(Captcha::check($code, $uniqid));
    }
}
