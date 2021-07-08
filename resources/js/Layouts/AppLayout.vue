<template>
    <div class=" h-full">
        <jet-confirmation-modal maxWidth="md" :show="loading" @close="loading = false">
            <template #title>
                در حال بارگزاری اطلاعات
            </template>
            <template #content>
                <loading/>
            </template>
            <template #footer>

            </template>
        </jet-confirmation-modal>
        <div v-show="searchResultsContainer" class="fixed inset-0 transform transition-all"
             @click="searchResultsContainer=false">
            <div class="absolute inset-0 bg-gray-500 opacity-75 z-50"></div>
        </div>
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
                    <div class="relative h-full w-1/2 hidden md:flex py-2 px-2 items-center justify-start">
                        <input type="text"
                               class="w-full h-12 border-l-0 border border-blue-500 text-lg focus:bg-gray-50"
                               placeholder="جستجوی جامع (/ + ctrl)"
                               id="searchQuery"
                               v-model="searchQuery"
                               @keyup.enter="submitSearch"
                               @focus="searchResultsContainer=true"/>
                        <div @click="submitSearch"
                             class="bg-white hover:bg-gray-400 border-r-0 border border-blue-500 cursor-pointer w-12 h-12 flex items-center justify-center">
                            <i class="material-icons">search</i>
                        </div>
                        <div v-if="searchResultsContainer"
                             class="absolute top-full left-0 px-2 bg-white w-full h-64 rounded-b">
                            <p class="text-md font-bold my-1">موارد قابل جستجو:</p>
                            <div
                                class="text-xs text-gray-500 sm:col-span-2 md:col-span-4 grid grid-cols-2 md:grid-cols-4">
                                <div>
                                    <p class="font-bold">اطلاعات پرونده</p>
                                    <p>سریال دستگاه</p>
                                    <p>شماره ترمینال</p>
                                    <p>شماره مشتری</p>
                                    <p>دلیل رد شدن سریال</p>
                                    <p>دلیل لغو پرونده</p>
                                    <p>دلیل تغییر سریال</p>
                                    <p>نام مالک قبلی</p>
                                    <p>کد ملی مالک قبلی</p>
                                    <p>تلفن همراه مالک قبلی</p>
                                </div>
                                <div>
                                    <p class="font-bold">اطلاعات پذیرنده</p>
                                    <p>نام و نام خانوادگی</p>
                                    <p>شماره شناسنامه</p>
                                    <p>کد ملی</p>
                                    <p>نام پدر</p>
                                    <p>تلفن همراه</p>
                                    <p>نام شرکت</p>
                                    <p>نام تجاری</p>
                                    <p> شماره ثبت</p>
                                    <p>شناسه ملی</p>
                                </div>
                                <div>
                                    <p class="font-bold">اطلاعات کسب و کار</p>
                                    <p>نام کسب و کار</p>
                                    <p>صنف مربوطه</p>
                                    <p>تلفن تماس</p>
                                    <p>آدرس و کد پستی</p>
                                    <p>کد مالیاتی</p>
                                    <p class="font-bold">اطلاعات دستگاه ها</p>
                                    <p>سریال دستگاه</p>
                                    <p>مدل</p>
                                    <p>توضیحات</p>
                                </div>
                                <div>
                                    <p class="font-bold">اطلاعات حساب بانکی</p>
                                    <p>شماره حساب</p>
                                    <p>شماره شبا</p>
                                    <p>نام بانک و کد شعبه</p>
                                    <p class="font-bold">پرونده های تعمیرات</p>
                                    <p>سریال دستگاه</p>
                                    <p>نام مالک</p>
                                    <p>مدل</p>
                                    <p>توضیحات</p>
                                    <p>سریال دستگاه</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:mr-auto">
                            <button
                                class="relative bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <!--                                <div v-if="$page.user.notifications && $page.user.notifications.length > 0" style="top: -12px;left:12px"-->
                                <!--                                     class="absolute rounded-full bg-red-500 text-white w-6 h-6 flex items-center justify-center">-->
                                <!--                                    <div class="font-bold">{{$page.user.notifications && $page.user.notifications.length}}</div>-->
                                <!--                                </div>-->
                            </button>
                            <button @click="logout"
                                    class="bg-gray-800 p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                <i class="material-icons h-6 w-6">exit_to_app</i>
                            </button>
                        </div>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="-mr-2 flex md:hidden">
                        <button
                            class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                            <span class="sr-only">امکانات سیستم</span>

                            <svg v-if="isOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" @click="isOpen = !isOpen" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>

                            <svg v-else class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" @click="isOpen = !isOpen" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
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
        <jet-dialog maxWidth="3xl" :show="viewSearchResultModal" @close="viewSearchResultModal=false">
            <template #title>نتایج جستجوی جامع</template>
            <template #content>
                <div class="md:grid md:grid-cols-2 gap-3">
                    <div class="col-span-2 border border-gray-200 rounded">
                        <p class="search-results-box-header-title">پذیرنده ها</p>
                        <loading v-if="searchingProfiles"/>
                        <div v-else class="">
                            <div v-if="searchResults.profiles.length===0" class="flex items-center justify-center">
                                نتیجه ای یافت نشد.
                            </div>
                            <div v-else>
                                <div class="search-results-box-header-cells">
                                    <div class="w-1/4 text-center list-table-header-cell">نام پذیرنده</div>
                                    <div class="w-1/4 text-center list-table-header-cell">نوع پرونده</div>
                                    <div class="w-1/4 text-center list-table-header-cell">بازاریاب</div>
                                    <div class="w-1/4 text-center list-table-header-cell">دستگاه</div>
                                    <div class="w-1/4 text-center list-table-header-cell">وضعیت پرونده</div>
                                </div>
                                <div class="h-28 overflow-y-auto">
                                    <inertia-link v-for="profile in searchResults.profiles"
                                                  :key="'profiles_search_'+profile.id"
                                                  target="_blank"
                                                  :href="route('dashboard.profiles.view',{profileId:profile.id})">
                                        <div class="mx-1 my-2 flex justify-between">
                                            <div class="w-1/4 text-cen ter truncate list-table-body-cell">
                                                {{profile.customer &&
                                                profile.customer.fullName}}
                                            </div>
                                            <div class="w-1/4 text-center list-table-body-cell">{{profile.typeText}}
                                            </div>
                                            <div class="w-1/4 text-center list-table-body-cell">{{profile.user &&
                                                profile.user.name}}
                                            </div>
                                            <div class="w-1/4 text-center list-table-body-cell">
                                                {{profile.device && profile.device.serial}}
                                            </div>
                                            <div class="w-1/4 text-center list-table-body-cell">
                                                {{profile.statusText}}
                                            </div>
                                        </div>
                                    </inertia-link>
                                    <div class="flex justify-between">
                                            <span
                                                class="mx-2 my-1 p-1 text-blue-500 hover:text-blue-400 rounded cursor-pointer"
                                                :class="{'bg-gray-200' : link.active}"
                                                v-for="link in searchResults.profilesPagination"
                                                @click="searchProfiles(link.url)"
                                                :key="link" v-html="link.label"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded">
                        <p class="search-results-box-header-title">دستگاه ها</p>
                        <loading v-if="searchingDevices"/>
                        <div v-else class="">
                            <div v-if="searchResults.devices.length===0" class="flex items-center justify-center">
                                نتیجه ای یافت نشد.
                            </div>
                            <div v-else>
                                <div class="search-results-box-header-cells">
                                    <div class="w-1/3 text-center list-table-header-cell">سریال دستگاه</div>
                                    <div class="w-1/3 text-center list-table-header-cell">مدل</div>
                                    <div class="w-1/3 text-center list-table-header-cell">مالک</div>
                                </div>
                                <div class="h-28 overflow-y-auto">
                                    <inertia-link v-for="device in searchResults.devices"
                                                  :key="'devices_search_'+device.id"
                                                  target="_blank"
                                                  :href="route('dashboard.devices.view',{id:device.id})">
                                        <div class="mx-1 my-2 flex justify-between">
                                            <div class="w-1/3 text-center list-table-body-cell">{{device.serial}}</div>
                                            <div class="w-1/3 text-center list-table-body-cell">
                                                {{device.device_type && device.device_type.name}}
                                            </div>
                                            <div class="w-1/3 text-center list-table-body-cell">{{device.user &&
                                                device.user.name}}
                                            </div>
                                        </div>
                                    </inertia-link>
                                    <div class="flex justify-between">
                                            <span
                                                class="mx-2 my-1 p-1 text-blue-500 hover:text-blue-400 rounded cursor-pointer"
                                                :class="{'bg-gray-200' : link.active}"
                                                v-for="link in searchResults.devicesPagination"
                                                @click="searchDevices(link.url)"
                                                :key="link" v-html="link.label"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded">
                        <p class="search-results-box-header-title">تعمیرات</p>
                        <loading v-if="searchingRepairs"/>
                        <div v-else class="">
                            <div v-if="searchResults.repairs.length===0" class="flex items-center justify-center">
                                نتیجه ای یافت نشد.
                            </div>
                            <div v-else>
                                <div class="search-results-box-header-cells">
                                    <div class="w-1/3 text-center list-table-header-cell">نام پذیرنده</div>
                                    <div class="w-1/3 text-center list-table-header-cell">مدل</div>
                                    <div class="w-1/3 text-center list-table-header-cell">وضعیت</div>
                                </div>
                                <div class="h-28 overflow-y-auto">
                                    <inertia-link v-for="repair in searchResults.repairs"
                                                  :key="'repairs_search_'+repair.id"
                                                  target="_blank"
                                                  :href="route('dashboard.repairs.view',{id:repair.id})">
                                        <div class="mx-1 my-2 flex justify-between">
                                            <div class="w-1/3 text-center list-table-body-cell">{{repair.name}}</div>
                                            <div class="w-1/3 text-center list-table-body-cell">{{repair.device_type &&
                                                repair.device_type.name}}
                                            </div>
                                            <div class="w-1/3 text-center list-table-body-cell">{{repair.statusText}}
                                            </div>
                                        </div>
                                    </inertia-link>
                                    <div class="flex justify-between">
                                            <span
                                                class="mx-2 my-1 p-1 text-blue-500 hover:text-blue-400 rounded cursor-pointer"
                                                :class="{'bg-gray-200' : link.active}"
                                                v-for="link in searchResults.repairsPagination"
                                                @click="searchRepairs(link.url)"
                                                :key="link" v-html="link.label"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @click.native="viewSearchResultModal=false">بستن</jet-secondary-button>
            </template>
        </jet-dialog>
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
    import JetDialog from "@/Jetstream/DialogModal";
    import Loading from "@/Pages/Dashboard/Components/Loading";
    import JetSecondaryButton from "@/Jetstream/SecondaryButton"

    export default {
        components: {
            Loading,
            MobileSidebar,
            Sidebar,
            JetSecondaryButton,
            JetDialog,
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
                shadowLayer: true,

                searchResultsContainer: false,
                searchQuery: null,
                searchingProfiles: true,
                searchingDevices: true,
                searchingRepairs: true,
                searchResults: {
                    profiles: [],
                    profilePagination: [],
                    devices: [],
                    devicesPagination: [],
                    repairs: [],
                    repairsPagination: []
                },
                viewSearchResultModal: false,
            }
        },
        mounted() {
            Inertia.on('start', event => {
                this.loading = true;
            });

            Inertia.on('finish', event => {
                this.loading = false;
            });
            document.addEventListener('keyup', e => {
                if (e.key === '/' && e.ctrlKey === true) {
                    this.searchResultsContainer = true;
                    document.getElementById('searchQuery').focus();
                    document.getElementById('searchQuery').select();
                    e.preventDefault();
                }
            });
            document.getElementById('searchQuery').addEventListener('keyup', e => {
                if (e.key === 'Escape' && this.searchResultsContainer === true) {
                    this.searchResultsContainer = false;
                    document.getElementById('searchQuery').blur();
                    e.preventDefault();
                }
            });
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

            submitSearch() {
                if (this.searchQuery) {
                    this.viewSearchResultModal = true;
                    this.searchingProfiles = true;
                    this.searchingDevices = true;
                    this.searchingRepairs = true;
                    this.searchProfiles();
                } else {
                    document.getElementById('searchQuery').classList.add('error');
                }
            },
            searchProfiles(url) {
                if (!url) url = 'dashboard/searchProfiles';
                this.searchResults.profiles = [];
                this.searchingProfiles = true;
                axios.post(url, {query: this.searchQuery})
                    .then(response => {
                        let profiles = response.data.data;
                        let links = response.data.links;
                        this.searchResults.profiles = profiles;
                        this.searchResults.profilesPagination = links;
                    })
                    .catch(error => {

                    })
                    .finally(() => {
                        this.searchingProfiles = false;
                        this.searchDevices();
                    });
            },
            searchDevices() {
                this.searchResults.devices = [];
                this.searchingDevices = true;
                axios.post('dashboard/searchDevices', {query: this.searchQuery})
                    .then(response => {
                        let devices = response.data.data;
                        let links = response.data.links;
                        this.searchResults.devices = devices;
                        this.searchResults.devicesPagination = links;
                    })
                    .catch(error => {

                    })
                    .finally(() => {
                        this.searchingDevices = false;
                        this.searchRepairs();
                    });
            },
            searchRepairs() {
                this.searchResults.repairs = [];
                this.searchingRepairs = true;
                axios.post('dashboard/searchRepairs', {query: this.searchQuery})
                    .then(response => {
                        let repairs = response.data.data;
                        let links = response.data.links;
                        this.searchResults.repairs = repairs;
                        this.searchResults.repairsPagination = links;
                    })
                    .catch(error => {

                    })
                    .finally(() => {
                        this.searchingRepairs = false;
                    });
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
    .search-results-box-header-title {
        @apply font-bold text-center bg-gray-50 py-2;
    }

    .search-results-box-header-cells {
        @apply flex justify-between shadow;
    }

    .error {
        @apply border-red-500;
    }
</style>
