<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo/>
        </x-slot>
        <h3 class="text-xl text-center">{{systemConfig('COMPANY_NAME')}}</h3>
        <h3 class="text-lg text-center">{{systemConfig('PAGE_TITLE')}}</h3>
        <h2 class="text-lg text-center md:text-3xl">
            {{!isset($message) || is_null($message) ? 'اطلاعات مورد نظر یافت نشد.' : $message}}
        </h2>
    </x-jet-authentication-card>
</x-guest-layout>

