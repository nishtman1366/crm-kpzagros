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
            <MobileSidebar v-show="isOpen"/>
        </nav>

        <div class="grid grid-cols-1 h-full sm:grid-cols-12">
            <div class="hidden h-full sm:block sm:col-span-2 bg-gray-800">
                <Sidebar/>
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
    import MobileSidebar from "@/Layouts/MobileSidebar";

    export default {
        components: {
            MobileSidebar,
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
