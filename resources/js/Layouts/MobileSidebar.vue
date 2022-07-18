<template>
    <div class="md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <inertia-link :href="route('dashboard.main')"
                          class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">dashboard</i>
                داشبورد
            </inertia-link>
            <template v-if="$page.user.level!='TECHNICAL'">
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    پذیرندگان</p>
                <inertia-link :href="route('dashboard.profiles.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">assignment_ind</i>
                    لیست پذیرندگان
                </inertia-link>
                <inertia-link v-if="$page.user.level!='TECHNICAL'" :href="route('dashboard.profiles.create')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">assignment_ind</i>
                    ثبت درخواست جدید
                </inertia-link>
            </template>
            <template v-if="$page.user.level!='MARKETER'">
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    دستگاه ها</p>
                <inertia-link :href="route('dashboard.devices.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">dock</i>
                    لیست دستگاه ها
                </inertia-link>
                <inertia-link :href="route('dashboard.repairs.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">construction</i>
                    تعمیرات
                </inertia-link>
                <inertia-link :href="route('dashboard.returns.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">keyboard_return</i>
                    عودت دستگاه
                </inertia-link>
            </template>
            <template v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    اخبار و اطلاعیه ها</p>
                <inertia-link :href="route('dashboard.posts.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">article</i>
                    لیست اخبار
                </inertia-link>
                <inertia-link :href="route('dashboard.posts.categories.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">list</i>
                    دسته بندی ها
                </inertia-link>
            </template>
            <template
                v-if="$page.user.level!=='TECHNICAL' && $page.user.level!=='MARKETER' && $page.user.level!=='OFFICE'">
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    کاربران</p>
                <inertia-link v-if="$page.user.level=='SUPERUSER'" :href="route('dashboard.users.list',{type:'admin'})"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">people</i>
                    مدیران سیستم
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.users.list',{type:'office'})"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">reduce_capacity</i>
                    کارمندان دفتر
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.users.list',{type:'agent'})"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">reduce_capacity</i>
                    نمایندگان
                </inertia-link>
                <inertia-link
                    v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT'"
                    :href="route('dashboard.users.list',{type:'marketer'})"
                    class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">person</i>
                    بازاریابان
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.users.list',{type:'technical'})"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">engineering</i>
                    کارشناسان فنی
                </inertia-link>
            </template>
            <template>
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    پشتیبانی
                </p>
                <inertia-link :href="route('dashboard.tickets.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">comment</i>
                    لیست درخواست ها
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.tickets.agents.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">group</i>
                    کاربران
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.tickets.types.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">forum</i>
                    واحدها
                </inertia-link>
                <inertia-link v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'"
                              :href="route('dashboard.notifications.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">forum</i>
                    اعلان های گروهی
                </inertia-link>
            </template>
            <template v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                <p class="text-gray-300 hover:bg-gray-700 hover:text-white block px-4 py-2 rounded-md text-base font-bold">
                    تنظیمات</p>
                <inertia-link :href="route('dashboard.settings.main')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">settings</i>
                    تنظیمات سیستم
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.devices.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">developer_mode</i>
                    دستگاه ها
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.banks.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">attach_money</i>
                    بانک ها
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.psps.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">miscellaneous_services</i>
                    سرویس دهندگان
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.repairTypes.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">handyman</i>
                    دلایل خرابی
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.repairLocations.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">location_city</i>
                    محل های تعمیرات
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.licenses.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">content_paste</i>
                    ارسال مدارک
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.notifications.types.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">textsms</i>
                    انواع اعلان ها
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.notifications.events.list')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">sms_failed</i>
                    تنظیمات اعلان ها
                </inertia-link>
                <inertia-link :href="route('dashboard.settings.main')"
                              class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                    <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">security</i>
                    پشتیبان گیری / بازیابی اطلاعات
                </inertia-link>
            </template>
        </div>
        <div class="pt-4 pb-3 border-t border-gray-700">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8 rounded-full m-1" :src="$page.user.profile_photo_url" alt="">
                </div>
                <div class="ml-3">
                    <div class="inline text-base font-medium leading-none text-white">{{ $page.user.name }}</div>
                    <div class="inline text-sm font-medium leading-none text-gray-400">
                        ({{ $page.user.levelText }})
                    </div>
                </div>
                <button
                    class="ml-auto bg-gray-800 flex-shrink-0 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                    <span class="sr-only">View notifications</span>
                    <!-- Heroicon name: bell -->
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>
            </div>
            <div class="mt-3 px-2 space-y-1">
                <a href="#"
                   class="block px-3 py-1 rounded-md text-xs text-gray-400">
                    پروفایل شما
                </a>

                <a :href="route('dashboard.settings.main')"
                   class="block px-3 py-1 rounded-md text-xs text-gray-400">تنظیمات</a>

                <a href="#" @click.prevent="logout"
                   class="block px-3 py-1 rounded-md text-xs text-gray-400">خروج از سیستم</a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "MobileSidebar",
    methods: {
        logout() {
            axios.post(route('logout').url()).then(response => {
                window.location = '/';
            })
        },
    }
}

</script>

<style scoped>

</style>
