<?php

declare(strict_types=1);

namespace OneBit\Captcha\Contracts;

interface Captcha
{
    /**
     * Set custom configuration for the captcha
     * 
     * @param array $config
     * @return self
     */
    public function withConfig(array $config): self;

    /**
     * Generate a new captcha code
     * 
     * @return self
     */
    public function makeCode(): self;

    /**
     * Get captcha attributes (code, unique id, and image data)
     * 
     * @return array
     */
    public function getAttr(): array;

    /**
     * Verify if the provided code matches the stored captcha code
     * 
     * @param string $code
     * @param string $uniqid
     * @return bool
     */
    public function check(string $code, string $uniqid): bool;
}
