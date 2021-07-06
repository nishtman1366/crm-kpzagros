<template>
    <div>
        <nav class="sidebar-wrapper bg-gray-800" id="sidebar">
            <div class="sidebar-content">
                <!-- Start Sidebar Header -->
                <div class="sidebar-header">
                    <div class="user-pic">
                        <img id="user-avatar" class="img-responsive img-rounded"
                             :src="$page.user.profile_photo_url"
                             alt="User picture">
                    </div>
                    <div class="user-info">
                        <inertia-link :href="route('profile.show')">
                          <span class="user-name">
                            <strong>{{$page.user.name}}</strong>
                          </span></inertia-link>
                        <span class="user-role">{{$page.user.levelText}}</span>
                        <span class="user-status">
                            <i class="material-icons">lens</i>
                            <span>Online</span>
                          </span>
                    </div>
                </div>
                <!-- End Sidebar Header -->
                <!-- Start Sidebar Search -->
                <!--            <div class="sidebar-search">-->
                <!--                <div>-->
                <!--                    <div class="input-group">-->
                <!--                        <input type="text" class="form-control search-menu" placeholder="جستجو...">-->
                <!--                        <div class="input-group-append">-->
                <!--                                  <span class="input-group-text">-->
                <!--                                    <i class="fa fa-search" aria-hidden="true"></i>-->
                <!--                                  </span>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--            </div>-->
                <!-- End Sidebar Search -->
                <!-- Start Sidebar Menu -->
                <div class="sidebar-menu">
                    <ul>
                        <li>
                            <inertia-link :href="route('dashboard.main')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">dashboard</i>
                                داشبورد
                            </inertia-link>
                        </li>
                        <template v-if="$page.user.level!='TECHNICAL'">
                            <li class="header-menu" data-child="#menu-profiles">
                                پذیرندگان
                            </li>
                            <li id="menu-profiles" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li class="sidebar-dropdown">
                                        <inertia-link :href="route('dashboard.profiles.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">assignment_ind</i>
                                            لیست پذیرندگان
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level!='TECHNICAL'" class="sidebar-dropdown">
                                        <inertia-link :href="route('dashboard.profiles.create')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">assignment_ind</i>
                                            ثبت درخواست جدید
                                            <!--                                        <span class="badge badge-pill badge-warning">New</span>-->
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-if="$page.user.level!='MARKETER'">
                            <li class="header-menu" data-child="#menu-devices">
                                دستگاه ها
                            </li>
                            <li id="menu-devices" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li v-if="$page.user.level!='TECHNICAL' || $page.user.level!='MARKETER'"
                                        class="sidebar-dropdown">
                                        <inertia-link :href="route('dashboard.devices.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">dock</i>
                                            لیست دستگاه ها
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level!='MARKETER'" class="sidebar-dropdown">
                                        <inertia-link :href="route('dashboard.repairs.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">construction</i>
                                            تعمیرات
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level!='MARKETER'" class="sidebar-dropdown">
                                        <inertia-link :href="route('dashboard.returns.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">keyboard_return</i>
                                            عودت دستگاه
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <li class="header-menu" data-child="#menu-news">
                                اخبار و اطلاعیه ها
                            </li>
                            <li id="menu-news" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li>
                                        <inertia-link :href="route('dashboard.posts.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">article</i>
                                            لیست اخبار
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.posts.categories.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">list</i>
                                            دسته بندی ها
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template
                            v-if="$page.user.level!=='TECHNICAL' && $page.user.level!=='MARKETER' && $page.user.level!=='OFFICE'">
                            <li class="header-menu" data-child="#menu-users">
                                کاربران
                            </li>
                            <li id="menu-users" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li v-if="$page.user.level=='SUPERUSER'">
                                        <inertia-link :href="route('dashboard.users.list',{type:'admin'})">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">people</i>
                                            مدیران سیستم
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.users.list',{type:'office'})">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">reduce_capacity</i>
                                            کارمندان دفتر
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.users.list',{type:'agent'})">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">reduce_capacity</i>
                                            نمایندگان
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT'">
                                        <inertia-link :href="route('dashboard.users.list',{type:'marketer'})">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">person</i>
                                            بازاریابان
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.users.list',{type:'technical'})">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">engineering</i>
                                            کارشناسان فنی
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template>
                            <li class="header-menu" data-child="#menu-tickets">
                                پشتیبانی
                            </li>
                            <li id="menu-tickets" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li>
                                        <inertia-link :href="route('dashboard.tickets.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">comment</i>
                                            لیست درخواست ها
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.tickets.agents.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">group</i>
                                            کاربران
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.tickets.types.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">forum</i>
                                            واحدها
                                        </inertia-link>
                                    </li>
                                    <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                                        <inertia-link :href="route('dashboard.notifications.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">forum</i>
                                            اعلان های گروهی
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <li class="header-menu" data-child="#menu-reports">
                                گزارشات
                            </li>
                            <li id="menu-reports" class="submenu">
                                <ul class="sidebar-submenu">

                                    <li>
                                        <inertia-link :href="route('dashboard.reports.main')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">signal_cellular_alt</i>
                                            خلاصه
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.reports.profiles')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">insights</i>
                                            پرونده ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.reports.devices')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">query_stats</i>
                                            دستگاه ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.reports.repairs')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">donut_small</i>
                                            تعمیرات
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                        <template v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <li class="header-menu" data-child="#menu-settings">
                                تنظیمات
                            </li>
                            <li id="menu-settings" class="submenu">
                                <ul class="sidebar-submenu">
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.main')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">settings</i>
                                            تنظیمات سیستم
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.devices.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">developer_mode</i>
                                            دستگاه ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.banks.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">attach_money</i>
                                            بانک ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.psps.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">miscellaneous_services</i>
                                            سرویس دهندگان
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.repairTypes.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">handyman</i>
                                            دلایل خرابی
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.repairLocations.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">location_city</i>
                                            محل های تعمیرات
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.licenses.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">content_paste</i>
                                            ارسال مدارک
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.notifications.types.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">textsms</i>
                                            انواع اعلان ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.notifications.events.list')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">sms_failed</i>
                                            تنظیمات اعلان ها
                                        </inertia-link>
                                    </li>
                                    <li>
                                        <inertia-link :href="route('dashboard.settings.main')">
                                            <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">security</i>
                                            پشتیبان گیری / بازیابی اطلاعات
                                        </inertia-link>
                                    </li>
                                </ul>
                            </li>
                        </template>
                    </ul>
                </div>
                <!-- End Sidebar Menu -->
            </div>
            <!-- Start Sidebar Footer -->
        </nav>
    </div>
    <!--    <b-sidebar id="sidebar-right" title="Sidebar" width="250px" z-index="100" no-header right shadow>-->
    <!--        <template v-slot:footer="{ hide }">-->
    <!--            <div class="sidebar-footer">-->
    <!--                <a href="#" id="notifications-alert">-->
    <!--                    <i class="fa fa-bell"></i>-->
    <!--                    <span class="badge badge-pill badge-warning notification">4</span>-->
    <!--                </a>-->
    <!--                <a href="#">-->
    <!--                    <i class="fa fa-envelope"></i>-->
    <!--                    <span class="badge badge-pill badge-success notification">7</span>-->
    <!--                </a>-->
    <!--                <a href="#">-->
    <!--                    <i class="fa fa-cog"></i>-->
    <!--                    <span class="badge-sonar"></span>-->
    <!--                </a>-->
    <!--                <a href="#"-->
    <!--                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">-->
    <!--                    <i class="fa fa-power-off"></i>-->
    <!--                </a>-->
    <!--                <form id="logout-form" action="" method="POST"-->
    <!--                      style="display: none;">-->
    <!--                </form>-->
    <!--            </div>-->
    <!--        </template>-->
    <!--        -->
    <!--    </b-sidebar>-->

</template>

<script>
    export default {
        name: "Sidebar",
        props: ['user'],
        data() {
            return {}
        },
        mounted() {
            // let headers = document.querySelectorAll('.header-menu');
            // headers.forEach(header => {
            //     header.addEventListener('click', (e) => {
            //         let moreIcon = document.querySelectorAll('.more').forEach(icon => {
            //             icon.classList.remove('hidden');
            //         });
            //         let lessIcon = document.querySelectorAll('.less').forEach(icon => {
            //             icon.classList.add('hidden');
            //         })
            //
            //         let menuItem = e.target;
            //         let icons = menuItem.querySelectorAll(":scope > i");
            //         icons.forEach(icon => {
            //             icon.classList.toggle('hidden');
            //         })
            //         let submenu = document.querySelector(menuItem.getAttribute('data-child'));
            //         if (submenu) {
            //             let submenuItems = document.querySelectorAll('.submenu');
            //             submenuItems.forEach(sub => {
            //                 if (sub !== submenu) {
            //                     sub.classList.add('hidden');
            //                 }
            //             });
            //             submenu.classList.toggle('hidden');
            //         }
            //     });
            // });
        },
        methods: {}
    }
</script>

<style scoped lang="scss">
    .sidebar-menu {
        @apply border-t border-gray-200 pb-2;

        .header-menu {
            @apply cursor-pointer text-white font-bold px-3 pt-3 pb-1 block;

            :hover {
                @apply text-indigo-500;
            }

            i {
                @apply h-5 w-5 text-center text-xl leading-5 align-middle;
            }
        }

        ul {
            li {
                a {
                    @apply text-white inline-block w-full relative py-2 pr-3 pl-5;

                    i {

                    }

                    span.badge {
                        @apply float-left mt-2;
                    }
                }
            }

            li:not(.submenu):hover {

                a {
                    @apply text-white;

                    i {
                        @apply text-indigo-500;
                    }
                }
            }
        }

        .sidebar-dropdown {

            div {
                @apply bg-gray-300;
            }

        }

        .sidebar-submenu {

            ul {
                @apply px-1 py-0;
            }

            li {
                @apply pl-5 text-sm;

                a {

                    .badge {
                        @apply float-left mt-0;
                    }

                }
            }
        }
    }


    #sidebar {
        /*background-color: #31353d;*/
    }

    .sidebar-content {
        @apply overflow-y-auto relative h-full;
        /*max-height: calc(100% - 30px);*/
        /*height: calc(100% - 30px);*/
    }

    .sidebar-brand {
        cursor: pointer;
        font-size: 20px;

        .sidebar-wrapper {
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }

        .sidebar-wrapper {
            text-transform: uppercase;
            font-weight: bold;
            flex-grow: 1;
        }

        .sidebar-wrapper > a {
            color: #818896;
            text-transform: uppercase;
            font-weight: bold;
            flex-grow: 1;
        }

    }

    #close-sidebar {
        color: #bdbdbd;
        cursor: pointer;
        font-size: 20px;
    }

    .sidebar-header {
        @apply border-t-2 border-gray-100;
    }

    .sidebar-content .sidebar-header {
        padding: 20px;
        overflow: hidden;
    }

    .sidebar-wrapper {

        .sidebar-header {

            .user-pic {
                float: right;
                width: 60px;
                padding: 2px;
                border-radius: 12px;
                margin-left: 15px;
                overflow: hidden;

                img {
                    object-fit: cover;
                    height: 100%;
                    width: 100%;
                }

            }

            .user-info {
                color: #b8bfce;

                > span {
                    display: block;
                }

                .user-role {
                    color: #818896;
                    font-size: 12px;
                }

                .user-status {
                    font-size: 11px;
                    margin-top: 4px;

                    i {
                        font-size: 8px;
                        margin-right: 4px;
                        color: #5cb85c;
                    }

                }
            }
        }

        ul {
            @apply p-0 m-0 list-none;
        }

        /* End Sidebar Menu */
    }

    .sidebar-footer {
        background: #3a3f48;
        box-shadow: 0px -1px 5px #282c33;
        border-top: 1px solid #464a52;
        position: absolute;
        width: 96%;
        bottom: 0;
        right: 0;
        display: flex;

        > a {
            color: #818896;
            flex-grow: 1;
            text-align: center;
            height: 30px;
            line-height: 30px;
            position: relative;

            :first-child {
                border-left: none;
            }

            .notification {
                position: absolute;
                top: 0;
            }

        }

        > a:hover {

            i {
                color: #b8bfce;
            }

        }
    }
</style>
