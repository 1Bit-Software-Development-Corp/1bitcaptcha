# 1bitcaptcha

A simple and customizable captcha package for Laravel.

## Installation

You can install the package via composer:

```bash
composer require 1bit/captcha
```

## Publishing Assets

After installing the package, you need to publish the configuration file and assets:

```bash
php artisan vendor:publish --provider="OneBit\Captcha\CaptchaServiceProvider" --tag="1bitcaptcha-config"
php artisan vendor:publish --provider="OneBit\Captcha\CaptchaServiceProvider" --tag="1bitcaptcha-assets"
```

## Configuration

You can customize the captcha by editing the `config/1bitcaptcha.php` file:

```php
return [
    'charset' => 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789',
    'codelen' => 4,
    'width' => 130,
    'height' => 50,
    'font' => public_path('vendor/1bitcaptcha/font/icon.ttf'),
    'fontsize' => 20,
    'cachetime' => 300,
    'noise_lines' => 6,
    'noise_points' => 100,
];
```

## Usage

### Basic Usage

You can use the captcha in your forms like this:

```php
// In your controller
public function showForm()
{
    return view('form');
}

// In your view (form.blade.php)
<form method="POST" action="/submit">
    @csrf

    <div class="form-group">
        <label for="captcha">Captcha</label>
        {!! captcha_img() !!}
        <input type="text" name="captcha" id="captcha" class="form-control" required>
    </div>

    <button type="submit">Submit</button>
</form>

// In your controller to validate the captcha
public function submit(Request $request)
{
    $request->validate([
        'captcha' => 'required',
        'captcha_uniq' => 'required',
    ]);

    if (!captcha_check($request->captcha, $request->captcha_uniq)) {
        return back()->withErrors(['captcha' => 'Invalid captcha code']);
    }

    // Process the form...
}
```

### Using the Facade

You can also use the Captcha facade directly:

```php
use OneBit\Captcha\Facades\Captcha;

// Generate a new captcha
$captcha = Captcha::makeCode()->getAttr();

// Verify a captcha
$isValid = Captcha::check($code, $uniqid);
```

### Custom Configuration

You can customize the captcha on the fly:

```php
// Using the helper function
$captcha = captcha([
    'width' => 200,
    'height' => 70,
    'fontsize' => 30,
]);

// Using the facade
$captcha = Captcha::withConfig([
    'width' => 200,
    'height' => 70,
    'fontsize' => 30,
])->makeCode()->getAttr();
```

### Using Captcha with API

You can also use the captcha in your API endpoints. Here's how to implement it:

#### 1. Create a Controller to Generate Captcha

```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use OneBit\Captcha\Facades\Captcha;
use Illuminate\Support\Arr;

class CaptchaController extends Controller
{
    /**
     * Get a new captcha.
     */
    public function getCaptcha()
    {
        $captchaAttr = Captcha::makeCode()->getAttr();

        $captchaImage = Arr::get($captchaAttr, 'data', '');
        $captchaUniqid = Arr::get($captchaAttr, 'uniq', '');

        return response()->json([
            'success' => true,
            'data' => [
                'captcha_image' => $captchaImage,
                'captcha_key' => $captchaUniqid,
            ]
        ]);
    }
}
```

#### 2. Add a Route for the Captcha API

```php
// In your routes/api.php file
Route::get('/captcha', [\App\Http\Controllers\Auth\CaptchaController::class, 'getCaptcha']);
```

#### 3. Validate Captcha in Form Requests

You can validate the captcha in your form requests like this:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OneBit\Captcha\Facades\Captcha;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Your other validation rules
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

            // Captcha validation rules
            'captcha_key' => 'required|string',
            'captcha_value' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$this->validateCaptcha($this->captcha_key, $value)) {
                        $fail('The captcha is incorrect.');
                    }
                },
            ],
        ];

        return $rules;
    }

    /**
     * Validate the captcha value against the key.
     */
    private function validateCaptcha(string $key, string $value): bool
    {
        return Captcha::check($value, $key);
    }
}
```

#### 4. Frontend Implementation Example

Here's an example of how to implement the captcha in a frontend application using JavaScript/Axios:

```javascript
// Fetch a new captcha
async function fetchCaptcha() {
  try {
    const response = await axios.get('/api/captcha');
    if (response.data.success) {
      // Display the captcha image
      document.getElementById('captcha-image').src = response.data.data.captcha_image;
      // Store the captcha key to be submitted with the form
      document.getElementById('captcha-key').value = response.data.data.captcha_key;
    }
  } catch (error) {
    console.error('Failed to fetch captcha:', error);
  }
}

// Submit the form with captcha
async function submitForm(event) {
  event.preventDefault();

  const formData = {
    name: document.getElementById('name').value,
    email: document.getElementById('email').value,
    password: document.getElementById('password').value,
    password_confirmation: document.getElementById('password_confirmation').value,
    captcha_key: document.getElementById('captcha-key').value,
    captcha_value: document.getElementById('captcha-value').value
  };

  try {
    const response = await axios.post('/api/register', formData);
    // Handle successful registration
  } catch (error) {
    // Handle errors, possibly refresh captcha
    fetchCaptcha();
  }
}

// Initialize captcha when page loads
document.addEventListener('DOMContentLoaded', fetchCaptcha);

// Add refresh button for captcha
document.getElementById('refresh-captcha').addEventListener('click', fetchCaptcha);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
