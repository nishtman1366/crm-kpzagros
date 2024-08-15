<template>
    <guest>
        <template #header>
            <div class="text-2xl">پیگیری درخواست تعمیرات</div>
        </template>
        <template #contents>
            <div class="w-full lg:w-4/5 xl:w-2/3 mx-auto p-1">
                <div class="flex flex-col lg:flex-row items-center lg:justify-between">
                    <div class="flex items-center justify-between">
                       <div
                           :class="statusColors(repair.status)"
                           class="text-lg px-4 py-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{ repair.statusText }}
                                        </div>
                        <div v-if="repair.status===5">
                            <jet-button @click.native="showPaymentModal"
                                        class="mr-8 bg-green-600 hover:bg-green-500 active:bg-green-700">پرداخت هزینه
                            </jet-button>
                        </div>
                    </div>
                    <div class="my-4 w-full lg:w-max-content flex lg:block items-center justify-between">
                        <div>تاریخ: <span class="font-bold">{{ repair.jCreatedAt }}</span></div>
                        <div>کد رهگیری: <span class="font-bold">{{ repair.tracking_code }}</span></div>
                    </div>
                </div>
                <section class="p-3 bg-gray-100">
                    <div class="grid grid-cols-2 gap-3">
                        <div>نام پذیرنده: <span class="font-bold">{{ repair.name }}</span></div>
                        <div>مدل دستگاه: <span
                            class="font-bold">{{ repair.device_type && repair.device_type.name }}</span>
                        </div>
                        <div>شماره تماس:<span class="font-bold">{{ repair.mobile }}</span></div>
                        <div>سریال دستگاه: <span class="font-bold">{{ repair.serial }}</span></div>
                    </div>
                </section>
                <section class="p-3">
                    <div class="my-4">
                        <p class="mb-3 text-lg font-bold">ایرادات اعلام شده توسط مشتری:</p>
                        <div class="flex items-center justify-between flex-wrap">
                            <div v-for="item in  repairTypesList" class="ml-1 my-2 pl-1">{{ item.type.name }}</div>
                        </div>
                    </div>
                    <div class="my-4">
                        <p class="mb-3 text-lg font-bold">اقلام همراه دستگاه:</p>
                        <p>{{ repair.accessoryList }}</p>
                    </div>
                    <div>
                        <p class="mb-3 text-lg font-bold">توضیحات تکمیلی واحد پذیرش:</p>
                        <p class="text-justify">{{ repair.description }}</p>
                    </div>
                </section>
                <section class="p-3 my-2 mx-3 bg-gray-100">
                    <div class="my-4">
                        <p class="mb-3 text-lg font-bold">گزارش عملیات:</p>
                        <p class="text-justify">{{ repair.technical_description }}</p>
                    </div>
                </section>
                <section v-if="repair.price && repair.price!==0" class="p-3 my-2 mx-3 bg-gray-100">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
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
                            <tr v-for="payment in repair.payments" :key="payment.id">
                                <td class="py-4 text-center text-gray-900">
                                    {{ payment.type && payment.type.name }}
                                </td>
                                <td class="py-4 text-center text-gray-900">{{ payment.jDate }}</td>
                                <td class="py-4 text-center text-gray-900">{{ payment.ref_code }}</td>
                                <td class="py-4 text-center text-gray-900">{{ payment.tracking_code }}</td>
                                <td :colspan="payment.status==2 ? 2 : ''"
                                    class="py-4 text-center text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="paymentStatusColors(payment.status)">
                                                    {{ payment.statusText }}
                                            </span>
                                </td>
                                <td class="py-4 text-center text-gray-900">
<!--                                    <InertiaLink-->
<!--                                        v-if="payment.status==1 && $page.user.level==='ADMIN'  || $page.user.level==='SUPERUSER'"-->
<!--                                        :href="route('dashboard.payments.confirm',{paymentId: payment.id})"-->
<!--                                        class="tooltip-box text-indigo-600 hover:text-indigo-900">-->
<!--                                        <button title="تایید پرداخت"-->
<!--                                                v-b-tooltip.hover>-->
<!--                                            <i id="test" class="material-icons">check</i>-->
<!--                                        </button>-->
<!--                                    </InertiaLink>-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="my-4">
                        <p class="mb-3 text-lg font-bold">هزینه تعمیرات: {{ repair.priceText }}</p>
                        <div class="">
                            <p>پذیرنده گرامی</p>
                            <p> شما می‌توانید هزینه تعمیرات را به صورت آنلاین یا یکی از روش‌های زیر پرداخت نمایید.</p>
                            <ul>
                                <li class="mt-2 mr-2">واریز به حساب بانکی شماره <span
                                    class="font-bold mx-1">{{ $page.configs.repairBankAccount }}</span> نزد بانک
                                    <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                        class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                </li>
                                <li class="mt-2 mr-2">واریز بین‌بانکی به شماره شبا <span
                                    class="font-bold mx-1">{{ $page.configs.repairBankSheba }}</span> نزد بانک
                                    <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                        class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                </li>
                                <li class="mt-2 mr-2">واریز کارت به کارت به شماره <span
                                    class="font-bold mx-1">{{ $page.configs.repairBankCart }}</span> نزد بانک
                                    <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                        class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <jet-confirmation-modal :show="viewPaymentModal"
                                    @close="viewPaymentModal = false">
                <template #title>
                    پرداخت هزینه تعمیرات
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-4 col-span-2 text-right">
                            <p class="mb-3 text-lg font-bold">هزینه تعمیرات: {{ repair.priceText }}</p>
                            <div class="">
                                <p>پذیرنده گرامی</p>
                                <p> شما می‌توانید هزینه تعمیرات را به صورت آنلاین یا یکی از روش‌های زیر پرداخت
                                    نمایید.</p>
                                <ul>
                                    <li class="mt-2 mr-2">واریز به حساب بانکی شماره <span
                                        class="font-bold mx-1">{{ $page.configs.repairBankAccount }}</span> نزد بانک
                                        <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                            class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                    </li>
                                    <li class="mt-2 mr-2">واریز بین‌بانکی به شماره شبا <span
                                        class="font-bold mx-1">{{ $page.configs.repairBankSheba }}</span> نزد بانک
                                        <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                            class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                    </li>
                                    <li class="mt-2 mr-2">واریز کارت به کارت به شماره <span
                                        class="font-bold mx-1">{{ $page.configs.repairBankCart }}</span> نزد بانک
                                        <span class="font-bold">{{ $page.configs.repairBankName }}</span> بنام <span
                                            class="font-bold">{{ $page.configs.repairBankOwner }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-8">
                            <jet-button
                                :disabled="requestOnlinePaymentLoading"
                                :class="{ 'bg-green-500 hover:bg-green-400': submitPaymentForm.type==1 }"
                                class="border-green-500 bg-green-200 hover:bg-green-400 active:bg-green-600 text-gray-900 disabled:opacity-25"
                                @click.native="requestOnlinePayment">پرداخت آنلاین
                            </jet-button>
                            <jet-button @click.native="submitPaymentForm.type=2;requestOnlinePaymentLoading=false"
                                        :disabled="requestOnlinePaymentLoading"
                                        :class="{ 'bg-blue-500 hover:bg-blue-400': submitPaymentForm.type==2 }"
                                        class="border-blue-500 bg-blue-200 hover:bg-blue-400 active:bg-blue-600 text-gray-900 disabled:opacity-25">
                                واریز به حساب
                            </jet-button>
                        </div>
                        <div class="mt-3" v-if="requestOnlinePaymentLoading">
                            <div v-if="onlinePaymentError" class="text-center text-lg">{{ onlinePaymentError }}</div>
                            <div v-else class="text-center text-lg">
                                در حال ارسال به درگاه پرداخت
                            </div>
                        </div>
                        <div class="grid grid-cols-2 mt-3" v-else-if="submitPaymentForm.type==2">
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="ref_code" value="کد پیگیری پرداخت"></jet-label>
                                <jet-input name="ref_code"
                                           id="ref_code"
                                           v-model="submitPaymentForm.ref_code">
                                </jet-input>
                                <jet-input-error :message="submitPaymentForm.error('ref_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="payment_date" value="تاریخ پرداخت"></jet-label>
                                <date-picker
                                    :clearable="true"
                                    type="date"
                                    ref="payment_date_cal"
                                    input-format="YYYY-MM-DD"
                                    format="jYYYY/jMM/jDD"
                                    @change="selectPaymentDate"
                                    element="payment_date"
                                    v-model="paymentDate"></date-picker>
                                <jet-input name="payment_date"
                                           id="payment_date"
                                           readonly="true"
                                           v-model="paymentDate">
                                </jet-input>
                                <jet-input-error :message="submitPaymentForm.error('payment_date')" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer v-if="!requestOnlinePaymentLoading">
                    <jet-secondary-button class="ml-2" @click.native="viewPaymentModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="bg-green-500 ml-2 hover:bg-green-400" @click.native="submitPayment"
                                :class="{ 'opacity-25': submitPaymentForm.processing }"
                                :disabled="submitPaymentForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>

        </template>
    </guest>
</template>

<script>
import Guest from "@/Layouts/Guest";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetButton from "@/Jetstream/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import {Inertia} from "@inertiajs/inertia";

export default {
    name: "View",
    components: {
        Guest,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetConfirmationModal,
        JetSectionBorder,
        datePicker: VuePersianDatetimePicker
    },
    props: {
        repair: Object,
        repairTypesList: Array,
        national_code: Number,
        tracking_code: Number
    },
    data() {
        return {
            viewPaymentModal: false,
            requestOnlinePaymentLoading: false,
            paymentDate: '',
            onlinePaymentError: '',
            submitPaymentForm: this.$inertia.form({
                '_method': 'PUT',
                type: null,
                ref_code: '',
                payment_date: '',
                status: 6,
            }, {
                bag: 'submitPaymentForm',
            }),
        }
    },
    methods: {
        showPaymentModal() {
            this.viewPaymentModal = true;
        },
        selectPaymentDate(e) {
            this.submitPaymentForm.payment_date = e.format('YYYY/MM/DD');
        },
        submitPayment() {
            this.submitPaymentForm.post(route('public.repairs.update', {repair: this.repair.id})).then(response => {
                if (!this.submitPaymentForm.hasErrors()) {
                    this.viewPaymentModal = false;
                }
            })
        },
        requestOnlinePayment() {
            this.submitPaymentForm.type = 1;
            this.requestOnlinePaymentLoading = true;
            this.onlinePaymentError = '';
            axios.get(route('public.payments.ipg.request', {type: 'repairs', id: this.repair.id}))
                .then(response => {
                    this.viewPaymentModal = true;
                    if (response.data.paymentRequest && response.data.paymentRequest.status === 'success') {
                        // console.log(response.data.paymentRequest.redirectUrl);
                        window.location.href = response.data.paymentRequest.redirectUrl;
                    }
                    // this.requestOnlinePaymentLoading = false;
                })
                .catch(error => {
                    this.onlinePaymentError = error.response.data.message;

                })
                .finally(() => {

                })
        },
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
                    return 'bg-red-100 text-red-800';
            }
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
    }
}
</script>

<style scoped>

</style>
