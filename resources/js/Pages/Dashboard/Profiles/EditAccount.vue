<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت اطلاعات حساب های بانکی {{customer.fullName}}</template>
        <template #dashboardContent>
            <ProfileSteps :step="3"
                          :customer-info="!!profile.customer"
                          :customer-id="profile.customer ? profile.customer.id : ''"
                          :business-info="!!profile.business"
                          :accounts-info="profile.accounts.length > 0"
                          :device-info="!!profile.device_type"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER' ? true : false"
            ></ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات حساب های بانکی</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به حساب های بانکی مشتری را وارد نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div v-for="(account, index) in accountForm.accounts" :key="index"
                                 class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid  md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <div class="grid  md:grid-cols-8 gap-1">
                                            <div class="col-6 sm:col-span-6">
                                                <div class="grid md:grid-cols-6 gap-2">
                                                    <div class="col-3 sm:col-span-3">
                                                        <label :for="'bank_id_'+index"
                                                               class="block text-sm font-medium text-gray-700">
                                                            نام بانک:
                                                        </label>
                                                        <select :id="'bank_id_'+index" :name="'bank_id_'+index"
                                                                :ref="'bank_id_'+index"
                                                                v-model="account.bank_id"
                                                                :autocomplete="'bank_id_'+index"
                                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            <option value="0">انتخاب بانک</option>
                                                            <option v-for="bank in banks" :key="bank.id"
                                                                    :value="bank.id">
                                                                {{bank.name}}
                                                            </option>
                                                        </select>
                                                        <jet-input-error
                                                            :message="accountForm.error('accounts.'+index+'.bank_id')"
                                                            class="mt-2"/>
                                                    </div>
                                                    <div class="col-3 sm:col-span-3">
                                                        <label :for="'branch_'+index"
                                                               class="block text-sm font-medium text-gray-700">
                                                            کد شعبه:
                                                        </label>
                                                        <input type="text"
                                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                               placeholder="کد شعبه"
                                                               :ref="'branch_'+index"
                                                               :id="'branch_'+index"
                                                               v-model="account.branch"/>
                                                        <jet-input-error
                                                            :message="accountForm.error('accounts.'+index+'.branch')"
                                                            class="mt-2"/>
                                                    </div>
                                                    <div class="col-3 sm:col-span-3">
                                                        <label :for="'account_number_'+index"
                                                               class="block text-sm font-medium text-gray-700">
                                                            شماره حساب:
                                                        </label>
                                                        <input type="text"
                                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                               placeholder="شماره حساب"
                                                               :ref="'account_number_'+index"
                                                               :id="'account_number_'+index"
                                                               v-model="account.account_number"/>
                                                        <jet-input-error
                                                            :message="accountForm.error('accounts.'+index+'.account_number')"
                                                            class="mt-2"/>
                                                    </div>
                                                    <div class="col-3 sm:col-span-3">
                                                        <label :for="'sheba_code_'+index"
                                                               class="block text-sm font-medium text-gray-700">
                                                            شماره شبا:
                                                        </label>
                                                        <input type="text"
                                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                               placeholder="شماره شبا بدون IR"
                                                               :ref="'sheba_code_'+index"
                                                               :id="'sheba_code_'+index"
                                                               v-model="account.sheba_code"/>
                                                        <jet-input-error
                                                            :message="accountForm.error('accounts.'+index+'.sheba_code')"
                                                            class="mt-2"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2 sm:col-span-2">
                                                <div
                                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg v-if="imageFiles[index].shebaFilePreview===''"
                                                             class="mx-auto h-12 w-12 text-gray-400"
                                                             stroke="currentColor"
                                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"/>
                                                        </svg>
                                                        <img v-else :src="imageFiles[index].shebaFilePreview">
                                                        <div class="flex text-sm text-gray-600">
                                                            <label :for="'sheba_file_'+index"
                                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>انتخاب فایل</span>
                                                                <input :id="'sheba_file_'+index"
                                                                       :name="'sheba_file_'+index"
                                                                       type="file"
                                                                       @change="onShebaFileChange(index,$event)"
                                                                       class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            تصویر تایید شماره شبا
                                                        </p>
                                                        <jet-input-error
                                                            :message="accountForm.error('accounts.'+index+'.sheba_file')"
                                                            class="mt-2"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label :for="'first_name_'+index"
                                               class="block text-sm font-medium text-gray-700">
                                            نام:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام"
                                               :ref="'first_name_'+index"
                                               :id="'first_name_'+index"
                                               v-model="account.first_name"/>
                                        <jet-input-error :message="accountForm.error('accounts.'+index+'.first_name')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label :for="'last_name_'+index"
                                               class="block text-sm font-medium text-gray-700">
                                            نام خانوادگی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام خانوادگی"
                                               :ref="'last_name_'+index"
                                               :id="'last_name_'+index"
                                               v-model="account.last_name"/>
                                        <jet-input-error :message="accountForm.error('accounts.'+index+'.last_name')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label :for="'birthday_'+index" class="block text-sm font-medium text-gray-700">
                                            تاریخ تولد:
                                        </label>
                                        <date-picker
                                            :ref="'birthday_'+index"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectBirthday($event,index)"
                                            :element="'birthday_'+index"
                                            v-model="birthday[index]"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ تولد"
                                               :ref="'birthday_'+index"
                                               :id="'birthday_'+index"
                                               v-model="birthday[index]"/>
                                        <jet-input-error :message="accountForm.error('accounts.'+index+'.birthday')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label :for="'national_code_'+index"
                                               class="block text-sm font-medium text-gray-700">
                                            کد ملی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد ملی"
                                               :ref="'national_code_'+index"
                                               :id="'national_code_'+index"
                                               v-model="account.national_code"/>
                                        <jet-input-error
                                            :message="accountForm.error('accounts.'+index+'.national_code')"
                                            class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label :for="'mobile_'+index" class="block text-sm font-medium text-gray-700">
                                            شماره تلفن همراه:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="شماره تلفن همراه"
                                               :ref="'mobile_'+index"
                                               :id="'mobile_'+index"
                                               v-model="account.mobile"/>
                                        <jet-input-error :message="accountForm.error('accounts.'+index+'.mobile')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3 text-center">
                                        <jet-button @click.native="setCustomerInfoForAccount(index)">
                                            دریافت اطلاعات مشتری
                                        </jet-button>
                                    </div>
                                    <div v-if="accountForm.accounts.length!==1" class="col-6 sm:col-span-6 text-left">
                                        <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                v-on:click="removeAccountRow(index)">
                                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                            حذف این حساب
                                        </button>
                                    </div>
                                    <div class="col-6 sm:col-span-6" aria-hidden="true">
                                        <div class="py-5col-6 sm:col-span-6">
                                            <div class="border-t border-gray-200"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 text-left sm:px-6">
                                <button type="submit"
                                        class="inline-flex justify-center my-3 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        v-on:click="NewAccountRow">
                                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                              d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M11,15V12H9V15H6V17H9V20H11V17H14V15H11Z"/>
                                    </svg>
                                    اضافه کردن حساب جدید
                                </button>
                                <button type="submit"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        v-on:click="submitAccountForm">
                                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                              d="M15,9H5V5H15M12,19A3,3 0 0,1 9,16A3,3 0 0,1 12,13A3,3 0 0,1 15,16A3,3 0 0,1 12,19M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3Z"/>
                                    </svg>
                                    ذخیره اطلاعات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetInput from '@/Jetstream/Input';
    import JetInputError from '@/Jetstream/InputError';
    import JetButton from '@/Jetstream/Button';
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
    import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";

    export default {
        name: "CreateAccount",
        components: {Dashboard, JetInput, JetInputError, datePicker: VuePersianDatetimePicker, JetButton, ProfileSteps},
        props: {
            profileId: Number,
            profile: Object,
            customer: Object,
            banks: Array,
            accounts: Array
        },
        data() {
            return {
                imageFiles: [{shebaFilePreview: ''}],
                fileUploadErrors: [{sheba_file: ''}],
                birthday: [],
                accountForm: this.$inertia.form({
                    '_method': 'POST',
                    accounts: [{
                        customer_id: this.customer.id,
                        bank_id: '',
                        branch: '',
                        sheba_code: '',
                        account_number: '',
                        first_name: '',
                        last_name: '',
                        national_code: '',
                        birthday: '',
                        mobile: '',
                        sheba_file: '',
                    }]
                }, {
                    bag: 'accountForm',
                    resetOnSuccess: false
                })
            }
        },
        mounted() {
            let list = [];
            this.imageFiles = [];
            for (let account of this.accounts) {
                this.birthday.push(account.account.jBirthday);
                list.push({
                    id: account.account.id,
                    customer_id: this.customer.id,
                    bank_id: account.account.bank_id,
                    branch: account.account.branch,
                    sheba_code: account.account.sheba_code,
                    account_number: account.account.account_number,
                    first_name: account.account.first_name,
                    last_name: account.account.last_name,
                    national_code: account.account.national_code,
                    birthday: account.account.birthday,
                    mobile: account.account.mobile,
                    sheba_file: '',
                });
                this.imageFiles.push({shebaFilePreview: account.account.shebaFile});
                this.fileUploadErrors.push({sheba_file: ''});
            }
            this.accountForm = this.$inertia.form({
                '_method': 'PUT',
                accounts: list
            }, {
                bag: 'accountForm',
                resetOnSuccess: false
            })

        },
        methods: {
            onShebaFileChange(index, e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors[index].sheba_file = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors[index].sheba_file = '';
                this.accountForm.accounts[index].sheba_file = e.target.files[0];
                this.imageFiles[index].shebaFilePreview = URL.createObjectURL(file);
            },
            setCustomerInfoForAccount(index) {
                this.accountForm.accounts[index].first_name = this.customer.first_name;
                this.accountForm.accounts[index].last_name = this.customer.last_name;
                this.accountForm.accounts[index].national_code = this.customer.national_code;
                this.accountForm.accounts[index].birthday = this.customer.birthday;
                this.birthday.push(this.customer.jBirthday);
                this.accountForm.accounts[index].mobile = this.customer.mobile;
            },
            NewAccountRow() {
                this.accountForm.accounts.push({
                    customer_id: this.customer.id,
                    bank_id: '',
                    branch: '',
                    sheba_code: '',
                    account_number: '',
                    first_name: '',
                    last_name: '',
                    national_code: '',
                    birthday: '',
                    mobile: '',
                    sheba_file: '',
                });
                this.imageFiles.push({shebaFilePreview: ''});
                this.fileUploadErrors.push({sheba_file: ''});
            },
            removeAccountRow(index) {
                this.accountForm.accounts.splice(index, 1);
                this.imageFiles.splice(index, 1);
            },
            selectBirthday(e, index) {
                this.accountForm.accounts[index].birthday = e.format('YYYY/MM/DD');
            },
            submitAccountForm() {
                for (let i = 0; i < this.accountForm.accounts.length; i++) {
                    this.accountForm.accounts[i].branch = this.fixNumbers(this.accountForm.accounts[i].branch);
                    this.accountForm.accounts[i].sheba_code = this.fixNumbers(this.accountForm.accounts[i].sheba_code);
                    this.accountForm.accounts[i].account_number = this.fixNumbers(this.accountForm.accounts[i].account_number);
                    this.accountForm.accounts[i].mobile = this.fixNumbers(this.accountForm.accounts[i].mobile);
                    this.accountForm.accounts[i].national_code = this.fixNumbers(this.accountForm.accounts[i].national_code);
                }
                this.accountForm.post(route('dashboard.profiles.accounts.update', {profileId: this.profileId})).then(response => {

                })
            },
            fixNumbers(str) {
                let persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g];
                let arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g];
                if (typeof str === 'string') {
                    for (var i = 0; i < 10; i++) {
                        str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
                    }
                }
                return str;
            }
        }
    }
</script>

<style scoped>

</style>
