<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- CAPTCHA -->
        <div class="form-group mb-3">
            <label for="captcha">Mã xác thực <span class="text-danger">*</span></label>
            <div class="captcha-container">
                <span>{!! Captcha::img('default') !!}</span>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fa fa-refresh"></i> Làm mới mã
                </a>
            </div>
            <input id="captcha" type="text" class="form-control mt-2 @error('captcha') is-invalid @enderror"
                name="captcha" placeholder="Nhập mã xác thực">
            @error('captcha')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Nếu bạn chưa có tài khoản?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Đăng Ký') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>