<template>
    <div class=" h-full">
        <jet-confirmation-modal maxWidth="md" :show="loading" @close="loading = false">
            <template #title>
                در حال بارگزاری اطلاعات
            </template>
            <template #content>
                <div class="w-full mt-3 text-center sm:mt-0">
                    <div class="lds-ellipsis">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </template>
            <template #footer>

            </template>
        </jet-confirmation-modal>

        <nav class="bg-gray-800">
            <div class="w-full mx-2 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8"
                                 :src="$page.configs.companyLogo ? $page.configs.companyLogo : 'https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg'"
                                 alt="Workflow">
                        </div>
                        <div>
                            <p class="text-gray-100 mr-4 sm:text-xl">{{$page.configs.companyName}}</p>
                            <p class="text-gray-100 mr-4 text-sm">{{$page.configs.pageTitle}}</p>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:mr-auto">
                            <button
                                class="bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <span class="sr-only">View notifications</span>
                                <!-- Heroicon name: bell -->
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </button>
                            <button @click="logout"
                                class="bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <span class="sr-only">View notifications</span>
                                <!-- Heroicon name: bell -->
                                <i class="material-icons h-6 w-6">exit_to_app</i>
                            </button>
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <button
                            class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                            <span class="sr-only">امکانات سیستم</span>

                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" @click="isOpen = !isOpen" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>

                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden" v-show="isOpen">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <inertia-link :href="route('dashboard.main')"
                                  class="text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M13,3V9H21V3M13,21H21V11H13M3,21H11V15H3M3,13H11V3H3V13Z"/>
                        </svg>
                        داشبورد
                    </inertia-link>
                    <inertia-link :href="route('dashboard.profiles.list')"
                                  class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M15 14C16.33 14 19 14.67 19 16V17H11V16C11 14.67 13.67 14 15 14M15 13C16.11 13 17 12.11 17 11S16.11 9 15 9C13.9 9 13 9.89 13 11C13 12.11 13.9 13 15 13M22 8V18C22 19.11 21.11 20 20 20H4C2.9 20 2 19.11 2 18V6C2 4.89 2.9 4 4 4H10L12 6H20C21.11 6 22 6.9 22 8M20 8H4V18H20V8Z"/>
                        </svg>
                        پذیرندگان
                    </inertia-link>

                    <inertia-link
                        v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='AGENT'"
                        :href="route('dashboard.devices.list')"
                        class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M3,4H20A2,2 0 0,1 22,6V8H18V6H5V18H14V20H3A2,2 0 0,1 1,18V6A2,2 0 0,1 3,4M17,10H23A1,1 0 0,1 24,11V21A1,1 0 0,1 23,22H17A1,1 0 0,1 16,21V11A1,1 0 0,1 17,10M18,12V19H22V12H18Z"/>
                        </svg>
                        دستگاه ها
                    </inertia-link>

                    <inertia-link v-if="$page.user.level==='SUPERUSER'"
                                  :href="route('dashboard.users.list',{type:'admin'})"
                                  class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M12,5A3.5,3.5 0 0,0 8.5,8.5A3.5,3.5 0 0,0 12,12A3.5,3.5 0 0,0 15.5,8.5A3.5,3.5 0 0,0 12,5M12,7A1.5,1.5 0 0,1 13.5,8.5A1.5,1.5 0 0,1 12,10A1.5,1.5 0 0,1 10.5,8.5A1.5,1.5 0 0,1 12,7M5.5,8A2.5,2.5 0 0,0 3,10.5C3,11.44 3.53,12.25 4.29,12.68C4.65,12.88 5.06,13 5.5,13C5.94,13 6.35,12.88 6.71,12.68C7.08,12.47 7.39,12.17 7.62,11.81C6.89,10.86 6.5,9.7 6.5,8.5C6.5,8.41 6.5,8.31 6.5,8.22C6.2,8.08 5.86,8 5.5,8M18.5,8C18.14,8 17.8,8.08 17.5,8.22C17.5,8.31 17.5,8.41 17.5,8.5C17.5,9.7 17.11,10.86 16.38,11.81C16.5,12 16.63,12.15 16.78,12.3C16.94,12.45 17.1,12.58 17.29,12.68C17.65,12.88 18.06,13 18.5,13C18.94,13 19.35,12.88 19.71,12.68C20.47,12.25 21,11.44 21,10.5A2.5,2.5 0 0,0 18.5,8M12,14C9.66,14 5,15.17 5,17.5V19H19V17.5C19,15.17 14.34,14 12,14M4.71,14.55C2.78,14.78 0,15.76 0,17.5V19H3V17.07C3,16.06 3.69,15.22 4.71,14.55M19.29,14.55C20.31,15.22 21,16.06 21,17.07V19H24V17.5C24,15.76 21.22,14.78 19.29,14.55M12,16C13.53,16 15.24,16.5 16.23,17H7.77C8.76,16.5 10.47,16 12,16Z"/>
                        </svg>
                        مدیران سیستم
                    </inertia-link>

                    <inertia-link v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'"
                                  :href="route('dashboard.users.list',{type:'agent'})"
                                  class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M12,5A3.5,3.5 0 0,0 8.5,8.5A3.5,3.5 0 0,0 12,12A3.5,3.5 0 0,0 15.5,8.5A3.5,3.5 0 0,0 12,5M12,7A1.5,1.5 0 0,1 13.5,8.5A1.5,1.5 0 0,1 12,10A1.5,1.5 0 0,1 10.5,8.5A1.5,1.5 0 0,1 12,7M5.5,8A2.5,2.5 0 0,0 3,10.5C3,11.44 3.53,12.25 4.29,12.68C4.65,12.88 5.06,13 5.5,13C5.94,13 6.35,12.88 6.71,12.68C7.08,12.47 7.39,12.17 7.62,11.81C6.89,10.86 6.5,9.7 6.5,8.5C6.5,8.41 6.5,8.31 6.5,8.22C6.2,8.08 5.86,8 5.5,8M18.5,8C18.14,8 17.8,8.08 17.5,8.22C17.5,8.31 17.5,8.41 17.5,8.5C17.5,9.7 17.11,10.86 16.38,11.81C16.5,12 16.63,12.15 16.78,12.3C16.94,12.45 17.1,12.58 17.29,12.68C17.65,12.88 18.06,13 18.5,13C18.94,13 19.35,12.88 19.71,12.68C20.47,12.25 21,11.44 21,10.5A2.5,2.5 0 0,0 18.5,8M12,14C9.66,14 5,15.17 5,17.5V19H19V17.5C19,15.17 14.34,14 12,14M4.71,14.55C2.78,14.78 0,15.76 0,17.5V19H3V17.07C3,16.06 3.69,15.22 4.71,14.55M19.29,14.55C20.31,15.22 21,16.06 21,17.07V19H24V17.5C24,15.76 21.22,14.78 19.29,14.55M12,16C13.53,16 15.24,16.5 16.23,17H7.77C8.76,16.5 10.47,16 12,16Z"/>
                        </svg>
                        نمایندگان
                    </inertia-link>

                    <inertia-link
                        v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='AGENT'"
                        :href="route('dashboard.users.list',{type:'marketer'})"
                        class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M12,5A3.5,3.5 0 0,0 8.5,8.5A3.5,3.5 0 0,0 12,12A3.5,3.5 0 0,0 15.5,8.5A3.5,3.5 0 0,0 12,5M12,7A1.5,1.5 0 0,1 13.5,8.5A1.5,1.5 0 0,1 12,10A1.5,1.5 0 0,1 10.5,8.5A1.5,1.5 0 0,1 12,7M5.5,8A2.5,2.5 0 0,0 3,10.5C3,11.44 3.53,12.25 4.29,12.68C4.65,12.88 5.06,13 5.5,13C5.94,13 6.35,12.88 6.71,12.68C7.08,12.47 7.39,12.17 7.62,11.81C6.89,10.86 6.5,9.7 6.5,8.5C6.5,8.41 6.5,8.31 6.5,8.22C6.2,8.08 5.86,8 5.5,8M18.5,8C18.14,8 17.8,8.08 17.5,8.22C17.5,8.31 17.5,8.41 17.5,8.5C17.5,9.7 17.11,10.86 16.38,11.81C16.5,12 16.63,12.15 16.78,12.3C16.94,12.45 17.1,12.58 17.29,12.68C17.65,12.88 18.06,13 18.5,13C18.94,13 19.35,12.88 19.71,12.68C20.47,12.25 21,11.44 21,10.5A2.5,2.5 0 0,0 18.5,8M12,14C9.66,14 5,15.17 5,17.5V19H19V17.5C19,15.17 14.34,14 12,14M4.71,14.55C2.78,14.78 0,15.76 0,17.5V19H3V17.07C3,16.06 3.69,15.22 4.71,14.55M19.29,14.55C20.31,15.22 21,16.06 21,17.07V19H24V17.5C24,15.76 21.22,14.78 19.29,14.55M12,16C13.53,16 15.24,16.5 16.23,17H7.77C8.76,16.5 10.47,16 12,16Z"/>
                        </svg>
                        بازاریابان
                    </inertia-link>

                    <inertia-link :href="route('dashboard.reports.list')"
                                  class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M22,21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z"/>
                        </svg>
                        گزارشات
                    </inertia-link>
                    <div @click="isSettingsOpen=!isSettingsOpen"
                       class="text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-base font-medium">
                        <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M22,21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z"/>
                        </svg>
                        تنظیمات
                    </div>
                    <div class="md:hidden" v-show="isSettingsOpen">
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            تنظیمات سیستم
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            دستگاه ها
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            بانک ها
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            سرویس دهندگان
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            دلایل خرابی
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            محل های تعمیرات
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            ارسال مدارک
                        </inertia-link>
                        <inertia-link :href="route('dashboard.reports.list')"
                                      class="mr-2 text-gray-300 hover:bg-gray-700 hover:text-white block px-2 py-2 rounded-md text-sm font-medium">
                            پشتیبان گیری / بازیابی اطلاعات
                        </inertia-link>
                    </div>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-700">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8 rounded-full m-1" :src="$page.user.profile_photo_url" alt="">
                        </div>
                        <div class="ml-3">
                            <div class="inline text-base font-medium leading-none text-white">{{$page.user.name}}</div>
                            <div class="inline text-sm font-medium leading-none text-gray-400">
                                ({{$page.user.levelText}})
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

                        <a href="#"
                           class="block px-3 py-1 rounded-md text-xs text-gray-400">تنظیمات</a>

                        <a href="#" @click.prevent="logout"
                           class="block px-3 py-1 rounded-md text-xs text-gray-400">خروج از سیستم</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="grid grid-cols-1 h-full sm:grid-cols-12">
            <div class="hidden h-full sm:block sm:col-span-2 bg-gray-800">
                <Sidebar></Sidebar>
            </div>
            <div class="col-span-1 sm:col-span-10">
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-3xl font-bold leading-tight text-gray-900">
                            <slot name="header"></slot>
                        </h1>
                    </div>
                </header>
                <slot name="contents"></slot>
            </div>
        </div>

        <portal-target name="modal" multiple></portal-target>

    </div>

</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import {Inertia} from '@inertiajs/inertia';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import Sidebar from "@/Layouts/Sidebar";

    export default {
        components: {
            Sidebar,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            Inertia,
            JetConfirmationModal
        },
        props: {
            isMenuActive: {type: String, default: 'dashboard'}
        },
        data() {
            return {
                showingNavigationDropdown: false,
                isOpen: false,
                isSettingsOpen: false,
                profileMenu: false,
                loading: false,
            }
        },
        mounted() {
            Inertia.on('start', event => {
                this.loading = true;
            });

            Inertia.on('finish', event => {
                this.loading = false;
            })
        },
        methods: {
            openProfileMenu() {
                this.profileMenu = !this.profileMenu;
            },
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },

            logout() {
                axios.post(route('logout').url()).then(response => {
                    window.location = '/';
                })
            },
        }
    }
</script>
<style scoped>
    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }

    .lds-ellipsis div {
        position: absolute;
        top: 33px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #ff0000;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }
        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }
        100% {
            transform: translate(24px, 0);
        }
    }

</style>
