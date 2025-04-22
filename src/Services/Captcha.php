<?php

declare(strict_types=1);

namespace OneBit\Captcha\Services;

use OneBit\Captcha\Contracts\Captcha as CaptchaContract;
use Illuminate\Support\Facades\Cache;

class Captcha implements CaptchaContract
{
    protected object $img;

    protected string $code = '';

    protected string $uniqid = '';

    protected array $config = [];

    public function __construct()
    {
        $this->config = config('1bitcaptcha');
    }

    /**
     * Set the captcha code
     * 
     * @param string $code
     * @return self
     */
    public function withCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set the unique identifier
     * 
     * @param string $uniqid
     * @return self
     */
    public function withUniqid(string $uniqid): self
    {
        $this->uniqid = $uniqid;

        return $this;
    }

    /**
     * Set custom configuration for the captcha
     * 
     * @param array $config
     * @return self
     */
    public function withConfig(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * Generate a new captcha code
     * 
     * @return self
     */
    public function makeCode(): self
    {
        if (empty($this->uniqid)) {
            $this->uniqid = md5(uniqid('captcha') . mt_rand(10000, 99999));
        }

        if (empty($this->code)) {
            $length = strlen($this->config['charset']) - 1;

            for ($i = 0; $i < $this->config['codelen']; $i++) {
                $this->code .= $this->config['charset'][mt_rand(0, $length)];
            }
        } else {
            $this->config['codelen'] = strlen($this->code);
        }

        Cache::put($this->uniqid, $this->code, $this->config['cachetime']);

        return $this;
    }

    /**
     * Creates the CAPTCHA image and returns it as a base64 encoded string
     *
     * @return string Base64 encoded PNG image
     *
     * @throws \RuntimeException If the font file doesn't exist or GD operations fail
     */
    private function createImage(): string
    {
        if (!file_exists($this->config['font'])) {
            throw new \RuntimeException('CAPTCHA font file not found: ' . $this->config['font']);
        }

        try {
            $this->initializeImage();
            $this->addNoiseLines();
            $this->addNoisePoints();
            $this->renderText();

            return $this->getImageData();
        } finally {
            // Ensure we clean up the image resource even if an error occurs
            if (isset($this->img) && is_resource($this->img)) {
                imagedestroy($this->img);
            }
        }
    }

    /**
     * Initialize the base image with background
     */
    private function initializeImage(): void
    {
        $this->img = imagecreatetruecolor($this->config['width'], $this->config['height']);

        if ($this->img === false) {
            throw new \RuntimeException('Failed to create GD image');
        }

        // Set background color
        $backgroundColor = imagecolorallocate(
            $this->img,
            mt_rand(220, 255),
            mt_rand(220, 255),
            mt_rand(220, 255)
        );

        if ($backgroundColor === false) {
            throw new \RuntimeException('Failed to allocate background color');
        }

        imagefilledrectangle(
            $this->img,
            0,
            0,
            $this->config['width'],
            $this->config['height'],
            $backgroundColor
        );
    }

    /**
     * Add random lines to the image for noise
     */
    private function addNoiseLines(): void
    {
        $numLines = $this->config['noise_lines'] ?? 6;

        for ($i = 0; $i < $numLines; $i++) {
            $color = imagecolorallocate(
                $this->img,
                mt_rand(0, 50),
                mt_rand(0, 50),
                mt_rand(0, 50)
            );

            if ($color === false) {
                continue;
            }

            imageline(
                $this->img,
                mt_rand(0, $this->config['width']),
                mt_rand(0, $this->config['height']),
                mt_rand(0, $this->config['width']),
                mt_rand(0, $this->config['height']),
                $color
            );
        }
    }

    /**
     * Add random points to the image for noise
     */
    private function addNoisePoints(): void
    {
        $numPoints = $this->config['noise_points'] ?? 100;

        for ($i = 0; $i < $numPoints; $i++) {
            $color = imagecolorallocate(
                $this->img,
                mt_rand(200, 255),
                mt_rand(200, 255),
                mt_rand(200, 255)
            );

            if ($color === false) {
                continue;
            }

            imagestring(
                $this->img,
                mt_rand(1, 5),
                mt_rand(0, $this->config['width']),
                mt_rand(0, $this->config['height']),
                '*',
                $color
            );
        }
    }

    /**
     * Render the CAPTCHA text on the image
     */
    private function renderText(): void
    {
        $charWidth = $this->config['width'] / $this->config['codelen'];

        for ($i = 0; $i < $this->config['codelen']; $i++) {
            $fontColor = imagecolorallocate(
                $this->img,
                mt_rand(0, 156),
                mt_rand(0, 156),
                mt_rand(0, 156)
            );

            if ($fontColor === false) {
                continue;
            }

            $x = (int) ($charWidth * $i + mt_rand(1, 5));
            $y = (int) ($this->config['height'] / 1.4);
            $angle = mt_rand(-30, 30);

            imagettftext(
                $this->img,
                $this->config['fontsize'],
                $angle,
                $x,
                $y,
                $fontColor,
                $this->config['font'],
                $this->code[$i]
            );
        }
    }

    /**
     * Get the image data as base64 encoded string
     */
    private function getImageData(): string
    {
        ob_start();
        imagepng($this->img);
        $data = ob_get_clean();

        if ($data === false) {
            throw new \RuntimeException('Failed to capture image data');
        }

        return base64_encode($data);
    }

    /**
     * Get captcha attributes (code, unique id, and image data)
     * 
     * @return array
     */
    public function getAttr(): array
    {
        return [
            'code' => $this->code,
            'uniq' => $this->uniqid,
            'data' => $this->getData(),
        ];
    }

    /**
     * Get the captcha code
     * 
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the unique identifier
     * 
     * @return string
     */
    public function getUniqid(): string
    {
        return $this->uniqid;
    }

    /**
     * Get the captcha image as a data URI
     * 
     * @return string
     */
    public function getData(): string
    {
        return "data:image/png;base64,{$this->createImage()}";
    }

    /**
     * Verify if the provided code matches the stored captcha code
     * 
     * @param string $code
     * @param string $uniqid
     * @return bool
     */
    public function check(string $code, string $uniqid): bool
    {
        if (empty($uniqid)) {
            return false;
        }

        $val = Cache::pull($uniqid);

        return is_string($val) && strtolower($val) === strtolower($code);
    }
}
