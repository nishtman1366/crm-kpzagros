<x-guest-layout>
    <div class="flex items-center justify-center">
        <div class="w-1/3 text-3xl text-center">
            <div class="border border-grey-700 bg-gray-300 hover:bg-gray-400 transition-all">
                <a href="/crm" target="_blank">
                    ورود به سامانه جامع<br/>(نسخه جدید)
                </a>
            </div>
        </div>
        <x-jet-authentication-card>
            <x-slot name="logo">
                <x-jet-authentication-card-logo/>
            </x-slot>

            <h3 class="text-2xl text-center">{{systemConfig('COMPANY_NAME', 'کیان‌پرداز زاگرس')}}</h3>
            <h3 class="text-xl text-center">{{systemConfig('PAGE_TITLE', 'سامانه جامع امور نمایندگان زاگرس پی')}}</h3>

            <x-jet-validation-errors class="mb-4"/>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <x-jet-label for="username" value="{{ __('نام کاربری') }}"/>
                    <x-jet-input id="username" class="block mt-1 w-full" type="text" name="username"
                                 :value="old('username')" required autofocus/>
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ __('کلمه عبور') }}"/>
                    <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required
                                 autocomplete="current-password"/>
                </div>

                {{--            <div class="block mt-4">--}}
                {{--                <label for="remember_me" class="flex items-center">--}}
                {{--                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">--}}
                {{--                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>--}}
                {{--                </label>--}}
                {{--            </div>--}}

                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        {{--                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">--}}
                        {{--                        {{ __('Forgot your password?') }}--}}
                        {{--                    </a>--}}
                    @endif
                    @if(request()->getHttpHost()==='kpzagros-crm.ir' || request()->getHttpHost()==='kpzagros-crm.com' || request()->getHttpHost()==='127.0.0.1:8000')
                        <div>
                            <a class="text-blue-500 hover:text-blue-400"
                               href="https://zagrospay.com/%d9%81%d8%b1%d9%85-%d8%af%d8%b1%d8%ae%d9%88%d8%a7%d8%b3%d8%aa-%d9%86%d9%85%d8%a7%db%8c%d9%86%d8%af%da%af%db%8c/"
                               target="_blank">درخواست نمایندگی</a>
                        </div>
                        <div>
                            <a class="text-blue-500 hover:text-blue-400"
                               href="https://zagrospay.com/%d8%af%d8%b1%d8%a8%d8%a7%d8%b1%d9%87-%d9%85%d8%a7/"
                               target="_blank">
                                درباره ما
                            </a>
                        </div>
                    @endif
                    <x-jet-button class="ml-4">
                        {{ __('ورود') }}
                    </x-jet-button>

                </div>
            </form>
        </x-jet-authentication-card>
    </div>
</x-guest-layout>
