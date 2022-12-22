<template>
    <Dashboard>
        <template #breadcrumb> / درخواست های عودت دستگاه / مشاهده درخواست</template>
        <template #dashboardContent>
            <div>
                <div class="p-3 bg-gray-300">
                    <jet-form-section @submitted="submitEditReturnForm">
                        <template #title>
                            ثبت درخواست عودت دستگاه
                        </template>

                        <template #description>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات درخواست عودت دستگاه را ثبت نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">مدل و شماره سریال دستگاه:</span>
                                مدل دستگاه مورد نظر را از لیست مدل ها انتخاب نمایید و شماره سریال دستگاه را وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">اطلاعات پذیرنده:</span>
                                در این بخش اطلاعات مربوط به مالک دستگاه را وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">مبلغ و فااکتور خرید دستگاه:</span>
                                در این بخش مبلغی که بابت فروش دستگاه دریافت شده است و همچنین فایل تصویر فاکتور فروش را
                                وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">اقلام همراه دستگاه:</span>
                                چنانچه اقلامی همراه دستگاه عودت داده شده وجود دارد در این بخش انتخاب نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">توضیحات تکمیلی:</span>
                                چنانچه موردی جهت توضیح در خصوص علت عودت دستگاه وجود دارد میتوانید در این بخش وارد
                                نمایید.
                            </p>
                            <div
                                :class="event.status==6 ? ' border-red-600 bg-red-100' : ' border-green-600 bg-green-100'"
                                class="my-1 p-3 border-r-4"
                                v-for="event in device.events" :key="event.id">
                                <p class="text-sm text-gray-400 mt-2">{{ event.jDate | persianDigit }}</p>
                                <p class="font-bold ml-1">{{ event.title }}
                                    <span class="text-xs text-gray-400">{{ event.user && event.user.name }}</span>
                                </p>
                                <p v-if="event.description" class="text-justify" v-html="event.description"></p>
                            </div>
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="device_type_id" value="مدل دستگاه"/>
                                <select id="device_type_id" name="device_type_id" ref="device_type_id"
                                        :disabled="disableInputs"
                                        v-model="editReturnForm.device_type_id"
                                        autocomplete="device_type_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="type in deviceTypes" :key="type.id"
                                            :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="editReturnForm.error('device_type_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="serial" value="سریال دستگاه"/>
                                <jet-input id="serial"
                                           :disabled="disableInputs"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="editReturnForm.serial"
                                           ref="serial"
                                           autocomplete="serial"/>
                                <jet-input-error :message="editReturnForm.error('serial')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="name" value="نام پذیرنده"/>
                                <jet-input id="name"
                                           type="text"
                                           :disabled="disableInputs"
                                           class="mt-1 block w-full"
                                           v-model="editReturnForm.name"
                                           ref="name"
                                           autocomplete="name"/>
                                <jet-input-error :message="editReturnForm.error('name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="national_code" value="کد ملی پذیرنده"/>
                                <jet-input id="national_code"
                                           type="text"
                                           :disabled="disableInputs"
                                           class="mt-1 block w-full"
                                           v-model="editReturnForm.national_code"
                                           ref="national_code"
                                           autocomplete="national_code"/>
                                <jet-input-error :message="editReturnForm.error('national_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="mobile" value="تلفن همراه پذیرنده"/>
                                <jet-input id="mobile"
                                           type="text"
                                           :disabled="disableInputs"
                                           class="mt-1 block w-full"
                                           v-model="editReturnForm.mobile"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="editReturnForm.error('mobile')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-3">
                                <jet-label for="amount" value="مبلغ خرید"/>
                                <jet-input id="amount"
                                           type="text"
                                           :disabled="disableInputs"
                                           class="mt-1 block w-full"
                                           v-model="editReturnForm.amount"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="editReturnForm.error('amount')" class="mt-2"/>
                            </div>
                            <div class="col-span-3">
                                <div
                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg v-if="imageFiles.faktorFilePreview===''"
                                             class="mx-auto h-12 w-12 text-gray-400"
                                             stroke="currentColor"
                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        <a v-else :href="imageFiles.faktorFilePreview" target="_blank">
                                            <img :src="imageFiles.faktorFilePreview">
                                        </a>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="faktor_file"
                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>انتخاب فایل</span>
                                                <input id="faktor_file"
                                                       name="faktor_file"
                                                       :disabled="disableInputs"
                                                       type="file"
                                                       @change="onFaktorFileChange"
                                                       class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            تصویر فاکتور خرید
                                        </p>
                                        <jet-input-error
                                            :message="editReturnForm.error('file')"
                                            class="mt-2"/>
                                        <jet-input-error
                                            :message="fileUploadErrors.faktor"
                                            class="mt-2"/>
                                    </div>
                                </div>
                                <jet-input-error :message="editReturnForm.error('accessories')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-label for="accessories" value="اقلام همراه"/>
                                <div class="flex">
                                    <jet-label v-for="item in accessories" :key="item.id" class="flex items-center"
                                               :for="'accessories_'+item.id">
                                        <input class="form-checkbox" type="checkbox" :name="'accessories_'+item.id"
                                               :id="'accessories_'+item.id"
                                               :disabled="disableInputs"
                                               v-model="editReturnForm.accessories"
                                               :value="item.id"/>
                                        <span class="mr- ml-3">{{ item.name }}</span>
                                    </jet-label>
                                </div>
                                <jet-input-error :message="editReturnForm.error('accessories')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="description" value="توضیحات تکمیلی"/>
                                <textarea id="description"
                                          :disabled="disableInputs"
                                          type="description"
                                          class="form-input mt-1 block w-full"
                                          v-model="editReturnForm.description"
                                          ref="description"
                                          autocomplete="description" rows="3" cols="60"></textarea>
                                <jet-input-error :message="editReturnForm.error('description')" class="mt-2"/>
                            </div>
                            <template v-if="device.status >= 3">
                                <div class="col-span-6">
                                    <jet-section-border></jet-section-border>
                                </div>
                                <div class="col-span-6 text-lg">
                                    پرداخت ها
                                </div>
                                <div class="col-span-6">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                        <tr>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                نام پرداخت کننده
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                شیوه پرداخت
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                تاریخ
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                کد پیگیری بانک
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                کد پیگیری سیستم
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                وضعیت
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="payment in device.payments" :key="payment.id">
                                            <td class="py-4 text-center text-gray-900">
                                                {{ payment.user && payment.user.name }}
                                            </td>
                                            <td class="py-4 text-center text-gray-900">
                                                {{ payment.type && payment.type.name }}
                                            </td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.jDate }}</td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.ref_code }}</td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.tracking_code }}</td>
                                            <td class="py-4 text-center text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="paymentStatusColors(payment.status)">
                                                    {{ payment.statusText }}
                                            </span>
                                                <InertiaLink
                                                    v-if="payment.status==1 && ($page.user.level==='ADMIN'  || $page.user.level==='SUPERUSER')"
                                                    :href="route('dashboard.payments.confirm',{paymentId: payment.id})"
                                                    class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                                    <button title="تایید پرداخت"
                                                            v-b-tooltip.hover>
                                                        <i id="test" class="material-icons">check</i>
                                                    </button>
                                                </InertiaLink>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </template>
                        <template #actions v-if="$page.user.level!=='ACCOUNTING'">
                            <jet-action-message :on="editReturnForm.recentlySuccessful" class="mr-3">
                                ذخیره شد.
                            </jet-action-message>

                            <jet-button v-if="!disableInputs" :class="{ 'opacity-25': editReturnForm.processing }"
                                        class="mx-1"
                                        :disabled="editReturnForm.processing">
                                ذخیره تغییرات
                            </jet-button>
                            <jet-button v-if="!disableInputs && device.status==1"
                                        class="bg-green-500 hover:bg-green-400 mx-1"
                                        :class="{ 'opacity-25': updateReturnStatusForm.processing }"
                                        @click.native="updateStatus(2)"
                                        type="button"
                                        :disabled="updateReturnStatusForm.processing">
                                دریافت توسط واحد فنی
                            </jet-button>
                            <jet-button v-if="!disableInputs && device.status==2"
                                        class="bg-green-500 hover:bg-green-400 mx-1"
                                        :class="{ 'opacity-25': updateReturnStatusForm.processing }"
                                        @click.native="updateStatus(3)"
                                        type="button"
                                        :disabled="updateReturnStatusForm.processing">
                                در صف امور مالی
                            </jet-button>
                            <jet-button v-if="!disableInputs && device.status==3"
                                        class="bg-yellow-500 hover:bg-yellow-400 mx-1"
                                        @click.native="viewPaymentModal=true"
                                        type="button">
                                عودت هزینه
                            </jet-button>
                            <jet-button v-if="!disableInputs && device.status==4"
                                        class="bg-green-500 hover:bg-green-400 mx-1"
                                        :class="{ 'opacity-25': updateReturnStatusForm.processing }"
                                        @click.native="updateStatus(5)"
                                        type="button"
                                        :disabled="updateReturnStatusForm.processing">
                                عودت شده
                            </jet-button>
                            <jet-button v-if="!disableInputs && (device.status==1 || device.status==2)"
                                        type="button"
                                        @click.native="rejectReturn=true"
                                        class="bg-red-500 hover:bg-red-400 mx-1">
                                رد درخواست
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>

            <jet-confirmation-modal :show="rejectReturn" @close="rejectReturn = false">
                <template #title>
                    دلیل رد درخواست
                </template>
                <template #content>
                    <div>
                        <jet-label value="لطفا علت رد درخواست را وارد نمایید." for="message"/>
                        <textarea name="message"
                                  id="message"
                                  v-model="updateReturnStatusForm.message"
                                  class="form-input block w-full"></textarea>
                        <jet-input-error :message="updateReturnStatusForm.error('message')"/>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="rejectReturn = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="updateStatus(6)"
                                       :class="{ 'opacity-25': updateReturnStatusForm.processing }"
                                       :disabled="updateReturnStatusForm.processing">
                        تایید
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <jet-confirmation-modal maxHeight="h-96" class="h-16" :show="viewPaymentModal"
                                    @close="viewPaymentModal = false">
                <template #title>
                    عودت هزینه دستگاه
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div>
                            <jet-secondary-button class="text-gray-300">پرداخت آنلاین</jet-secondary-button>
                            <jet-button
                                :class="{ 'bg-blue-500 hover:bg-blue-400': updateReturnStatusForm.type==2 }"
                                class="border-blue-500 bg-blue-200 hover:bg-blue-400 active:bg-blue-600">واریز به حساب
                            </jet-button>
                        </div>
                        <div class="grid grid-cols-2 mt-3" v-if="updateReturnStatusForm.type==2">
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="ref_code" value="کد پیگیری پرداخت"></jet-label>
                                <jet-input name="ref_code"
                                           id="ref_code"
                                           v-model="updateReturnStatusForm.ref_code">
                                </jet-input>
                                <jet-input-error :message="updateReturnStatusForm.error('ref_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="payment_date" value="تاریخ پرداخت"></jet-label>
                                <date-picker
                                    :clearable="true"
                                    type="datetime"
                                    ref="payment_date_cal"
                                    input-format="YYYY-MM-DD H:m"
                                    format="jYYYY/jMM/jDD  H:m"
                                    @change="selectPaymentDate"
                                    element="payment_date"
                                    v-model="paymentDate"></date-picker>
                                <jet-input name="payment_date"
                                           id="payment_date"
                                           readonly="true"
                                           v-model="paymentDate">
                                </jet-input>
                                <jet-input-error :message="updateReturnStatusForm.error('payment_date')" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="viewPaymentModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="updateStatus(4)"
                                       :class="{ 'opacity-25': updateReturnStatusForm.processing }"
                                       :disabled="updateReturnStatusForm.processing">
                        تایید
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
import JetDangerButton from '@/Jetstream/DangerButton';

export default {
    name: "View",
    components: {
        Dashboard,
        JetActionMessage,
        JetConfirmationModal,
        JetDangerButton,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        datePicker: VuePersianDatetimePicker
    },
    props: {
        device: Object,
        deviceTypes: Array,
        accessories: Array,
    },
    data() {
        return {
            imageFiles: {
                faktorFilePreview: this.device.fileUrl,
            },
            fileUploadErrors: {
                faktorFile: null,
            },
            editReturnForm: this.$inertia.form({
                '_method': 'POST',
                device_type_id: this.device.device_type_id,
                serial: this.device.serial,
                name: this.device.name,
                mobile: this.device.mobile,
                national_code: this.device.national_code,
                description: this.device.description,
                amount: this.device.amount,
                file: '',
                status: this.device.status,
                accessories: this.device.accessories
            }, {
                bag: 'editReturnForm',
                resetOnSuccess: false
            }),
            updateReturnStatusForm: this.$inertia.form({
                '_method': 'PUT',
                status: this.device.status,
                message: null,
                type: 2,
                ref_code: null,
                payment_date: null,
            }, {
                bag: 'updateReturnStatusForm',
                resetOnSuccess: false
            }),
            paymentDate: null,
            viewPaymentModal: false,
            rejectReturn: false,
        }
    },
    methods: {
        submitEditReturnForm() {
            this.editReturnForm.post(route('dashboard.returns.update', {returnId: this.device.id})).then(response => {
                if (!this.editReturnForm.hasErrors()) {

                }
            })
        },
        updateStatus(status) {
            this.updateReturnStatusForm.status = status;
            this.updateReturnStatusForm.put(route('dashboard.returns.updateStatus', {returnId: this.device.id})).then(response => {
                if (!this.updateReturnStatusForm.hasErrors()) {
                    this.rejectReturn = false;
                    this.viewPaymentModal = false;
                }
            })
        },
        selectPaymentDate(e) {
            this.updateReturnStatusForm.payment_date = e.format('YYYY/MM/DD H:m');
        },
        paymentStatusColors(status) {
            switch (status) {
                case 0:
                    return 'bg-blue-100 text-blue-800';
                case 1:
                    return 'bg-yellow-100 text-yellow-800';
                case 2:
                    return 'bg-green-100 text-green-800';
            }
        },
        onFaktorFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadErrors.faktor = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadErrors.faktor = '';
            this.editReturnForm.file = e.target.files[0];
            this.imageFiles.faktorFilePreview = URL.createObjectURL(file);
        },
    },
    computed: {
        disableInputs: function () {
            if (this.$page.user.level == 'SUPERUSER' || this.$page.user.level == 'ADMIN' || this.$page.user.level == 'OFFICE') {
                return false;
            }
            return !(this.device.status == 0 || this.device.status == 1);
        }
    }
}
</script>

<style scoped>

</style>
