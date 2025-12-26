<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mews\Captcha\Facades\Captcha as CaptchaFacade;

class Captcha implements Rule
{
    public function passes($attribute, $value)
    {
        return CaptchaFacade::check($value, 'default'); // Sử dụng config 'default', không phải 'math'
    }

    public function message()
    {
        return 'Mã xác thực CAPTCHA không chính xác.';
    }
}
