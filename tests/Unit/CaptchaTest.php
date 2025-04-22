<?php

namespace OneBit\Captcha\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use OneBit\Captcha\Services\Captcha;
use OneBit\Captcha\Tests\TestCase\TestCase;

class CaptchaTest extends TestCase
{
    /** @var Captcha */
    protected $captcha;

    protected function setUp(): void
    {
        parent::setUp();
        $this->captcha = $this->app->make(Captcha::class);
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(Captcha::class, $this->captcha);
    }

    /** @test */
    public function it_can_set_custom_config()
    {
        $config = [
            'width' => 200,
            'height' => 80,
            'fontsize' => 25,
        ];

        $captcha = $this->captcha->withConfig($config);

        $this->assertInstanceOf(Captcha::class, $captcha);
        
        // We need to use reflection to access protected properties
        $reflectionClass = new \ReflectionClass(Captcha::class);
        $configProperty = $reflectionClass->getProperty('config');
        $configProperty->setAccessible(true);
        
        $actualConfig = $configProperty->getValue($captcha);
        
        $this->assertEquals(200, $actualConfig['width']);
        $this->assertEquals(80, $actualConfig['height']);
        $this->assertEquals(25, $actualConfig['fontsize']);
    }

    /** @test */
    public function it_can_set_custom_code()
    {
        $code = 'TEST123';
        $captcha = $this->captcha->withCode($code);

        $this->assertInstanceOf(Captcha::class, $captcha);
        $this->assertEquals($code, $captcha->getCode());
    }

    /** @test */
    public function it_can_set_custom_uniqid()
    {
        $uniqid = 'test-unique-id-123';
        $captcha = $this->captcha->withUniqid($uniqid);

        $this->assertInstanceOf(Captcha::class, $captcha);
        $this->assertEquals($uniqid, $captcha->getUniqid());
    }

    /** @test */
    public function it_can_generate_code()
    {
        $captcha = $this->captcha->makeCode();

        $this->assertInstanceOf(Captcha::class, $captcha);
        $this->assertNotEmpty($captcha->getCode());
        $this->assertNotEmpty($captcha->getUniqid());
        
        // Check that the code is stored in cache
        $this->assertTrue(Cache::has($captcha->getUniqid()));
        $this->assertEquals($captcha->getCode(), Cache::get($captcha->getUniqid()));
    }

    /** @test */
    public function it_can_generate_code_with_custom_length()
    {
        $config = ['codelen' => 6];
        $captcha = $this->captcha->withConfig($config)->makeCode();

        $this->assertInstanceOf(Captcha::class, $captcha);
        $this->assertEquals(6, strlen($captcha->getCode()));
    }

    /** @test */
    public function it_can_generate_code_with_custom_charset()
    {
        $config = ['charset' => '0123456789'];
        $captcha = $this->captcha->withConfig($config)->makeCode();

        $this->assertInstanceOf(Captcha::class, $captcha);
        $this->assertMatchesRegularExpression('/^[0-9]+$/', $captcha->getCode());
    }

    /** @test */
    public function it_can_get_attributes()
    {
        $captcha = $this->captcha->makeCode();
        $attr = $captcha->getAttr();

        $this->assertIsArray($attr);
        $this->assertArrayHasKey('code', $attr);
        $this->assertArrayHasKey('uniq', $attr);
        $this->assertArrayHasKey('data', $attr);
        
        $this->assertEquals($captcha->getCode(), $attr['code']);
        $this->assertEquals($captcha->getUniqid(), $attr['uniq']);
        $this->assertStringStartsWith('data:image/png;base64,', $attr['data']);
    }

    /** @test */
    public function it_can_verify_captcha_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify the code
        $this->assertTrue($this->captcha->check($code, $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }

    /** @test */
    public function it_returns_false_for_invalid_captcha_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify with wrong code
        $this->assertFalse($this->captcha->check('WRONG', $uniqid));
        
        // Code should be removed from cache after verification
        $this->assertFalse(Cache::has($uniqid));
    }

    /** @test */
    public function it_returns_false_for_empty_uniqid()
    {
        $this->assertFalse($this->captcha->check('TEST123', ''));
    }

    /** @test */
    public function it_is_case_insensitive_when_verifying_code()
    {
        $code = 'TEST123';
        $uniqid = 'test-unique-id-123';
        
        // Store the code in cache
        Cache::put($uniqid, $code, 300);
        
        // Verify with lowercase code
        $this->assertTrue($this->captcha->check('test123', $uniqid));
    }
}
