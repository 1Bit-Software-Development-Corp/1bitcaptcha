<?php

namespace OneBit\Captcha\Tests\Unit;

use OneBit\Captcha\Contracts\Captcha as CaptchaContract;
use OneBit\Captcha\Services\Captcha;
use OneBit\Captcha\Tests\TestCase\TestCase;

class CaptchaServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_captcha_contract()
    {
        $this->assertTrue($this->app->bound(CaptchaContract::class));
        $this->assertInstanceOf(Captcha::class, $this->app->make(CaptchaContract::class));
    }

    /** @test */
    public function it_registers_captcha_singleton()
    {
        $this->assertTrue($this->app->bound('1bitcaptcha'));
        $this->assertInstanceOf(Captcha::class, $this->app->make('1bitcaptcha'));
    }

    /** @test */
    public function it_registers_captcha_facade()
    {
        $this->assertTrue($this->app->bound('Captcha'));
    }

    /** @test */
    public function it_merges_config()
    {
        $config = $this->app['config']->get('1bitcaptcha');
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('charset', $config);
        $this->assertArrayHasKey('codelen', $config);
        $this->assertArrayHasKey('width', $config);
        $this->assertArrayHasKey('height', $config);
        $this->assertArrayHasKey('font', $config);
        $this->assertArrayHasKey('fontsize', $config);
        $this->assertArrayHasKey('cachetime', $config);
        $this->assertArrayHasKey('noise_lines', $config);
        $this->assertArrayHasKey('noise_points', $config);
    }
}
