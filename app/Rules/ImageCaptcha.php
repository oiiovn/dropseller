<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\App;

class ImageCaptcha implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // $value nên là một mảng các chỉ số hình ảnh đã được chọn
        if (!is_array($value)) {
            return false;
        }
        
        $controller = App::make(RegisteredUserController::class);
        return $controller->validateImageCaptcha($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Xác thực captcha không thành công. Vui lòng thử lại.';
    }
}
