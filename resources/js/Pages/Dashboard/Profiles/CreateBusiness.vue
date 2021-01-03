<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت اطلاعات کسب و کار {{customer.fullName}}</template>
        <template #dashboardContent>
            <ProfileSteps :step="2"
                          :customer-info="!!profile.customer"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false"
            ></ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات کسب و کار</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به کسب و کار مشتری را وارد نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <div class="grid md:grid-cols-8 gap-2">
                                            <div class="col-2 sm:col-span-2">
                                                <label for="ostan_id" class="block text-sm font-medium text-gray-700">
                                                    استان:
                                                </label>
                                                <select id="ostan_id" name="ostan_id" ref="ostan_id"
                                                        v-model="businessForm.ostan_id"
                                                        autocomplete="ostan_id" v-on:change="listShahrestansItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب استان</option>
                                                    <option v-for="ostan in ostans" :key="ostan.id"
                                                            :value="ostan.id">
                                                        {{ostan.name}}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('ostan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-2 sm:col-span-2">
                                                <label for="shahrestan_id"
                                                       class="block text-sm font-medium text-gray-700">
                                                    شهرستان:
                                                </label>
                                                <select id="shahrestan_id" name="shahrestan_id" ref="shahrestan_id"
                                                        v-model="businessForm.shahrestan_id"
                                                        autocomplete="shahrestan_id"
                                                        v-on:change="listBakhshItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهرستان</option>
                                                    <option v-for="shahrestan in shahrestanItems"
                                                            :key="shahrestan.id" :value="shahrestan.id">
                                                        {{shahrestan.name}}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahrestan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-2 sm:col-span-2">
                                                <label for="bakhsh_id" class="block text-sm font-medium text-gray-700">
                                                    بخش:
                                                </label>
                                                <select id="bakhsh_id" name="bakhsh_id" autocomplete="bakhsh_id"
                                                        v-model="businessForm.bakhsh_id"
                                                        ref="bakhsh_id"
                                                        v-on:change="listShahrItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب بخش</option>
                                                    <option v-for="bakhsh in bakhshItems"
                                                            :key="bakhsh.id" :value="bakhsh.id">
                                                        {{bakhsh.name}}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('bakhsh_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-2 sm:col-span-2">
                                                <label for="shahr_id" class="block text-sm font-medium text-gray-700">
                                                    شهر:
                                                </label>
                                                <select id="shahr_id" name="shahr_id" autocomplete="shahr_id"
                                                        v-model="businessForm.shahr_id"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهر</option>
                                                    <option v-for="shahr in shahrItems"
                                                            :key="shahr.id" :value="shahr.id">
                                                        {{shahr.name}}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahr_id')"
                                                                 class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار"
                                               ref="name"
                                               id="name"
                                               v-model="businessForm.name"/>
                                        <jet-input-error :message="businessForm.error('name')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="name_english" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار به انگلیسی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار به انگلیسی"
                                               ref="name_english"
                                               id="name_english"
                                               v-model="businessForm.name_english"/>
                                        <jet-input-error :message="businessForm.error('name_english')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="senf" class="block text-sm font-medium text-gray-700">
                                            صنف مرتبط:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="صنف مرتبط"
                                               ref="senf"
                                               id="senf"
                                               v-model="businessForm.senf"/>
                                        <jet-input-error :message="businessForm.error('senf')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            کد پستی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد پستی"
                                               ref="postal_code"
                                               id="postal_code"
                                               v-model="businessForm.postal_code"/>
                                        <jet-input-error :message="businessForm.error('postal_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-6 sm:col-span-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">
                                            آدرس:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="آدرس"
                                               ref="address"
                                               id="address"
                                               v-model="businessForm.address"/>
                                        <jet-input-error :message="businessForm.error('address')" class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="phone_code" class="block text-sm font-medium text-gray-700">
                                            پیش شماره شهرستان:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="پیش شماره شهرستان"
                                               ref="phone_code"
                                               id="phone_code"
                                               v-model="businessForm.phone_code"/>
                                        <jet-input-error :message="businessForm.error('phone_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">
                                            تلفن تماس:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن تماس"
                                               ref="phone"
                                               id="phone"
                                               v-model="businessForm.phone"/>
                                        <jet-input-error :message="businessForm.error('phone')" class="mt-2"/>
                                    </div>
                                    <div class="col-1 sm:col-span-1">
                                        <label for="has_license" class="block text-sm font-medium text-gray-700">
                                            جواز کسب:
                                        </label>
                                        <div class="grid grid-cols-2">
                                            <div class="col-1 flex items-center">
                                                <input id="license_yes"
                                                       name="type"
                                                       type="radio"
                                                       v-model="hasLicense"
                                                       value="YES"
                                                       class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                >
                                                <label for="type_person"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    دارد
                                                </label>
                                            </div>
                                            <div class="col-1 flex items-center">
                                                <input id="license_no"
                                                       name="type"
                                                       type="radio"
                                                       v-model="hasLicense"
                                                       value="NO"
                                                       class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                >
                                                <label for="type_organization"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    ندارد
                                                </label>
                                            </div>
                                        </div>
                                        <jet-input-error :message="businessForm.error('has_license')" class="mt-2"/>
                                    </div>
                                    <div v-if="hasLicense==='YES'" class="col-3 sm:col-span-3">
                                        <label for="license_code" class="block text-sm font-medium text-gray-700">
                                            شماره جواز کسب:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="شماره جواز کسب"
                                               ref="license_code"
                                               id="license_code"
                                               v-model="businessForm.license_code"/>
                                        <jet-input-error :message="businessForm.error('license_code')" class="mt-2"/>
                                    </div>
                                    <div v-if="hasLicense==='YES'" class="col-3 sm:col-span-3">
                                        <label for="license_date" class="block text-sm font-medium text-gray-700">
                                            تاریخ صدور جواز کسب:
                                        </label>
                                        <date-picker
                                            ref="regDate_cal"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectLicenseDate"
                                            element="license_date"
                                            v-model="licenseDate"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ صدور جواز کسب"
                                               ref="license_date"
                                               id="license_date"
                                               v-model="licenseDate"
                                               readonly="true"/>
                                        <jet-input-error :message="businessForm.error('license_date')" class="mt-2"/>
                                    </div>
                                    <div v-if="hasLicense==='YES'" class="col-6 sm:col-span-6">
                                        <div
                                            class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg v-if="imageFiles.licenseFilePreview===''"
                                                     class="mx-auto h-12 w-12 text-gray-400"
                                                     stroke="currentColor"
                                                     fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"/>
                                                </svg>
                                                <img v-else :src="imageFiles.licenseFilePreview">
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="license_file"
                                                           class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>انتخاب فایل</span>
                                                        <input id="license_file"
                                                               name="license_file"
                                                               type="file"
                                                               @change="onLicenseFileChange"
                                                               class="sr-only">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    تصویر جواز کسب
                                                </p>
                                                <jet-input-error
                                                    :message="businessForm.error('license_file')"
                                                    class="mt-2"/>
                                                <jet-input-error
                                                    :message="fileUploadErrors.license_file"
                                                    class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="hasLicense==='NO'" class="col-6 sm:col-span-6">
                                        <div
                                            class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg v-if="imageFiles.esteshhadFilePreview===''"
                                                     class="mx-auto h-12 w-12 text-gray-400"
                                                     stroke="currentColor"
                                                     fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"/>
                                                </svg>
                                                <img v-else :src="imageFiles.esteshhadFilePreview">
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="esteshhad_file"
                                                           class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>انتخاب فایل</span>
                                                        <input id="esteshhad_file"
                                                               name="esteshhad_file"
                                                               type="file"
                                                               @change="onEsteshhadFileChange"
                                                               class="sr-only">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    تصویر استشهادنامه
                                                </p>
                                                <jet-input-error
                                                    :message="businessForm.error('esteshhad_file')"
                                                    class="mt-2"/>
                                                <jet-input-error
                                                    :message="fileUploadErrors.esteshhad_file"
                                                    class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-left sm:px-6">
                                <button type="button"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        v-on:click="submitBusinessForm"
                                        :disabled="submitBusinessFormLoading">
                                    <div v-if="submitBusinessFormLoading"
                                         class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-5 w-5 mx-1"></div>
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
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
    import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";

    export default {
        name: "CreateBusiness",
        components: {Dashboard, JetInput, JetInputError, datePicker: VuePersianDatetimePicker, ProfileSteps},
        props: {
            customer: Object,
            profile: Object,
            ostans: Array,
            shahrestans: Array,
            bakhshs: Array,
            shahrs: Array,
            profileId: Number,
        },
        data() {
            return {
                shahrestanItems: [],
                bakhshItems: [],
                shahrItems: [],
                hasLicense: 'YES',
                imageFiles: {
                    licenseFilePreview: '',
                    esteshhadFilePreview: '',
                },
                fileUploadErrors: {
                    license_file: '',
                    esteshhad_file: '',
                },
                licenseDate: '',
                businessForm: this.$inertia.form({
                    '_method': 'POST',
                    profile_id: '',
                    ostan_id: '',
                    shahrestan_id: '',
                    bakhsh_id: '',
                    shahr_id: '',
                    phone_code: '',
                    senf: '',
                    name: '',
                    name_english: '',
                    address: '',
                    postal_code: '',
                    phone: '',
                    has_license: '',
                    license_code: '',
                    license_date: '',
                    license_file: '',
                    esteshhad_file: '',
                }, {
                    bag: 'businessForm',
                    resetOnSuccess: false
                }),

                submitBusinessFormLoading: false
            }
        },
        methods: {
            listShahrestansItems() {
                let ostanId = this.$refs.ostan_id.value;
                let shahrestanList = [];
                for (let shahrestan of this.shahrestans) {
                    if (shahrestan.ostan == ostanId) {
                        shahrestanList.push({id: shahrestan.id, name: shahrestan.name});
                    }
                }
                this.shahrestanItems = shahrestanList;
            },
            listBakhshItems() {
                let shahrestanId = this.$refs.shahrestan_id.value;
                let bakhshList = [];
                for (let bakhsh of this.bakhshs) {
                    if (bakhsh.shahrestan == shahrestanId) {
                        bakhshList.push({id: bakhsh.id, name: bakhsh.name});
                    }
                }
                this.bakhshItems = bakhshList;
            },
            listShahrItems() {
                let bakhshId = this.$refs.bakhsh_id.value;
                let shahrList = [];
                for (let shahr of this.shahrs) {
                    if (shahr.bakhsh == bakhshId) {
                        shahrList.push({id: shahr.id, name: shahr.name});
                    }
                }
                this.shahrItems = shahrList;
            },
            onLicenseFileChange(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.license_file = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.license_file = '';
                this.businessForm.license_file = e.target.files[0];
                this.imageFiles.licenseFilePreview = URL.createObjectURL(file);
            },
            onEsteshhadFileChange(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.esteshhad_file = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.esteshhad_file = '';
                this.businessForm.esteshhad_file = e.target.files[0];
                this.imageFiles.esteshhadFilePreview = URL.createObjectURL(file);
            },
            resetFileUploadErrors() {
                this.fileUploadErrors = {
                    license_file: '',
                    esteshhad_file: '',
                };
            },
            selectLicenseDate(e) {
                this.businessForm.license_date = e.format('YYYY/MM/DD');
            },
            submitBusinessForm() {
                this.submitBusinessFormLoading = true;
                this.businessForm.profile_id = this.profileId;
                this.businessForm.has_license = this.hasLicense;
                this.resetFileUploadErrors();
                this.businessForm.post(route('dashboard.profiles.businesses.store', {profileId: this.profileId})).then(response => {
                    console.log(this.businessForm.processing);
                    this.submitBusinessFormLoading = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>
