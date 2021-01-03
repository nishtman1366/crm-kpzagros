<template>
    <div>
        <div class="block sm:hidden p-3 rounded bg-gray-600">
            <div class="p-2 m-1 grid grid-cols-8 rounded"
                 :class="step===1 ? 'bg-pink-600 text-white' : 'bg-gray-300'">
                <div class="col-1 self-center">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                         clip-rule="evenodd">
                        <path fill="currentColor"
                              d="M23.995 24h-1.995c0-3.104.119-3.55-1.761-3.986-2.877-.664-5.594-1.291-6.584-3.458-.361-.791-.601-2.095.31-3.814 2.042-3.857 2.554-7.165 1.403-9.076-1.341-2.229-5.413-2.241-6.766.034-1.154 1.937-.635 5.227 1.424 9.025.93 1.712.697 3.02.338 3.815-.982 2.178-3.675 2.799-6.525 3.456-1.964.454-1.839.87-1.839 4.004h-1.995l-.005-1.241c0-2.52.199-3.975 3.178-4.663 3.365-.777 6.688-1.473 5.09-4.418-4.733-8.729-1.35-13.678 3.732-13.678 4.983 0 8.451 4.766 3.732 13.678-1.551 2.928 1.65 3.624 5.09 4.418 2.979.688 3.178 2.143 3.178 4.663l-.005 1.241zm-13.478-6l.91 2h1.164l.92-2h-2.994zm2.995 6l-.704-3h-1.615l-.704 3h3.023z"/>
                    </svg>
                </div>
                <div class="col-span-7 p-1">
                    <p class="font-base font-bold">
                        <inertia-link v-if="customerInfo && edit"
                                      :href="route('dashboard.profiles.customers.edit',{profileId})">
                            مشتری
                        </inertia-link>
                        <span v-else>مشتری</span>
                    </p>
                    <p class="font-sm">
                        اطلاعات حقیقی و حقوقی مشتری
                    </p>
                </div>
            </div>
            <div class="p-2 m-1 grid grid-cols-8 rounded"
                 :class="step===2 ? 'bg-pink-600 text-white' : 'bg-gray-300'">
                <div class="col-1 self-center">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                         clip-rule="evenodd">
                        <path fill="currentColor"
                              d="M24 22h-24v-20h24v20zm-1-19h-22v18h22v-18zm-4 13v1h-4v-1h4zm-6.002 1h-10.997l-.001-.914c-.004-1.05-.007-2.136 1.711-2.533.789-.182 1.753-.404 1.892-.709.048-.108-.04-.301-.098-.407-1.103-2.036-1.305-3.838-.567-5.078.514-.863 1.448-1.359 2.562-1.359 1.105 0 2.033.488 2.545 1.339.737 1.224.542 3.033-.548 5.095-.057.106-.144.301-.095.41.14.305 1.118.531 1.83.696 1.779.41 1.773 1.503 1.767 2.56l-.001.9zm-9.998-1h8.999c.003-1.014-.055-1.27-.936-1.473-1.171-.27-2.226-.514-2.57-1.267-.174-.381-.134-.816.119-1.294.921-1.739 1.125-3.199.576-4.111-.332-.551-.931-.855-1.688-.855-.764 0-1.369.31-1.703.871-.542.91-.328 2.401.587 4.09.259.476.303.912.13 1.295-.342.757-1.387.997-2.493 1.252-.966.222-1.022.478-1.021 1.492zm18-3v1h-6v-1h6zm0-3v1h-6v-1h6zm0-3v1h-6v-1h6z"/>
                    </svg>
                </div>
                <div class="col-span-7 p-1">
                    <inertia-link v-if="businessInfo && edit"
                                  :href="route('dashboard.profiles.businesses.edit',{profileId})">
                        <p class="font-base font-bold">
                            کسب و کار</p>
                    </inertia-link>
                    <span v-else>کسب و کار</span>
                    <p class="font-sm">اطلاعات کسب و کار</p>
                </div>
            </div>
            <div class="p-2 m-1 grid grid-cols-8 rounded"
                 :class="step===3 ? 'bg-pink-600 text-white' : 'bg-gray-300'">
                <div class="col-1 self-center">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                         clip-rule="evenodd">
                        <path fill="currentColor"
                              d="M16 11.5C16 10.12 17.12 9 18.5 9S21 10.12 21 11.5 19.88 14 18.5 14 16 12.88 16 11.5M13 3V20H24V3H13M22 16C20.9 16 20 16.9 20 18H17C17 16.9 16.11 16 15 16V7C16.11 7 17 6.11 17 5H20C20 6.11 20.9 7 22 7V16M7 6C8.1 6 9 6.9 9 8S8.1 10 7 10 5 9.1 5 8 5.9 6 7 6M7 4C4.79 4 3 5.79 3 8S4.79 12 7 12 11 10.21 11 8 9.21 4 7 4M7 14C3.13 14 0 15.79 0 18V20H11V18H2C2 17.42 3.75 16 7 16C8.83 16 10.17 16.45 11 16.95V14.72C9.87 14.27 8.5 14 7 14Z"/>
                    </svg>
                </div>
                <div class="col-span-7 m-1 p-1">
                    <inertia-link v-if="accountsInfo && edit"
                                  :href="route('dashboard.profiles.accounts.edit',{profileId})">
                        <p class="font-base font-bold">
                            حساب های بانکی
                        </p>
                    </inertia-link>
                    <span v-else>حساب های بانکی</span>
                    <p class="font-sm">اطلاعات حساب های بانکی مشتری</p>
                </div>
            </div>
            <div class="p-2 m-1 grid grid-cols-8 rounded"
                 :class="step===4 ? 'bg-pink-600 text-white' : 'bg-gray-300'">
                <div class="col-1 self-center ">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                         clip-rule="evenodd">
                        <path fill="currentColor"
                              d="M3,4H20A2,2 0 0,1 22,6V8H18V6H5V18H14V20H3A2,2 0 0,1 1,18V6A2,2 0 0,1 3,4M17,10H23A1,1 0 0,1 24,11V21A1,1 0 0,1 23,22H17A1,1 0 0,1 16,21V11A1,1 0 0,1 17,10M18,12V19H22V12H18Z"/>
                    </svg>
                </div>
                <div class="col-span-7 m-1 p-1 bg-gray-300 rounded"
                     :class="step===4 ? 'bg-pink-600' : 'bg-gray-300'">
                    <p class="font-base font-bold">
                        <inertia-link v-if="deviceInfo && edit"
                                      :href="route('dashboard.profiles.devices.edit',{profileId})">
                            دستگاه
                        </inertia-link>
                        <span v-else>دستگاه</span>
                    </p>
                    <p class="font-sm">انتخاب دستگاه</p>
                </div>
            </div>
            <div class="p-2 m-1 grid grid-cols-8 rounded"
                 :class="step===5 ? 'bg-pink-600 text-white' : 'bg-gray-300'">
                <div class="col-1 self-center">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                         clip-rule="evenodd">
                        <path fill="currentColor"
                              d="M21 6.285l-11.16 12.733-6.84-6.018 1.319-1.49 5.341 4.686 9.865-11.196 1.475 1.285z"/>
                    </svg>
                </div>
                <div class="col-span-7 m-1 p-1 bg-gray-300 rounded"
                     :class="step===5 ? 'bg-pink-600' : 'bg-gray-300'">
                    <p class="font-base font-bold">جمع بندی</p>
                    <p class="font-sm">بررسی نهایی اطلاعات و جمع بندی!</p>
                </div>
            </div>
        </div>
        <div class="hidden md:flex m-2">
            <div class="w-1/5 text-center px-6">
                <div
                    :class="step===1 ? 'bg-pink-600' : 'bg-gray-300'"
                    class=" rounded-lg flex items-center justify-center border border-gray-200"
                >
                    <div
                        class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M23.995 24h-1.995c0-3.104.119-3.55-1.761-3.986-2.877-.664-5.594-1.291-6.584-3.458-.361-.791-.601-2.095.31-3.814 2.042-3.857 2.554-7.165 1.403-9.076-1.341-2.229-5.413-2.241-6.766.034-1.154 1.937-.635 5.227 1.424 9.025.93 1.712.697 3.02.338 3.815-.982 2.178-3.675 2.799-6.525 3.456-1.964.454-1.839.87-1.839 4.004h-1.995l-.005-1.241c0-2.52.199-3.975 3.178-4.663 3.365-.777 6.688-1.473 5.09-4.418-4.733-8.729-1.35-13.678 3.732-13.678 4.983 0 8.451 4.766 3.732 13.678-1.551 2.928 1.65 3.624 5.09 4.418 2.979.688 3.178 2.143 3.178 4.663l-.005 1.241zm-13.478-6l.91 2h1.164l.92-2h-2.994zm2.995 6l-.704-3h-1.615l-.704 3h3.023z"/>
                        </svg>
                    </div>
                    <div
                        :class="step===1 ? 'bg-pink-300' : 'bg-gray-200'"
                        class="w-2/3 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
                    >
                        <h2 class="font-bold text-sm">
                            <inertia-link v-if="customerInfo && edit"
                                          :href="route('dashboard.profiles.customers.edit',{profileId})">
                                مشتری
                            </inertia-link>
                            <span v-else>مشتری</span>
                        </h2>
                        <p class="text-xs text-gray-600">
                            اطلاعات حقیقی و حقوقی مشتری
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor"
                          d="M20,10V14H11L14.5,17.5L12.08,19.92L4.16,12L12.08,4.08L14.5,6.5L11,10H20Z"/>
                </svg>
            </div>
            <div class="w-1/5 text-center px-6">
                <div
                    :class="step===2 ? 'bg-pink-600' : 'bg-gray-300'"
                    class="rounded-lg flex items-center justify-center border border-gray-200"
                >
                    <div
                        class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step"
                    >
                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                             clip-rule="evenodd">
                            <path
                                d="M24 22h-24v-20h24v20zm-1-19h-22v18h22v-18zm-4 13v1h-4v-1h4zm-6.002 1h-10.997l-.001-.914c-.004-1.05-.007-2.136 1.711-2.533.789-.182 1.753-.404 1.892-.709.048-.108-.04-.301-.098-.407-1.103-2.036-1.305-3.838-.567-5.078.514-.863 1.448-1.359 2.562-1.359 1.105 0 2.033.488 2.545 1.339.737 1.224.542 3.033-.548 5.095-.057.106-.144.301-.095.41.14.305 1.118.531 1.83.696 1.779.41 1.773 1.503 1.767 2.56l-.001.9zm-9.998-1h8.999c.003-1.014-.055-1.27-.936-1.473-1.171-.27-2.226-.514-2.57-1.267-.174-.381-.134-.816.119-1.294.921-1.739 1.125-3.199.576-4.111-.332-.551-.931-.855-1.688-.855-.764 0-1.369.31-1.703.871-.542.91-.328 2.401.587 4.09.259.476.303.912.13 1.295-.342.757-1.387.997-2.493 1.252-.966.222-1.022.478-1.021 1.492zm18-3v1h-6v-1h6zm0-3v1h-6v-1h6zm0-3v1h-6v-1h6z"/>
                        </svg>
                    </div>
                    <div
                        :class="step===2 ? 'bg-pink-300' : 'bg-gray-200'"
                        class="w-2/3 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
                    >
                        <h2 class="font-bold text-sm">
                            <inertia-link v-if="businessInfo && edit"
                                          :href="route('dashboard.profiles.businesses.edit',{profileId})">
                                کسب و کار
                            </inertia-link>
                            <span v-else>کسب و کار</span>
                        </h2>
                        <p class="text-xs text-gray-600">
                            اطلاعات کسب و کار
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor"
                          d="M20,10V14H11L14.5,17.5L12.08,19.92L4.16,12L12.08,4.08L14.5,6.5L11,10H20Z"/>
                </svg>
            </div>
            <div class="w-1/5 text-center px-6">
                <div
                    :class="step===3 ? 'bg-pink-600' : 'bg-gray-300'"
                    class="bg-gray-300 rounded-lg flex items-center justify-center border border-gray-200"
                >
                    <div class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step">
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M16 11.5C16 10.12 17.12 9 18.5 9S21 10.12 21 11.5 19.88 14 18.5 14 16 12.88 16 11.5M13 3V20H24V3H13M22 16C20.9 16 20 16.9 20 18H17C17 16.9 16.11 16 15 16V7C16.11 7 17 6.11 17 5H20C20 6.11 20.9 7 22 7V16M7 6C8.1 6 9 6.9 9 8S8.1 10 7 10 5 9.1 5 8 5.9 6 7 6M7 4C4.79 4 3 5.79 3 8S4.79 12 7 12 11 10.21 11 8 9.21 4 7 4M7 14C3.13 14 0 15.79 0 18V20H11V18H2C2 17.42 3.75 16 7 16C8.83 16 10.17 16.45 11 16.95V14.72C9.87 14.27 8.5 14 7 14Z"/>
                        </svg>
                    </div>
                    <div
                        :class="step===3 ? 'bg-pink-300' : 'bg-gray-200'"
                        class="w-2/3 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
                    >
                        <p class="font-base font-bold">
                            <inertia-link v-if="accountsInfo && edit"
                                          :href="route('dashboard.profiles.accounts.edit',{profileId})">
                                حساب های بانکی
                            </inertia-link>
                            <span v-else>حساب های بانکی</span>
                        </p>
                        <p class="text-xs text-gray-600">
                            اطلاعات حساب های بانکی مشتری
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor"
                          d="M20,10V14H11L14.5,17.5L12.08,19.92L4.16,12L12.08,4.08L14.5,6.5L11,10H20Z"/>
                </svg>
            </div>
            <div class="w-1/5 text-center px-6">
                <div
                    :class="step===4 ? 'bg-pink-600' : 'bg-gray-300'"
                    class="rounded-lg flex items-center justify-center border border-gray-200"
                >
                    <div
                        class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step"
                    >
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="M3,4H20A2,2 0 0,1 22,6V8H18V6H5V18H14V20H3A2,2 0 0,1 1,18V6A2,2 0 0,1 3,4M17,10H23A1,1 0 0,1 24,11V21A1,1 0 0,1 23,22H17A1,1 0 0,1 16,21V11A1,1 0 0,1 17,10M18,12V19H22V12H18Z"/>
                        </svg>
                    </div>
                    <div
                        :class="step===4 ? 'bg-pink-300' : 'bg-gray-200'"
                        class="w-2/3 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
                    >
                        <p class="font-base font-bold">
                            <inertia-link v-if="deviceInfo && edit"
                                          :href="route('dashboard.profiles.devices.edit',{profileId})">
                                دستگاه
                            </inertia-link>
                            <span v-else>دستگاه</span>
                        </p>
                        <p class="text-xs text-gray-600">
                            انتخاب دستگاه
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                    <path fill="currentColor"
                          d="M20,10V14H11L14.5,17.5L12.08,19.92L4.16,12L12.08,4.08L14.5,6.5L11,10H20Z"/>
                </svg>
            </div>
            <div class="w-1/5 text-center px-6">
                <div
                    :class="step===5 ? 'bg-pink-600' : 'bg-gray-300'"
                    class="rounded-lg flex items-center justify-center border border-gray-200"
                >
                    <div class="w-1/3 bg-transparent h-20 flex items-center justify-center icon-step">
                        <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                             clip-rule="evenodd">
                            <path
                                d="M21 6.285l-11.16 12.733-6.84-6.018 1.319-1.49 5.341 4.686 9.865-11.196 1.475 1.285z"/>
                        </svg>
                    </div>
                    <div
                        :class="step===5 ? 'bg-pink-300' : 'bg-gray-200'"
                        class="w-2/3 bg-gray-200 h-24 flex flex-col items-center justify-center px-1 rounded-r-lg body-step"
                    >
                        <h2 class="font-bold text-sm">
                            <inertia-link :href="route('dashboard.profiles.view',{profileId})">
                                جمع بندی
                            </inertia-link>
                        </h2>
                        <p class="text-xs text-gray-600">
                            بررسی نهایی اطلاعات و جمع بندی!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ProfileSteps",
        props: {
            edit: false,
            step: Number,
            customerInfo: false,
            businessInfo: false,
            accountsInfo: false,
            deviceInfo: false,
            profileId: ''
        }
    }
</script>

<style scoped>

</style>
