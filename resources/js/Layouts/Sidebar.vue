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
                        <li class="header-menu">
                            <span>پذیرندگان</span>
                        </li>
                        <li class="submenu">
                            <ul class="sidebar-submenu">
                                <li v-if="$page.user.level!='TECHNICAL'" class="sidebar-dropdown">
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
                        <!--                        <li class="header-menu">-->
                        <!--                            <span>دوره های آموزشی</span>-->
                        <!--                        </li>-->
                        <!--                        <li class="submenu">-->
                        <!--                            <ul class="sidebar-submenu">-->
                        <!--                                <li class="sidebar-dropdown">-->
                        <!--                                    <inertia-link :href="{ name: 'courses'}">-->
                        <!--                                        <i class="fa fa-bell"></i>-->
                        <!--                                        لیست دوره ها-->
                        <!--                                    </inertia-link>-->
                        <!--                                </li>-->
                        <!--                                <li class="sidebar-dropdown">-->
                        <!--                                    <inertia-link :href="{ name: 'courses-categories'}">-->
                        <!--                                        <i class="fa fa-shapes"></i>-->
                        <!--                                        دسته بندی ها-->
                        <!--                                        &lt;!&ndash;                                        <span class="badge badge-pill badge-warning">New</span>&ndash;&gt;-->
                        <!--                                    </inertia-link>-->
                        <!--                                </li>-->
                        <!--                            </ul>-->
                        <!--                        </li>-->
                        <li v-if="$page.user.level!='MARKETER'" class="header-menu">
                            <span>دستگاه ها</span>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT'" class="sidebar-dropdown">
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
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT'" class="header-menu">
                            <span>کاربران</span>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER'">
                            <inertia-link :href="route('dashboard.users.list',{type:'admin'})">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">people</i>
                                مدیران سیستم
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
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'" class="header-menu">
                            <span>تنظیمات</span>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.main')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">settings</i>
                                تنظیمات سیستم
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.devices.list')">
                                <i  class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">developer_mode</i>
                                دستگاه ها
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.banks.list')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">attach_money</i>
                                بانک ها
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.psps.list')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">miscellaneous_services</i>
                                سرویس دهندگان
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.repairTypes.list')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">handyman</i>
                                دلایل خرابی
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.repairLocations.list')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">location_city</i>
                                محل های تعمیرات
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.licenses.list')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">content_paste</i>
                                ارسال مدارک
                            </inertia-link>
                        </li>
                        <li v-if="$page.user.level=='SUPERUSER' || $page.user.level=='ADMIN'">
                            <inertia-link :href="route('dashboard.settings.main')">
                                <i class="material-icons h-5 w-5 text-center text-xl leading-5 align-middle">security</i>
                                پشتیبان گیری / بازیابی اطلاعات
                            </inertia-link>
                        </li>
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
        props:['user'],
        data() {
            return {}
        },
        mounted() {
        },
        methods: {}
    }
</script>

<style scoped lang="scss">
    /*.page-wrapper .sidebar-wrapper,*/
    /*.sidebar-wrapper .sidebar-brand > a,*/
    /*.sidebar-wrapper .sidebar-dropdown > a:after,*/
    /*.sidebar-wrapper .sidebar-menu .sidebar-dropdown .sidebar-submenu li a:before,*/
    /*.sidebar-wrapper ul li a i,*/
    /*.page-wrapper .page-content,*/
    /*.sidebar-wrapper .sidebar-search input.search-menu,*/
    /*.sidebar-wrapper .sidebar-search .input-group-text,*/
    /*.sidebar-wrapper .sidebar-menu ul li a,*/
    /*#show-sidebar,*/
    /*#close-sidebar {*/
    /*    -webkit-transition: all 0.3s ease;*/
    /*    -moz-transition: all 0.3s ease;*/
    /*    -ms-transition: all 0.3s ease;*/
    /*    -o-transition: all 0.3s ease;*/
    /*    transition: all 0.3s ease;*/
    /*}*/

    #sidebar {
        /*background-color: #31353d;*/
    }

    .sidebar-content {
        max-height: calc(100% - 30px);
        height: calc(100% - 30px);
        overflow-y: auto;
        position: relative;
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
        border-top: 1px solid #3a3f48;
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

        /* End Sidebar header */
        .sidebar-search {
            border-top: 1px solid #3a3f48;

            > div {
                padding: 10px 20px;
            }

            input.search-menu {
                background: #3a3f48;
                color: #818896;
                border-color: transparent;
                box-shadow: none;
            }

            .input-group-text {
                background: #3a3f48;
                color: #818896;
                border-color: transparent;
                box-shadow: none;
            }

        }

        /*End Sidebar Search */
        .sidebar-menu {
            border-top: 1px solid #3a3f48;
            padding-bottom: 10px;

            .header-menu {

                span {
                    color: #6c7b88;
                    font-weight: bold;
                    font-size: 14px;
                    padding: 15px 20px 5px 20px;
                    display: inline-block;
                    cursor: pointer
                }

            }

            ul {

                li {

                    a {
                        color: #818896;
                        display: inline-block;
                        width: 100%;
                        text-decoration: none;
                        position: relative;
                        padding: 8px 10px 8px 30px;
                        transition: all 0.3s ease;

                        i {
                            transition: all 0.3s ease;
                        }

                        span.badge {
                            float: left;
                            margin-top: 8px;
                        }

                    }
                }

                li:not(.submenu):hover {

                    a {
                        color: #b8bfce;

                        i {
                            color: #16c7ff;
                            text-shadow: 0 0 10px rgba(22, 199, 255, 0.5);
                        }

                    }
                }
            }

            .sidebar-dropdown {

                div {
                    background: #3a3f48;
                }

            }

            .sidebar-submenu {

                ul {
                    padding: 5px 0;
                }

                li {
                    padding-left: 25px;
                    font-size: 13px;

                    a {

                        .badge {
                            float: left;
                            margin-top: 0;
                        }

                    }
                }
            }
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
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

    .input-group > .form-control:not(:last-child) {
        border-radius: 0 3px 3px 0;
    }

    .input-group > .input-group-append > .input-group-text {
        border-radius: 3px 0 0 3px;
    }

    .input-group > .input-group-append:not(:last-child) > .input-group-text {
        border-radius: 0 3px 3px 0;
    }
</style>
