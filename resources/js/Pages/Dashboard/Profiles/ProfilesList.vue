<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در کدملی مشتری"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <button v-on:click="submitSearchForm"
                                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        جستجو
                                    </button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <InertiaLink :href="route('dashboard.profiles.create',)">
                                        <jet-button class="my-5 mx-1 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M10,4L12,6H20A2,2 0 0,1 22,8V18A2,2 0 0,1 20,20H4C2.89,20 2,19.1 2,18V6C2,4.89 2.89,4 4,4H10M15,9V12H12V14H15V17H17V14H20V12H17V9H15Z"/>
                                            </svg>
                                            ثبت پذیرنده جدید
                                        </jet-button>
                                    </InertiaLink>
                                    <jet-button v-show="$page.user.level=='ADMIN' || $page.user.level=='SUPERUSER'"
                                                @click.native="uploadExcel"
                                                class="my-5 mx-1 bg-green-600 hover:bg-green-500 sm:float-left">
                                        <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                  d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                        </svg>
                                        ورود اطلاعات
                                    </jet-button>
                                    <a v-show="$page.user.level=='ADMIN' || $page.user.level=='SUPERUSER'"
                                       target="_blank"
                                       :href="route('dashboard.profiles.downloadExcel',{
                                                query: query,
                                                statusId: status_id,
                                                agentId: agent_id,
                                                marketerId: marketer_id,
                                            })">
                                        <jet-button class="my-5 mx-1 bg-yellow-600 hover:bg-yellow-500 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                            </svg>
                                            دریافت لیست
                                        </jet-button>
                                    </a>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <select id="status_id"
                                            name="status_id"
                                            ref="status_id"
                                            v-model="status_id"
                                            autocomplete="status_id"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس وضعیت پرونده"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">وضعیت پرونده</option>
                                        <option v-for="statusItem in statuses" :key="statusItem.id"
                                                :value="statusItem.id">{{statusItem.name}}
                                        </option>
                                    </select>
                                    <select v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                            id="agent_id"
                                            name="agent_id"
                                            ref="agent_id"
                                            v-model="agent_id"
                                            autocomplete="agent_id"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس نماینده"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">نماینده</option>
                                        <option v-for="agent in agents" :key="agent.id"
                                                :value="agent.id">{{agent.name}}
                                        </option>
                                    </select>
                                    <select v-if="$page.user.level!=='MARKETER'" id="marketer_id"
                                            name="marketer_id"
                                            ref="marketer_id"
                                            v-model="marketer_id"
                                            autocomplete="marketer_id"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس بازاریاب"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">بازاریاب</option>
                                        <option v-for="marketer in marketers" :key="marketer.id"
                                                :value="marketer.id">{{marketer.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <date-picker
                                        @change="submitFromDate"
                                        v-model="from_date"
                                        element="from_date"
                                        ref="from_date_cal"></date-picker>
                                    <jet-input placeholder="تاریخ شروع"
                                               name="from_date"
                                               id="from_date"
                                               ref="from_date"
                                               v-model="from_date"
                                               readonly/>
                                    <date-picker
                                        @change="submitToDate"
                                        v-model="to_date"
                                        element="to_date"
                                        ref="to_date_cal"></date-picker>
                                    <jet-input placeholder="تاریخ پایان"
                                               name="to_date"
                                               id="to_date"
                                               ref="to_date"
                                               v-model="to_date"
                                               readonly/>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام پذیرنده
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نوع خدمات
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        بازاریاب
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        زمان ثبت
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        آخرین تغییرات
                                    </th>
                                    <th scope="col" class="py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="profile in profiles.data" :key="profile.id">
                                    <td class="py-4 text-center text-gray-900">
                                        {{profile.customer.fullName}}
                                        <p
                                            class="text-indigo-600">({{profile.customer.national_code}})</p>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        دستگاه کارتخوان
                                        <p
                                            class="text-indigo-600">({{profile.psp ? profile.psp.name : 'نامشخص'}})</p>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <p class="text-sm text-gray-900">{{profile.user.name}}</p>
                                        <p class="text-sm text-indigo-500">
                                            {{profile.user.parent ? 'نماینده: '+profile.user.parent.name : ''}}
                                        </p>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(profile.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{profile.statusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">{{profile.jCreatedAt}}</td>
                                    <td class="py-4 text-center text-gray-900">{{profile.jUpdatedAt}}</td>
                                    <td class="py-4 text-center text-gray-900">
                                        <InertiaLink
                                            :href="route('dashboard.profiles.view',{profileId: profile.id})"
                                            class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                            <button title="مشاهده پرونده"
                                                    v-b-tooltip.hover>
                                                <i id="test" class="material-icons">folder_shared</i>
                                            </button>
                                        </InertiaLink>
                                        <button
                                            v-if="profile.status==1 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT')"
                                            v-on:click="updateProfileStatus(profile.id,2)"
                                            class="text-green-400 hover:text-green-500"
                                            title="دردست بررسی"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">check_circle</i>
                                        </button>
                                        <button
                                            v-if="profile.status==3 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN')"
                                            v-on:click="updateProfileStatus(profile.id,4)"
                                            class="text-green-400 hover:text-green-500"
                                            title="ثبت در psp"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">assignment_turned_in</i>
                                        </button>
                                        <button
                                            v-if="profile.status==5 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT')"
                                            v-on:click="viewDevicesModal(profile.id)"
                                            class="text-green-400 hover:text-green-500"
                                            title="انتخاب دستگاه"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">keyboard_hide</i>
                                        </button>
                                        <button
                                            v-if="profile.status==6 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN')"
                                            v-on:click="selectTerminal(profile.id)"
                                            class="text-blue-600 hover:text-blue-700"
                                            title="تخصیص"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">sim_card</i>
                                        </button>
                                        <button v-if="profile.status==7"
                                                v-on:click="updateProfileStatus(profile.id,8)"
                                                class="text-green-400 hover:text-green-500"
                                                title="نصب دستگاه"
                                                v-b-tooltip.hover>
                                            <i class="material-icons">phonelink_setup</i>
                                        </button>
                                        <button v-if="profile.status == 7 || profile.status == 8"
                                                v-on:click="changeSerialRequest(profile.id,profile.device_type_id)"
                                                class="text-yellow-600 hover:text-yellow-700"
                                                title="جابجایی سریال"
                                                v-b-tooltip.hover>
                                            <i class="material-icons">undo</i>
                                        </button>
                                        <button
                                            v-if="profile.status == 14 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='AGENT')"
                                            v-on:click="selectNewSerial(profile.id,profile.change_reason)"
                                            class="text-blue-600 hover:text-blue-700"
                                            title="تخصیص سریال جدید"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">change_circle</i>
                                        </button>
                                        <button
                                            v-if="profile.status == 15 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN')"
                                            v-on:click="confirmChangeSerial(profile.id,profile.change_reason)"
                                            class="text-blue-600 hover:text-blue-700"
                                            title="تایید جابجایی"
                                            v-b-tooltip.hover>
                                            <i class="material-icons">change_circle</i>
                                        </button>
                                        <button v-if="profile.status == 8"
                                                v-on:click="cancelRequest(profile.id)"
                                                class="text-red-600 hover:text-red-700"
                                                title="درخواست فسخ"
                                                v-b-tooltip.hover>
                                            <i class="material-icons">cancel</i>
                                        </button>
                                        <button v-if="profile.status == 12"
                                                v-on:click="confirmCancel(profile.id,profile.cancel_reason)"
                                                class="text-yellow-600 hover:text-yellow-700"
                                                title="تایید فسخ"
                                                v-b-tooltip.hover>
                                            <i class="material-icons">block</i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination
                                :urlsArray="paginatedLinks"
                                :previousPageUrl="profiles.prev_page_url"
                                :nextPageUrl="profiles.next_page_url"
                            >
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>
            <!-- انتخاب سریال -->
            <jet-confirmation-modal :show="viewSearchModal" @close="viewSearchModal = false">
                <template #title>
                    جستجوی شماره سریال
                </template>
                <template #content>
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                            <input type="text"
                                   class=" inline-flex shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border"
                                   placeholder="جستجوی سریال"
                                   ref="search_serial"
                                   id="search_serial"
                                   v-model="search.serial"/>
                            <button type="button"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    v-on:click="searchDeviceSerials">
                                جستجو
                            </button>

                            <div class="mt-2">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            شماره سریال
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وضعیت فیزیکی
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">

                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="device in search.results" :key="device.id">
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            {{device.serial}}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            {{device.status2Text}}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            <button type="button"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                                    v-on:click="selectDevice(device)">
                                                انتخاب
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="viewSearchModal = false">
                        انصراف
                    </jet-secondary-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید سریال انتخاب شده -->
            <jet-confirmation-modal :show="confirmSerial" @close="confirmSerial = false">
                <template #title>
                    تایید شماره سریال
                </template>
                <template #content>
                    آیا از انتخاب این سریال مطمئن هستید؟

                    <div class="mt-4">
                        {{selectedSerial}}
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="confirmSerial = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="submitSerial"
                                       :class="{ 'opacity-25': serialForm.processing }"
                                       :disabled="serialForm.processing">
                        تایید
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- ثبت شماره ترمینال و پذیرنده -->
            <jet-confirmation-modal :show="viewTerminalModal" @close="viewTerminalModal = false">
                <template #title>
                    ثبت شماره پایانه و پذیرنده
                </template>
                <template #content>
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                            <div>
                                <label for="terminal_id"
                                       class="block text-sm font-medium text-gray-700">شماره پایانه</label>
                                <input type="text"
                                       class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="شماره پایانه"
                                       ref="terminal_id"
                                       id="terminal_id"
                                       v-model="terminalForm.terminal_id"/>
                                <jet-input-error :message="terminalForm.error('terminal_id')"
                                                 class="mt-2"/>
                            </div>
                            <div class="mt-2">
                                <label for="merchant_id"
                                       class="block text-sm font-medium text-gray-700">شماره پذیرنده</label>
                                <input type="text"
                                       class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="شماره پذیرنده"
                                       ref="merchant_id"
                                       id="merchant_id"
                                       v-model="terminalForm.merchant_id"/>
                                <jet-input-error :message="terminalForm.error('merchant_id')"
                                                 class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewTerminalModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="submitTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
                        تایید
                    </jet-button>
                    <jet-danger-button class="ml-2" @click.native="rejectSerial"
                                       v-on:click="rejectSerialModal=true">
                        عدم تایید سریال
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- عدم تایید سریال -->
            <!--            <jet-confirmation-modal :show="viewTerminalModal" @close="viewTerminalModal = false">-->
            <!--                <template #title>-->
            <!--                    ثبت شماره پایانه و پذیرنده-->
            <!--                </template>-->
            <!--                <template #content>-->
            <!--                    <div class="sm:flex sm:items-start">-->
            <!--                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">-->
            <!--                            <div>-->
            <!--                                <label for="terminal_id"-->
            <!--                                       class="block text-sm font-medium text-gray-700">شماره پایانه</label>-->
            <!--                                <input type="text"-->
            <!--                                       class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"-->
            <!--                                       placeholder="شماره پایانه"-->
            <!--                                       ref="terminal_id"-->
            <!--                                       id="terminal_id"-->
            <!--                                       v-model="terminalForm.terminal_id"/>-->
            <!--                                <jet-input-error :message="terminalForm.error('terminal_id')"-->
            <!--                                                 class="mt-2"/>-->
            <!--                            </div>-->
            <!--                            <div class="mt-2">-->
            <!--                                <label for="merchant_id"-->
            <!--                                       class="block text-sm font-medium text-gray-700">شماره پذیرنده</label>-->
            <!--                                <input type="text"-->
            <!--                                       class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"-->
            <!--                                       placeholder="شماره پذیرنده"-->
            <!--                                       ref="merchant_id"-->
            <!--                                       id="merchant_id"-->
            <!--                                       v-model="terminalForm.merchant_id"/>-->
            <!--                                <jet-input-error :message="terminalForm.error('merchant_id')"-->
            <!--                                                 class="mt-2"/>-->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </template>-->
            <!--                <template #footer>-->
            <!--                    <jet-secondary-button class="ml-2" @click.native="viewTerminalModal = false">-->
            <!--                        انصراف-->
            <!--                    </jet-secondary-button>-->
            <!--                    <jet-button class="ml-2 bg-green-600" @click.native="submitTerminal"-->
            <!--                                :class="{ 'opacity-25': terminalForm.processing }"-->
            <!--                                :disabled="terminalForm.processing">-->
            <!--                        تایید-->
            <!--                    </jet-button>-->
            <!--                    <jet-danger-button class="ml-2" @click.native="rejectSerial"-->
            <!--                                       v-on:click="rejectSerialModal=true">-->
            <!--                        عدم تایید سریال-->
            <!--                    </jet-danger-button>-->
            <!--                </template>-->
            <!--            </jet-confirmation-modal>-->
            <!-- درخواست فسخ -->
            <jet-confirmation-modal :show="viewCancelRequestModal" @close="viewCancelRequestModal = false">
                <template #title>
                    درخواست فسخ پرونده
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div>
                            <label for="cancel_reason"
                                   class="block text-sm font-medium text-gray-700">علت فسخ</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت فسخ"
                                ref="cancel_reason"
                                id="cancel_reason"
                                v-model="cancelRequestForm.cancel_reason"/>
                            <jet-input-error :message="cancelRequestForm.error('cancel_reason')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewCancelRequestModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="submitCancelRequest"
                                       :class="{ 'opacity-25': cancelRequestForm.processing }"
                                       :disabled="cancelRequestForm.processing">
                        ارسال
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید یا رد فسخ -->
            <jet-confirmation-modal :show="viewConfirmCancelModal" @close="viewConfirmCancelModal = false">
                <template #title>
                    تایید فسخ پرونده
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست فسخ</p>
                            <p class="text-md">{{cancelReason}}</p>
                        </div>
                        <div class="flex">
                            <jet-button
                                @click.native="confirmCancelForm.confirmCancelMessage=false"
                                class="bg-green-600 mx-auto hover:bg-green-500">
                                تایید
                            </jet-button>
                            <jet-danger-button
                                @click.native="confirmCancelForm.confirmCancelMessage=true"
                                class="mx-auto">
                                رد درخواست
                            </jet-danger-button>
                        </div>
                        <div v-if="confirmCancelForm.confirmCancelMessage">
                            <label for="message"
                                   class="block text-sm font-medium text-gray-700">علت رد درخواست</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت رد درخواست"
                                ref="message"
                                id="message"
                                v-model="confirmCancelForm.message"/>
                            <jet-input-error :message="confirmCancelForm.error('message')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewCancelModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitCancel"
                                :class="{ 'opacity-25': confirmCancelForm.processing }"
                                :disabled="confirmCancelForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- جابجایی سریال-->
            <jet-confirmation-modal :show="viewChangeSerialRequestModal" @close="viewChangeSerialRequestModal = false">
                <template #title>
                    جابجایی سریال
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div>
                            <label for="change_reason"
                                   class="block text-sm font-medium text-gray-700">علت درخواست جابجایی</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت درخواست جابجایی"
                                ref="change_reason"
                                id="change_reason"
                                v-model="changeSerialRequestForm.change_reason"/>
                            <jet-input-error :message="changeSerialRequestForm.error('change_reason')"
                                             class="mt-2"/>
                        </div>
                        <div>
                            <label for="change_reason"
                                   class="block text-sm font-medium text-gray-700">مدل مورد نظر</label>
                            <div class="grid grid-cols-6 gap-6 h-64 overflow-y-auto">
                                <div v-for="deviceType in deviceTypes" :key="deviceType.id"
                                     :class="oldDeviceTypeId==deviceType.id ? 'bg-indigo-200' : ''"
                                     class="col-span-3 sm:col-span-2 text-center border rounded border-grey-600 p-3">
                                    <svg
                                        class="mx-auto h-12 w-12 text-gray-400"
                                        stroke="currentColor"
                                        fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"/>
                                    </svg>
                                    <h1 class="text-md">{{deviceType.name}}</h1>
                                    <button type="submit"
                                            :disabled="oldDeviceTypeId==deviceType.id"
                                            :class="oldDeviceTypeId==deviceType.id ? 'text-gray-400 bg-gray-200' : 'text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'"
                                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md"
                                            v-on:click="selectNewDeviceType(deviceType.id)">
                                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                  d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M11,15V12H9V15H6V17H9V20H11V17H14V15H11Z"/>
                                        </svg>
                                        انتخاب
                                    </button>
                                </div>
                            </div>
                            <jet-input-error :message="changeSerialRequestForm.error('new_device_type_id')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewChangeSerialRequestModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitChangeSerialRequest"
                                :class="{ 'opacity-25': changeSerialRequestForm.processing }"
                                :disabled="changeSerialRequestForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- اختصاص سریال جدید-->
            <jet-confirmation-modal :show="viewSelectNewSerialModal" @close="viewSelectNewSerialModal = false">
                <template #title>
                    اختصاص سریال جدید
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست جابجایی</p>
                            <p class="text-md">{{changeReason}}</p>
                        </div>
                        <div class="my-3 p-2">
                            <p class="text-lg">مدل انتخاب شده:{{newDeviceType.name}}</p>
                        </div>
                        <div>
                            <input type="text"
                                   class=" inline-flex shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border"
                                   placeholder="جستجوی سریال"
                                   ref="search_serial2"
                                   id="search_serial2"
                                   v-model="search.serial"/>
                            <button type="button"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    v-on:click="searchDeviceSerials2">
                                جستجو
                            </button>

                            <div class="mt-2">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            شماره سریال
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وضعیت فیزیکی
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">

                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="device in search.results" :key="device.id">
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            {{device.serial}}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            {{device.physicalStatusText}}
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-900">
                                            <button type="button"
                                                    :class="selectNewSerialForm.new_device_id==device.id ? 'bg-red-600 text-white' : 'text-red-600'"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium border-red-600 hover:bg-red-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                                    v-on:click="selectNewSerialForm.new_device_id=device.id">
                                                انتخاب
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <jet-input-error :message="selectNewSerialForm.error('new_device_id')"
                                                 class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewSelectNewSerialModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitSelectNewSerial"
                                :class="{ 'opacity-25': selectNewSerialForm.processing }"
                                :disabled="selectNewSerialForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید یا رد جابجایی -->
            <jet-confirmation-modal :show="viewConfirmChangeSerialModal" @close="viewConfirmChangeSerialModal = false">
                <template #title>
                    تایید جابجایی پرونده
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست جابجایی</p>
                            <p class="text-md">{{changeReason}}</p>
                        </div>
                        <div class="my-3 p-2">
                            <p class="text-lg">دستگاه انتخاب شده جدید:</p>
                            <table class="w-full">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        مدل
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        شماره سریال
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت فیزیکی
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت انبار
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت psp
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="text-center">{{newDevice.device_type.name}}</td>
                                    <td class="text-center">{{newDevice.serial}}</td>
                                    <td class="text-center">{{newDevice.physicalStatusText}}</td>
                                    <td class="text-center">{{newDevice.transportStatusText}}</td>
                                    <td class="text-center">{{newDevice.pspStatusText}}</td>
                                    <td class="text-center">{{newDevice.statusText}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex">
                            <jet-button
                                @click.native="confirmChangeSerialForm.confirmChangeMessage=false"
                                :class="confirmChangeSerialForm.confirmChangeMessage==false ? 'bg-green-600' : ''"
                                class="border-green-600 bg-green-300 mx-auto hover:bg-green-500 hover:text-white">
                                تایید
                            </jet-button>
                            <jet-button
                                @click.native="confirmChangeSerialForm.confirmChangeMessage=true"
                                :class="confirmChangeSerialForm.confirmChangeMessage==true ? 'bg-red-600' : ''"
                                class="border-red-600 bg-red-300 mx-auto hover:bg-red-500 hover:text-white">
                                رد درخواست
                            </jet-button>
                        </div>
                        <div v-if="confirmChangeSerialForm.confirmChangeMessage">
                            <label for="change_message"
                                   class="block text-sm font-medium text-gray-700">علت رد درخواست</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت رد درخواست"
                                ref="change_message"
                                id="change_message"
                                v-model="confirmChangeSerialForm.change_message"/>
                            <jet-input-error :message="confirmChangeSerialForm.error('change_message')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewChangeSerialModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitConfirmChangeSerial"
                                :class="{ 'opacity-25': confirmChangeSerialForm.processing }"
                                :disabled="confirmChangeSerialForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- ارسال فایل اکسل -->
            <jet-confirmation-modal :show="viewUploadExcelModal" @close="viewUploadExcelModal = false">
                <template #title>
                    ورود اطلاعات
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-200 border-r-4 border-yellow-500">
                            <p class="text-md">جهت ورود اطلاعات بوسیله فایل اکسل از فایل نمونه استفاده کنید. دقت کنید که
                                در هر سطر از فایل اکسل اطلاعات یکی از پذیرندگان وارد شده باشد. چنانچه اطلاعات هر یک از
                                پذیرندگان دارای ایرادی باشد اطلاعات آن پذیرنده در سیستم ثبت نخواهد شد.</p>
                        </div>
                        <div>
                            <label for="profiles_file"
                                   class="block text-sm font-medium text-gray-700">فایل اکسل حاوی اطلاعات
                                پذیرندگان</label>
                            <input type="file"
                                   class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   ref="profiles_file"
                                   id="profiles_file"
                                   v-on:change="onProfilesExcelFileChange"/>
                            <jet-input-error :message="uploadExcelForm.error('file')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewUploadExcelModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitUploadExcel"
                                :class="{ 'opacity-25': uploadExcelForm.processing }"
                                :disabled="uploadExcelForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button'
    import {Inertia} from "@inertiajs/inertia";
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetInputError from '@/Jetstream/InputError';
    import JetInput from '@/Jetstream/Input';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

    export default {
        name: "ProfilesList",
        components: {
            Dashboard,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            Pagination,
            datePicker: VuePersianDatetimePicker,
        },
        props: {
            searchQuery: String,

            profiles: Object,

            statuses: Array,
            statusId: String,

            agents: Array,
            agentId: String,

            marketers: Array,
            marketerId: String,

            fromDate: String,
            toDate: String,

            paginatedLinks: Array
        },
        data() {
            return {
                status_id: null,
                agent_id: null,
                marketer_id: null,
                from_date: null,
                to_date: null,
                query: null,

                profileForm: this.$inertia.form({
                    '_method': 'PUT',
                    status: '',
                }, {
                    bag: 'profileForm',
                    resetOnSuccess: true
                }),

                viewSearchModal: false,
                search: {
                    serial: '',
                    results: []
                },
                devices: [],
                selectedSerial: '',
                confirmSerial: false,
                profileId: '',
                serialForm: this.$inertia.form({
                    '_method': 'PUT',
                    device_id: '',
                }, {
                    bag: 'serialForm',
                    resetOnSuccess: true
                }),
                viewTerminalModal: false,
                terminalForm: this.$inertia.form({
                    '_method': 'PUT',
                    terminal_id: '',
                    merchant_id: '',
                }, {
                    bag: 'terminalForm',
                    resetOnSuccess: true
                }),
                rejectSerialModal: false,


                viewCancelRequestModal: false,
                cancelRequestForm: this.$inertia.form({
                    '_method': 'PUT',
                    cancel_reason: '',
                }, {
                    bag: 'cancelRequestForm',
                    resetOnSuccess: true
                }),

                viewConfirmCancelModal: false,
                cancelReason: '',
                confirmCancelForm: this.$inertia.form({
                    '_method': 'PUT',
                    message: '',
                    confirmCancelMessage: false,
                }, {
                    bag: 'confirmCancelForm',
                    resetOnSuccess: true
                }),

                viewUploadExcelModal: false,
                uploadExcelForm: this.$inertia.form({
                    '_method': 'POST',
                    file: '',
                }, {
                    bag: 'uploadExcelForm',
                    resetOnSuccess: true
                }),

                viewChangeSerialRequestModal: false,
                deviceTypes: [],
                oldDeviceTypeId: '',
                newDeviceTypeId: '',
                changeSerialRequestForm: this.$inertia.form({
                    '_method': 'PUT',
                    change_reason: '',
                    new_device_type_id: '',
                }, {
                    bag: 'changeSerialRequestForm',
                    resetOnSuccess: true
                }),

                viewSelectNewSerialModal: false,
                newDeviceType: {
                    name: '',
                },
                selectNewSerialForm: this.$inertia.form({
                    '_method': 'PUT',
                    new_device_id: '',
                }, {
                    bag: 'selectNewSerialForm',
                    resetOnSuccess: true
                }),

                viewConfirmChangeSerialModal: false,
                changeReason: '',
                newDevice: {
                    device_type: {},
                },
                confirmChangeSerialForm: this.$inertia.form({
                    '_method': 'PUT',
                    change_message: '',
                    confirmChangeMessage: false,
                }, {
                    bag: 'confirmChangeSerialForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery;
            this.status_id = this.statusId;
            this.agent_id = this.agentId;
            this.marketer_id = this.marketerId;
            this.from_date = this.fromDate;
            this.to_date = this.toDate;
        },
        methods: {
            statusColors(status) {
                switch (status) {
                    case 0:
                        return 'bg-yellow-100 text-yellow-800';
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 2:
                        return 'bg-yellow-100 text-yellow-800';
                    case 3:
                        return 'bg-green-100 text-green-800';
                    case 4:
                        return 'bg-green-100 text-green-800';
                    case 5:
                        return 'bg-green-100 text-green-800';
                    case 6:
                        return 'bg-yellow-100 text-yellow-800';
                    case 7:
                        return 'bg-green-100 text-green-800';
                    case 8:
                        return 'bg-green-100 text-green-800';
                    case 9:
                        return 'bg-gray-100 text-gray-800';
                    case 10:
                        return 'bg-red-100 text-red-800';
                    case 11:
                        return 'bg-red-100 text-red-800';
                    case 12:
                        return 'bg-yellow-100 text-yellow-800';
                    case 13:
                        return 'bg-red-100 text-red-800';
                    case 14:
                        return 'bg-yellow-100 text-yellow-800';
                    case 15:
                        return 'bg-yellow-100 text-yellow-800';
                    case 16:
                        return 'bg-red-100 text-red-800';
                }
            },
            updateProfileStatus(id, status) {
                this.profileForm.status = status;
                this.profileForm.post(route('dashboard.profiles.update', {profileId: id}))
                    .then(response => {

                    })
            },
            viewDevicesModal(profileId) {
                this.viewSearchModal = true;
                this.profileId = profileId;
                axios.get('dashboard/devices/' + profileId)
                    .then(response => {
                        this.devices = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            searchDeviceSerials() {
                let serial = this.$refs.search_serial.value;
                var condition = new RegExp(serial);
                let selected = this.devices.filter(function (device) {
                    return condition.test(device.serial);
                });
                this.search.results = selected;
            },
            selectDevice(device) {
                this.serialForm.device_id = device.id;
                this.selectedSerial = device.serial;
                this.confirmSerial = true;
            },
            submitSerial() {
                this.serialForm.post(route('dashboard.profiles.update.serial', {profileId: this.profileId}))
                    .then(response => {
                        if (!this.serialForm.hasErrors()) {
                            this.confirmSerial = false;
                            this.viewSearchModal = false;
                            this.$refs.search_serial.value = '';
                            this.search.serial = '';
                            this.profileId = '';
                            this.search.results = [];
                        }
                    })
            },
            selectTerminal(profileId) {
                this.profileId = profileId;
                this.viewTerminalModal = true;
            },
            submitTerminal() {
                this.terminalForm.post(route('dashboard.profiles.update.terminal', {profileId: this.profileId}))
                    .then(response => {
                        if (!this.terminalForm.hasErrors()) {
                            this.viewTerminalModal = false;
                            this.profileId = '';
                        }
                    })
            },
            cancelRequest(profileId) {
                this.profileId = profileId;
                this.viewCancelRequestModal = true;
            },
            submitCancelRequest() {
                this.cancelRequestForm.post(route('dashboard.profiles.update.cancelRequest', {profileId: this.profileId}))
                    .then(response => {
                        if (!this.cancelRequestForm.hasErrors()) {
                            this.viewCancelRequestModal = false;
                            this.profileId = '';
                        }
                    })
            },
            confirmCancel(profileId, cancelReason) {
                this.profileId = profileId;
                this.cancelReason = cancelReason;
                this.viewConfirmCancelModal = true;
            },
            submitCancel() {
                this.confirmCancelForm.post(route('dashboard.profiles.update.cancelConfirm', {profileId: this.profileId}))
                    .then(response => {
                        if (!this.confirmCancelForm.hasErrors()) {
                            this.viewConfirmCancelModal = false;
                            this.profileId = '';
                        }
                    })
            },
            changeSerialRequest(profileId, oldDeviceTypeId) {
                this.viewChangeSerialRequestModal = true;
                this.profileId = profileId;
                this.oldDeviceTypeId = oldDeviceTypeId;
                this.changeSerialRequestForm.new_device_type_id = oldDeviceTypeId;
                axios.get('dashboard/deviceTypes/' + profileId)
                    .then(response => {
                        this.deviceTypes = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            selectNewDeviceType(newDeviceTypeId) {
                this.oldDeviceTypeId = newDeviceTypeId;
                this.changeSerialRequestForm.new_device_type_id = newDeviceTypeId;
            },
            submitChangeSerialRequest() {
                this.changeSerialRequestForm.post(route('dashboard.profiles.update.changeRequest', {profileId: this.profileId}))
                    .then(response => {
                        if (!this.changeSerialRequestForm.hasErrors()) {
                            this.viewChangeSerialRequestModal = false;
                            this.profileId = '';
                            this.oldDeviceTypeId = '';
                        }
                    })
            },
            selectNewSerial(profileId, changeReason) {
                axios.get('dashboard/profiles/' + profileId + '/newDeviceType')
                    .then(response => {
                        this.newDeviceType = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
                axios.get('dashboard/newDevices/' + profileId)
                    .then(response => {
                        this.devices = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.profileId = profileId;
                this.changeReason = changeReason;
                this.viewSelectNewSerialModal = true;
            },
            submitSelectNewSerial(){
                this.selectNewSerialForm.post(route('dashboard.profiles.update.newSerial', {profileId: this.profileId})).then(response => {
                    if (!this.selectNewSerialForm.hasErrors()) {
                        this.viewSelectNewSerialModal = false;
                        this.profileId = '';
                    }
                })
            },
            searchDeviceSerials2() {
                let serial = this.$refs.search_serial2.value;
                var condition = new RegExp(serial);
                let selected = this.devices.filter(function (device) {
                    return condition.test(device.serial);
                });
                this.search.results = selected;
            },
            confirmChangeSerial(profileId, changeReason) {
                axios.get('dashboard/profiles/' + profileId + '/newDevice')
                    .then(response => {
                        this.newDevice = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.profileId = profileId;
                this.changeReason = changeReason;
                this.viewConfirmChangeSerialModal = true;
            },
            submitConfirmChangeSerial() {
                this.confirmChangeSerialForm.post(route('dashboard.profiles.update.changeConfirm', {profileId: this.profileId})).then(response => {
                    if (!this.confirmChangeSerialForm.hasErrors()) {
                        this.viewConfirmChangeSerialModal = false;
                        this.profileId = '';
                    }
                })
            },
            uploadExcel() {
                this.viewUploadExcelModal = true;
            },
            onProfilesExcelFileChange(e) {
                const file = e.target.files[0];
                this.uploadExcelForm.file = e.target.files[0];
            },
            submitUploadExcel() {
                this.uploadExcelForm.post(route('dashboard.profiles.uploadExcel'))
                    .then(response => {
                        if (!this.uploadExcelForm.hasErrors()) {
                            this.viewUploadExcelModal = false;
                        }
                    });
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.profiles.list'), {
                    method: 'get',
                    data: {
                        query: this.query,
                        statusId: this.status_id,
                        agentId: this.agent_id,
                        marketerId: this.marketer_id,
                        fromDate: this.from_date,
                        toDate: this.to_date,
                    },
                })
            },
            submitFromDate(e) {
                this.submitSearchForm();
            },
            submitToDate(e) {
                this.submitSearchForm();
            }
        }
    }
</script>

<style scoped>

</style>
