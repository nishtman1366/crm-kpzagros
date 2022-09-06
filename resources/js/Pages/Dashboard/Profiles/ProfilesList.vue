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
                                    <jet-input type="text"
                                               @keyup.enter.native="submitSearchForm"
                                               v-model="query"
                                               placeholder="جستجو در کدملی یا نام مشتری"
                                               class="w-1/2"/>
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-500 hover:bg-blue-400">
                                        جستجو
                                    </jet-button>
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
                                    <jet-button
                                        v-show="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                        @click.native="uploadExcel"
                                        class="my-5 mx-1 bg-green-600 hover:bg-green-500 sm:float-left">
                                        <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                  d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                        </svg>
                                        ورود اطلاعات
                                    </jet-button>
                                    <jet-button v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                                @click.native="openDownloadExcelModal(false)"
                                                class="my-5 mx-1 bg-yellow-600 hover:bg-yellow-500 sm:float-left">
                                        <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                  d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                        </svg>
                                        دریافت لیست
                                    </jet-button>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <select id="psp_id"
                                            name="psp_id"
                                            ref="psp_id"
                                            v-model="psp_id"
                                            autocomplete="psp_id"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس سرویس دهنده"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">سرویس دهنده</option>
                                        <option v-for="psp in psps" :key="psp.id"
                                                :value="psp.id">{{ psp.name }}
                                        </option>
                                    </select>
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
                                                :value="statusItem.id">{{ statusItem.name }}
                                        </option>
                                    </select>
                                    <select
                                        v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'"
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
                                                :value="agent.id">{{ agent.name }}
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
                                                :value="marketer.id">{{ marketer.name }}
                                        </option>
                                    </select>
                                    <select id="profile_type"
                                            name="profile_type"
                                            ref="profile_type"
                                            v-model="profile_type"
                                            autocomplete="profile_type"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس نوع پرونده"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">نوع پرونده</option>
                                        <option value="REGISTER">پرونده جدید</option>
                                        <option value="TRANSFER">انتقال مالکیت</option>
                                        <option value="EDIT">تغییر مشخصات</option>
                                    </select>
                                    <select id="license_status"
                                            name="license_status"
                                            ref="license_status"
                                            v-model="license_status"
                                            autocomplete="license_status"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس وضعیت مدارک"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">وضعیت مدارک</option>
                                        <option :value="0">تایید نشده</option>
                                        <option :value="1">تایید موقت</option>
                                        <option :value="2">تایید نهایی</option>
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
                                    <span class="mx-1 text-blue-500 hover:text-blue-400 cursor-pointer"
                                          @click="clearDate('from')">حذف</span>
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
                                    <span class="mx-1 text-blue-500 hover:text-blue-400 cursor-pointer"
                                          @click="clearDate('to')">حذف</span>

                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col" colspan="2"
                                        class="list-table-header-cell">
                                        نام پذیرنده
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        نوع پرونده
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        نوع خدمات
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        بازاریاب
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        زمان ثبت
                                    </th>
                                    <th scope="col"
                                        class="list-table-header-cell">
                                        آخرین تغییرات
                                    </th>
                                    <th scope="col" class="py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <template v-for="profile in profiles.data">
                                    <tr :key="profile.id">
                                        <td class="list-table-body-cell">
                                            <svg v-if="profile.terminals && profile.terminals.length > 0"
                                                 title="ترمینال‌ها" :data-container="`profile-${profile.id}`"
                                                 v-b-tooltip.hover style="width:24px;height:24px" viewBox="0 0 24 24"
                                                 class="terminals-box text-indigo-500 hover:text-indigo-400 cursor-pointer">
                                                <path fill="currentColor"
                                                      d="M17 14V17H14V19H17V22H19V19H22V17H19V14M20 11V12.3C19.4 12.1 18.7 12 18 12C16.8 12 15.6 12.4 14.7 13H7V11H20M12.1 17H7V15H12.8C12.5 15.6 12.2 16.3 12.1 17M7 7H20V9H7V7M5 19H7V21H3V3H7V5H5V19Z"/>
                                            </svg>
                                        </td>
                                        <td class="list-table-body-cell">
                                            {{ profile.customer.fullName }}
                                            <p
                                                class="text-indigo-600">({{ profile.customer.national_code }})</p>
                                        </td>
                                        <td class="list-table-body-cell">
                                            {{ profile.typeText }}
                                        </td>
                                        <td class="list-table-body-cell">
                                            دستگاه کارتخوان
                                            <p
                                                class="sub-text">({{ profile.psp ? profile.psp.name : 'نامشخص' }})</p>
                                        </td>
                                        <td class="list-table-body-cell">
                                            <p>{{ profile.user.name }}</p>
                                            <p class="sub-text">
                                                {{ profile.user.parent ? 'نماینده: ' + profile.user.parent.name : '' }}
                                            </p>
                                        </td>
                                        <td class="list-table-body-cell">
                                        <span
                                            :class="statusColors(profile.status)"
                                            class="badge">
                                          {{ profile.statusText }}
                                        </span>
                                        </td>
                                        <td class="list-table-body-cell">{{ profile.jCreatedAt }}</td>
                                        <td class="list-table-body-cell">{{ profile.jUpdatedAt }}</td>
                                        <td class="list-table-body-cell flex flex-nowrap items-center justify-center">
                                            <InertiaLink
                                                :href="route('dashboard.profiles.view',{profile: profile.id})"
                                                class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                                <button title="مشاهده پرونده"
                                                        v-b-tooltip.hover>
                                                    <i id="view-device-button" class="material-icons">folder_shared</i>
                                                </button>
                                            </InertiaLink>
                                            <button
                                                v-if="profile.status==1 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='AGENT')"
                                                v-on:click="updateProfileStatus(profile.id,2)"
                                                class="text-green-400 hover:text-green-500"
                                                title="دردست بررسی"
                                                v-b-tooltip.hover>
                                                <i class="material-icons">check_circle</i>
                                            </button>
                                            <button
                                                v-if="profile.status==3 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                v-on:click="updateProfileStatus(profile.id,4)"
                                                class="text-green-400 hover:text-green-500"
                                                title="ثبت در psp"
                                                v-b-tooltip.hover>
                                                <i class="material-icons">assignment_turned_in</i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-for="terminal in profile.terminals" :key="terminal.id"
                                        :class="`profile-${profile.id}`"
                                        class="hidden bg-gray-100">
                                        <td class="list-table-body-cell" colspan="2">نوع ترمینال: <span
                                            class="font-bold">{{ terminal.typeText }}</span></td>
                                        <td class="list-table-body-cell">سریال دستگاه: <span
                                            class="font-bold">{{ terminal.device && terminal.device.serial }}</span>
                                        </td>
                                        <td class="list-table-body-cell" colspan="2">مدل دستگاه: <span
                                            class="font-bold">{{
                                                terminal.device_type && terminal.device_type.name
                                            }}</span>
                                        </td>
                                        <td class="list-table-body-cell" colspan="2">نوع ارتباط: <span
                                            class="font-bold">{{
                                                terminal.device_connection_type && terminal.device_connection_type.name
                                            }}</span>
                                        </td>
                                        <td class="list-table-body-cell">وضعیت: <span
                                            class="font-bold">{{ terminal.statusText }}</span></td>
                                        <td class="list-table-body-cell"><a target="_blank"
                                                                            :href="route('dashboard.profiles.terminal.delivery.form',{profile: profile.id,terminal:terminal.id})"
                                                                            class="tooltip-box text-purple-600 hover:text-purple-900">
                                            <button title="گواهی تحویل"
                                                    v-b-tooltip.hover>
                                                <i id="view-license-button" class="material-icons">file_download</i>
                                            </button>
                                        </a></td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                            <pagination
                                :urlsArray="paginatedLinks"
                                :totalRows="profiles.total"
                                :previousPageUrl="profiles.prev_page_url"
                                :nextPageUrl="profiles.next_page_url"/>
                        </div>
                    </div>
                </div>
            </div>

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
                        <div>
                            <label for="profiles_psp_id"
                                   class="block text-sm font-medium text-gray-700">سرویس‌دهنده</label>
                            <select id="profiles_psp_id" name="psp_id"
                                    v-model="uploadExcelForm.psp_id"
                                    class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option :value="null">سرویس دهنده</option>
                                <option v-for="psp in psps" :key="psp.id"
                                        :value="psp.id">{{ psp.name }}
                                </option>
                            </select>
                            <jet-input-error :message="uploadExcelForm.error('psp_id')"
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
            <!-- دانلود فایل اکسل -->
            <jet-confirmation-modal :show="viewDownloadExcelModal" @close="closeDownloadModal">
                <template #title>
                    دریافت اطلاعات به صورت فایل اکسل
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-200 border-r-4 border-yellow-500">
                            <p class="text-md">با توجه به زمانبر بودن فرایند تبدیل اطلاعات، این فرایند در پس زمینه اجرا
                                خواهد شد</p>
                        </div>
                        <div v-if="downloadExcelLoading" class="text-center">در حال بارگذاری اطلاعات...</div>
                        <template v-else>
                            <div v-if="downloadExcelResponse" class="text-center my-4">
                                <div v-if="downloadExcelResponse.status==='processing'">
                                    <div class="text-yellow-500">{{ downloadExcelResponse.message }}</div>
                                    <div>میزان پیشرفت: {{ downloadExcelResponse.complete }}%</div>
                                    <div>
                                        <div class="bg-gray-300 w-full h-5 rounded shadow">
                                            <div class="h-5 rounded bg-red-400"
                                                 :style="`width: ${downloadExcelResponse.complete}%;`"></div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="downloadExcelResponse.status==='done'">
                                    <div class="my-3 p-2 bg-green-200 border-r-4 border-green-500">
                                        {{ downloadExcelResponse.message }}
                                    </div>
                                    <div>
                                        <a :href="downloadExcelResponse.url" target="_blank">
                                            <jet-button>دریافت فایل</jet-button>
                                        </a>
                                    </div>
                                    <div class="my-3 p-2 bg-red-200 border-r-4 border-red-500">فایل گزارش در
                                        <span class="bg-white py-1 px-2 rounded-full text-red-500"
                                              style="display:inline-block;direction: ltr">
                                            {{ downloadExcelResponse.expiration }}
                                        </span>
                                        به صورت خودکار حذف خواهد شد.
                                    </div>
                                </div>
                                <div v-else-if="downloadExcelResponse.status==='failed'">
                                    <div class="my-3 p-2 bg-red-200 border-r-4 border-red-500">
                                        {{ downloadExcelResponse.message }}
                                    </div>
                                </div>
                                <div v-else class="text-right my-3 p-2 border-r-4 border-gray-500">
                                    <p class="text-md">فرایندی در حال اجرا نمی باشد.</p>
                                    <div v-if="downloadExcelResponse.message"
                                         class="my-3 p-2 bg-green-200 border-r-4 border-green-500">
                                        {{ downloadExcelResponse.message }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeDownloadModal">
                        بستن
                    </jet-secondary-button>
                    <jet-danger-button v-if="downloadExcelResponse && downloadExcelResponse.status==='processing'"
                                       class="ml-2 hidden" @click.native="cancelExcelProcess">
                        توقف فرایند و حذف فایل‌ها
                    </jet-danger-button>
                    <jet-danger-button
                        v-if="downloadExcelResponse && (downloadExcelResponse.status==='done' || downloadExcelResponse.status==='failed')"
                        class="ml-2" @click.native="cancelExcelProcess">
                        حذف فایل‌ها
                    </jet-danger-button>
                    <InertiaLink
                        v-if="!downloadExcelLoading && (!downloadExcelResponse || (downloadExcelResponse && downloadExcelResponse.status==='failed') || (downloadExcelResponse && downloadExcelResponse.status==='NotFound') || (downloadExcelResponse && downloadExcelResponse.status==='done'))"
                        :href="route('dashboard.profiles.downloadExcel',{
                                                query: query,
                                                pspId: psp_id,
                                                statusId: status_id,
                                                agentId: agent_id,
                                                marketerId: marketer_id,
                                                profileType: profile_type,
                                                licenseStatus: license_status,
                                                fromDate: from_date,
                                                toDate: toDate,
                                            })">
                        <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500">
                            تبدیل اطلاعات
                        </jet-button>
                    </InertiaLink>
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

        psps: Array,
        pspId: String,

        statuses: Array,
        statusId: String,

        agents: Array,
        agentId: String,

        marketers: Array,
        marketerId: String,

        profileType: String,

        licenseStatus: String,

        fromDate: String,
        toDate: String,

        paginatedLinks: Array
    },
    data() {
        return {
            status_id: null,
            psp_id: null,
            agent_id: null,
            marketer_id: null,
            profile_type: null,
            from_date: null,
            to_date: null,
            query: null,
            selectedProfile: null,

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
            selectedDevice: '',
            confirmSerial: false,
            profileId: '',

            viewUploadExcelModal: false,
            uploadExcelForm: this.$inertia.form({
                '_method': 'POST',
                file: '',
                psp_id: '',
            }, {
                bag: 'uploadExcelForm',
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

            downloadExcelLoading: false,
            viewDownloadExcelModal: false,
            downloadExcelResponse: null,

            exportStatusTimeout: null,
        }
    },
    mounted() {
        this.query = this.searchQuery;
        this.psp_id = this.pspId;
        this.status_id = this.statusId;
        this.agent_id = this.agentId;
        this.marketer_id = this.marketerId;
        this.profile_type = this.profileType;
        this.license_status = this.licenseStatus;
        this.from_date = this.fromDate;
        this.to_date = this.toDate;

        let terminalsBoxes = document.querySelectorAll('.terminals-box');
        terminalsBoxes.forEach((box) => {
            box.addEventListener('click', () => {
                let container = box.dataset.container;
                let elements = document.querySelectorAll(`.${container}`);
                elements.forEach((element) => {
                    element.classList.remove('hidden');
                })
            })
        });
    },
    methods: {
        statusColors(status) {
            switch (status) {
                case 0:
                    return 'badge-yellow';
                case 1:
                    return 'badge-green';
                case 2:
                    return 'badge-yellow';
                case 3:
                    return 'badge-green';
                case 4:
                    return 'badge-green';
                case 5:
                    return 'badge-green';
                case 6:
                    return 'badge-yellow';
                case 7:
                    return 'badge-green';
                case 8:
                    return 'badge-green';
                case 9:
                    return 'badge-gray';
                case 10:
                    return 'badge-red';
                case 11:
                    return 'badge-red';
                case 12:
                    return 'badge-yellow';
                case 13:
                    return 'badge-red';
                case 14:
                    return 'badge-yellow';
                case 15:
                    return 'badge-yellow';
                case 16:
                    return 'badge-red';
            }
        },
        updateProfileStatus(id, status) {
            this.profileForm.status = status;
            this.profileForm.post(route('dashboard.profiles.update', {profile: id}))
                .then(response => {

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
                    pspId: this.psp_id,
                    statusId: this.status_id,
                    agentId: this.agent_id,
                    marketerId: this.marketer_id,
                    profileType: this.profile_type,
                    licenseStatus: this.license_status,
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
        },
        clearDate(date) {
            if (date === 'from') {
                this.from_date = null;
            } else if (date === 'to') {
                this.to_date = null;
            }
            Inertia.visit(route('dashboard.profiles.list'), {
                method: 'get',
                data: {
                    query: this.query,
                    pspId: this.psp_id,
                    statusId: this.status_id,
                    agentId: this.agent_id,
                    marketerId: this.marketer_id,
                    profileType: this.profile_type,
                    licenseStatus: this.license_status,
                    fromDate: this.from_date,
                    toDate: this.to_date,
                },
            })
        },
        openDownloadExcelModal(refresh) {
            if (refresh === false) {
                this.downloadExcelLoading = true;
            }
            this.viewDownloadExcelModal = true;
            axios.get('dashboard/profiles/excel/status')
                .then(response => {
                    this.downloadExcelResponse = response.data;
                    if (response.data.status === 'processing') {
                        this.exportStatusTimeout = setTimeout(() => this.openDownloadExcelModal(true), 3500);
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.downloadExcelLoading = false;
                })
        },
        cancelExcelProcess() {
            this.downloadExcelLoading = true;
            axios.get('dashboard/profiles/excel/cancel')
                .then(response => {
                    this.downloadExcelResponse = response.data;
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.downloadExcelLoading = false;
                })
        },
        closeDownloadModal() {
            clearTimeout(this.exportStatusTimeout);
            this.viewDownloadExcelModal = false;
            this.downloadExcelResponse = null;
        }
    }
}
</script>

<style scoped>

</style>
