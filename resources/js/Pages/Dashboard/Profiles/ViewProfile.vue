<template>
    <Dashboard>
        <template #breadcrumb> / ثبت درخواست / بررسی اطلاعات {{ profile.customer.fullName }}</template>
        <template #dashboardContent>
            <ProfileSteps :step="5"
                          :customer-info="!!profile.customer"
                          :customer-id="profile.customer ? profile.customer.id : ''"
                          :business-info="profile.business && !!profile.business"
                          :accounts-info="profile.accounts.length > 0"
                          :device-info="!!profile.device_type"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER' ? true : false"
            ></ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="grid md:grid-cols-6 gap-6">
                            <div class="col-6 sm:col-span-6 overflow-y-auto" style="height: 200px">
                                <p class="text-indigo-600 text-lg m-2">گزارش پرونده</p>
                                <div v-for="error in profile.messages"
                                     :class="'border-'+error.color+'-600 bg-'+error.color+'-100'"
                                     class="my-1 p-3 border-r-4 col-2 sm:col-span-8">
                                    <span class="text-xs text-gray-600">{{ error.jDate }}</span>
                                    <p :class="'text-'+error.color+'-900'">{{ error.title }}</p>
                                    <p>{{ error.message }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">بررسی اطلاعات</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات وارد شده مشتری را بررسی نمایید.
                            </p>
                            <p class="mt-1 text-sm text-gray-600 text-justify">
                                در صورتی که اطلاعات ثبت شده به درستی وارد شده اند با کلیک بر روی گزینه
                                <JetButton class="bg-red-600 hover:bg-red-800">ثبت نهایی</JetButton>
                                درخواست
                                میتوانید درخواست خود را ثبت نمایید.
                            </p>
                            <p class="mt-1 text-sm text-gray-600 text-justify">
                                در غیر اینصورت اطلاعات مشتری برای بررسی بیشتر و تکمیل اطلاعات به صورت موقت ذخیره خواهند
                                شد.
                            </p>
                            <p class="mt-1 text-sm text-gray-600 text-justify">
                                از طریق قسمت بارگزاری مدارک می توانید تمامی مدارکی که از طرف پذیرنده ارسال نموده اید را
                                بررسی و مدارک مورد نیاز باقیمانده را ارسال نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div v-if="profileForm.hasErrors()"
                             class="m-2 p-3 bg-red-300 border-r-4 border-red-600 text-red-900">لطفا خطاهای پیش آمده را
                            بررسی نمایید.
                        </div>
                        <p v-for="(error,index) in errors" :key="index"
                           class="m-2 p-3 bg-red-300 border-r-4 border-red-600 text-red-900">
                            {{ error }}
                        </p>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            مشخصات پرونده</p>
                                        <p v-show="$page.message" class="bg-green-300 rounded mx-2 my-3 p-3">
                                            {{ $page.message }}</p>
                                        <jet-button :class="{'bg-blue-600':profileTypeForm.type==='REGISTER'}"
                                                    @click.native="profileTypeForm.type='REGISTER'"
                                                    class="bg-blue-300 hover:bg-blue-400 active:bg-blue-800"
                                                    :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                            ثبت پرونده
                                            جدید
                                        </jet-button>
                                        <jet-button :class="{'bg-red-600':profileTypeForm.type==='EDIT'}"
                                                    @click.native="profileTypeForm.type='EDIT'"
                                                    class="bg-red-300 hover:bg-red-400 active:bg-red-800"
                                                    :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                            تغییر مشخصات پرونده
                                        </jet-button>
                                        <jet-button :class="{'bg-green-600':profileTypeForm.type==='TRANSFER'}"
                                                    @click.native="profileTypeForm.type='TRANSFER'"
                                                    class="bg-green-300 hover:bg-green-400 active:bg-green-800"
                                                    :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                            ثبت
                                            تغییر مالکیت
                                        </jet-button>
                                        <p v-if="profileTypeForm.type==='TRANSFER'" class="text-lg mt-3">اطلاعات پذیرنده
                                            قبلی:</p>
                                        <div v-show="profileTypeForm.type==='TRANSFER'" class="grid grid-cols-2 gap-3">
                                            <div>
                                                <div>
                                                    <jet-label>نام</jet-label>
                                                    <jet-input tye="text"
                                                               class="block w-full mt-3"
                                                               name="previous_name"
                                                               ref="previous_name"
                                                               id="previous_name"
                                                               :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                               v-model="profileTypeForm.previous_name"/>
                                                    <jet-input-error
                                                        :message="profileTypeForm.error('previous_name')"
                                                        class="mt-2"/>
                                                </div>
                                                <div>
                                                    <jet-label>کد ملی</jet-label>
                                                    <jet-input tye="text"
                                                               class="block w-full mt-3"
                                                               name="previous_national_code"
                                                               ref="previous_national_code"
                                                               id="previous_national_code"
                                                               :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                               v-model="profileTypeForm.previous_national_code"/>
                                                    <jet-input-error
                                                        :message="profileTypeForm.error('previous_national_code')"
                                                        class="mt-2"/>
                                                </div>
                                                <div>
                                                    <jet-label>تلفن همراه</jet-label>
                                                    <jet-input tye="text"
                                                               class="block w-full mt-3"
                                                               name="previous_mobile"
                                                               ref="previous_mobile"
                                                               id="previous_mobile"
                                                               :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                               v-model="profileTypeForm.previous_mobile"/>
                                                    <jet-input-error
                                                        :message="profileTypeForm.error('previous_mobile')"
                                                        class="mt-2"/>
                                                </div>
                                                <div>
                                                    <p class="text-red-700 font-bold">حتما پس از ثبت اطلاعات، پرونده را
                                                        ثبت
                                                        نهایی نمایید.</p>
                                                </div>
                                            </div>
                                            <div>
                                                <div>
                                                    <div
                                                        class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                        <div class="space-y-1 text-center">
                                                            <svg v-if="imageFiles.transferFilePreview===''"
                                                                 class="mx-auto h-12 w-12 text-gray-400"
                                                                 stroke="currentColor"
                                                                 fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                                <path
                                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round"/>
                                                            </svg>
                                                            <img v-else :src="imageFiles.transferFilePreview">
                                                            <div class="flex text-sm text-gray-600">
                                                                <label for="transfer_file"
                                                                       class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                    <span>انتخاب فایل</span>
                                                                    <input id="transfer_file"
                                                                           name="transfer_file"
                                                                           type="file"
                                                                           :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                                           @change="onTransferFileChange"
                                                                           class="sr-only">
                                                                </label>
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                تصویر فرم انتقال مالکیت
                                                            </p>
                                                            <jet-input-error
                                                                :message="profileTypeForm.error('transfer_file')"
                                                                class="mt-2"/>
                                                            <jet-input-error
                                                                :message="fileUploadErrors.transfer_file"
                                                                class="mt-2"/>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                        <div class="space-y-1 text-center">
                                                            <svg v-if="imageFiles.transferPaymentFilePreview===''"
                                                                 class="mx-auto h-12 w-12 text-gray-400"
                                                                 stroke="currentColor"
                                                                 fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                                <path
                                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round"/>
                                                            </svg>
                                                            <img v-else :src="imageFiles.transferPaymentFilePreview">
                                                            <div class="flex text-sm text-gray-600">
                                                                <label for="transfer_payment_file"
                                                                       class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                    <span>انتخاب فایل</span>
                                                                    <input id="transfer_payment_file"
                                                                           name="transfer_payment_file"
                                                                           type="file"
                                                                           :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                                           @change="onTransferPaymentFileChange"
                                                                           class="sr-only">
                                                                </label>
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                تصویر رسید پرداخت وجه
                                                            </p>
                                                            <jet-input-error
                                                                :message="profileTypeForm.error('transfer_payment_file')"
                                                                class="mt-2"/>
                                                            <jet-input-error
                                                                :message="fileUploadErrors.transfer_payment_file"
                                                                class="mt-2"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-left">
                                            <jet-button @click.native="submitProfileTypeForm"
                                                        :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                                ذخیره
                                            </jet-button>
                                        </div>
                                    </div>

                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            اطلاعات مشتری</p>
                                        <div v-if="profile.customer.type==='ORGANIZATION'"
                                             class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-2">نام شرکت</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.company_name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام انگلیسی شرکت</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.company_name_english }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام تجاری</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.business_name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ ثبت</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.jRegDate }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شماره ثبت</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.reg_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شناسه ملی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.company_national_code }}
                                            </div>
                                            <div class="col-1 text-center text-indigo-600 sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.asasnamehUrl">
                                                    <img :src="profile.customer.asasnamehUrl" class="w-full">
                                                    تصویر اساسنامه
                                                </a>
                                            </div>
                                            <div class="col-1 text-center text-indigo-600  sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.agahi1Url">
                                                    <img :src="profile.customer.agahi1Url" class="w-full">
                                                    تصویر آگهی ثبت
                                                </a>
                                            </div>
                                            <div class="col-1 text-center text-indigo-600  sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.agahi2Url">
                                                    <img :src="profile.customer.agahi2Url" class="w-full">
                                                    تصویر آگهی تغییرات
                                                </a>
                                            </div>
                                        </div>
                                        <div v-if="profile.customer.type==='ORGANIZATION'" class="col-1 sm:col-span-8">
                                            <jet-section-border></jet-section-border>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-8 gap-y-6">
                                            <div class="col-1 sm:col-span-2">نام</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.first_name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام انگلیسی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.first_name_english }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام خانوادگی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.last_name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام خانوادگی انگلیسی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.last_name_english }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام پدر</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.father }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام پدر انگلیسی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.father_english }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">کد ملی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.customer.national_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شماره شناسنامه</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.id_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ تولد</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.jBirthday }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">جنسیت</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ profile.customer.genderText }}
                                            </div>
                                            <div class="col-1 sm:col-span-8">
                                                <jet-section-border></jet-section-border>
                                            </div>
                                            <div
                                                class="col-1 text-center text-indigo-600 sm:col-span-2 mx-1 hover:text-indigo-400">
                                                <a target="_blank" :href="profile.customer.nationalCard1Url">
                                                    <img :src="profile.customer.nationalCard1Url"
                                                         class="w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر روی کارت ملی
                                                </a>
                                            </div>
                                            <div
                                                class="col-1 text-center text-indigo-600 sm:col-span-2 mx-1 hover:text-indigo-400">
                                                <a target="_blank" :href="profile.customer.nationalCard2Url">
                                                    <img :src="profile.customer.nationalCard2Url"
                                                         class="w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر پشت کارت ملی
                                                </a>
                                            </div>
                                            <div
                                                class="col-1 text-center text-indigo-600 sm:col-span-2 mx-1 hover:text-indigo-400">
                                                <a target="_blank" :href="profile.customer.idCardUrl">
                                                    <img :src="profile.customer.idCardUrl"
                                                         class="w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر شناسنامه
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            اطلاعات کسب و کار</p>
                                        <div class="grid  grid-cols-2 md:grid-cols-8 gap-y-6">
                                            <div class="col-1 sm:col-span-1">استان</div>
                                            <div class="col-1 sm:col-span-1 font-bold">{{
                                                    profile.business &&
                                                    profile.business.ostan
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-1">شهرستان</div>
                                            <div class="col-1 sm:col-span-1 font-bold">{{
                                                    profile.business &&
                                                    profile.business.shahrestan
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-1">بخش</div>
                                            <div class="col-1 sm:col-span-1 font-bold">{{
                                                    profile.business &&
                                                    profile.business.bakhsh
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-1">شهر</div>
                                            <div class="col-1 sm:col-span-1 font-bold">{{
                                                    profile.business &&
                                                    profile.business.shahr
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام</div>
                                            <div class="col-2 sm:col-span-2 font-bold">{{
                                                    profile.business &&
                                                    profile.business.name
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">نام انگلیسی</div>
                                            <div class="col-2 sm:col-span-2 font-bold">
                                                {{ profile.business && profile.business.name_english }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">صنف مرتبط</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{
                                                    profile.business &&
                                                    profile.business.senf
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تلفن تماس</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{
                                                    profile.business &&
                                                    profile.business.fullPhone
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">آدرس</div>
                                            <div class="col-5 sm:col-span-6 font-bold">{{
                                                    profile.business &&
                                                    profile.business.address
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">کد پستی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.business && profile.business.postal_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">جواز کسب</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{
                                                    profile.business && profile.business.has_license == 'YES' ? 'دارد' :
                                                        'ندارد'
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شماره جواز</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.business && profile.business.license_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ جواز</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.business && profile.business.jLicenseDate }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">کد مالیاتی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.business && profile.business.tax_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-8">
                                                <jet-section-border></jet-section-border>
                                            </div>
                                            <div v-if="profile.business && profile.business.has_license==='YES'"
                                                 class="col-1 text-center text-indigo-600 sm:col-span-2 hover:text-indigo-400">
                                                <a target="_blank" :href="profile.business.licenseFile">
                                                    <img :src="profile.business.licenseFile"
                                                         class="w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر جواز کسب
                                                </a>
                                            </div>
                                            <div v-else
                                                 class="col-1 text-center text-indigo-600  sm:col-span-2 hover:text-indigo-400">
                                                <a target="_blank"
                                                   :href="profile.business && profile.business.esteshhadFile">
                                                    <img :src="profile.business && profile.business.esteshhadFile"
                                                         class="w-full rounded border-indigo-600 hover:border-indigo-400">
                                                    تصویر استشهادنامه
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            اطلاعات حساب های بانکی</p>
                                        <div v-for="account in profile.accounts"
                                             class="grid grid-cols-2 md:grid-cols-8 gap-y-6">
                                            <div class="col-1 sm:col-span-2">بانک</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ account.account.bank.name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">کد شعبه</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ account.account.branch }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شماره حساب</div>
                                            <div class="col-3 sm:col-span-6 font-bold">
                                                {{ account.account.account_number }}
                                            </div>
                                            <div class="col-span-2 sm:col-span-2">شماره شبا</div>
                                            <div class="col-span-2 sm:col-span-6 font-bold">
                                                {{ account.account.shebaText }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">صاحب حساب</div>
                                            <div class="col-2 sm:col-span-2 font-bold">{{ account.account.fullName }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">کدملی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ account.account.national_code }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ تولد</div>
                                            <div class="col-2 sm:col-span-2 font-bold">{{ account.account.jBirthday }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">شماره موبایل</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{ account.account.mobile }}
                                            </div>
                                            <div class="col-1 sm:col-span-8">
                                                <jet-section-border></jet-section-border>
                                            </div>
                                        </div>
                                        <div v-for="account in profile.accounts"
                                             class="grid grid-cols-2 md:grid-cols-8 gap-y-6">
                                            <div class="col-1 sm:col-span-2 text-center text-indigo-600">
                                                <a target="_blank" :href="account.account.shebaFile">
                                                    <img :src="account.account.shebaFile" class="w-full">
                                                    تصویر تاییدیه شبا
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            اطلاعات دستگاه</p>
                                        <div class="grid  grid-cols-2 md:grid-cols-8 gap-y-6">
                                            <div class="col-1 sm:col-span-2">نوع ارتباط</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.device_type.type.name }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">مدل دستگاه</div>
                                            <div class="col-1 sm:col-span-2 font-bold">
                                                <template
                                                    v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                                    <select class="form-input rounded-md shadow-sm block w-full pr-6"
                                                            name="device_type_id"
                                                            v-model="serialForm.device_type_id">
                                                        <option v-for="type in deviceTypes" :key="type.id"
                                                                :value="type.id">{{ type.name }}
                                                        </option>
                                                    </select>
                                                    <jet-input-error :message="serialForm.error('device_type_id')"
                                                                     class="font-normal mt-2"/>
                                                </template>
                                                <template v-else>{{ profile.device_type.name }}</template>
                                            </div>
                                            <div class="col-1 sm:col-span-2">سریال دستگاه</div>
                                            <template
                                                v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                                <div class="col-1 sm:col-span-3 font-bold">
                                                    <jet-input name="device_serial"
                                                               id="device_serial"
                                                               class="block w-full"
                                                               v-model="serialForm.serial"/>
                                                    <jet-input-error :message="serialForm.error('serial')"
                                                                     class="font-normal mt-2"/>
                                                </div>
                                                <div class="col-span-2 sm:col-span-3 font-bold">
                                                    <jet-button :disable="submitSerialFormLoading"
                                                                class="block bg-green-500 hover:bg-green-400 mx-auto"
                                                                @click.native="submitSerial">
                                                        <div v-if="submitSerialFormLoading"
                                                             class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-5 w-5 mx-1"></div>
                                                        ذخیره تغییرات
                                                    </jet-button>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="col-1 sm:col-span-6 font-bold">
                                                    {{ profile.device ? profile.device.serial : 'تخصیص نیافته' }}
                                                </div>
                                            </template>
                                            <div class="col-1 sm:col-span-2">تاریخ شروع گارانتی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{
                                                    profile.device ?
                                                        profile.device.guarantee_start : 'ثبت نشده'
                                                }}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ پایان گارانتی</div>
                                            <div class="col-1 sm:col-span-2 font-bold">{{
                                                    profile.device ?
                                                        profile.device.guarantee_end : 'ثبت نشده'
                                                }}
                                            </div>
                                            <div class="sm:col-span-8 border-b border-gray-200"></div>
                                            <div class="sm:col-span-2">فیزیک دستگاه</div>
                                            <div class="sm:col-span-2 font-bold">
                                                {{ profile.devicePhysicalStatusText }}
                                            </div>
                                            <div class="sm:col-span-2">نحوه فروش</div>
                                            <div class="sm:col-span-2 font-bold">{{ profile.deviceSellTypeText }}</div>
                                            <div class="sm:col-span-2">مبلغ فروش / امانت / اقساط</div>
                                            <div class="sm:col-span-2 font-bold">{{ profile.device_amount }}</div>
                                            <div class="sm:col-span-2">شماره پرونده اقساط</div>
                                            <div class="sm:col-span-2 font-bold">{{ profile.device_dept_profile_id }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            اطلاعات شاپرک</p>
                                        <div class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 self-center sm:col-span-2">شرکت ارائه دهنده (PSP)</div>
                                            <div class="col-1 sm:col-span-6 font-bold">
                                                {{ profile.psp ? profile.psp.name : 'نامشخص' }}
                                            </div>
                                            <div class="col-1 self-center sm:col-span-2">شماره پایانه</div>
                                            <div v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                                 class="col-1 sm:col-span-2 font-bold">
                                                <jet-input name="terminal_id"
                                                           id="terminal_id"
                                                           class="block w-full"
                                                           v-model="terminalForm.terminal_id"/>
                                                <jet-input-error :message="terminalForm.error('terminal_id')"
                                                                 class="font-normal mt-2"/>
                                            </div>
                                            <div v-else class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.terminal_id ? profile.terminal_id : 'تخصیص نیافته' }}
                                            </div>
                                            <div class="col-1 self-center sm:col-span-2">شماره پذیرنده</div>
                                            <div v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                                 class="col-1 sm:col-span-2 font-bold">
                                                <jet-input name="merchant_id"
                                                           id="merchant_id"
                                                           class="block w-full"
                                                           v-model="terminalForm.merchant_id"/>
                                                <jet-input-error :message="terminalForm.error('merchant_id')"
                                                                 class="font-normal mt-2"/>
                                            </div>
                                            <div v-else class="col-1 sm:col-span-2 font-bold">
                                                {{ profile.merchant_id ? profile.merchant_id : 'تخصیص نیافته' }}
                                            </div>
                                            <div class="col-span-2 sm:col-span-8 text-left">
                                                <jet-button :disable="submitTerminalFormLoading"
                                                            @click.native="submitTerminal">
                                                    <div v-if="submitTerminalFormLoading"
                                                         class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-5 w-5 mx-1"></div>
                                                    ذخیره تغییرات
                                                </jet-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-8 gap-2">
                                    <div class="col-span-2 sm:col-span-8">
                                        <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                            مدارک پرونده</p>
                                    </div>
                                    <div class="col-span-8 m-3 rounded border border-gray-600 py-1 px-3">
                                        <p class="text-lg py-1">وضعیت مدارک:
                                            <span class="rounded py-1 px-2"
                                            :class="licensesStatusColor(profile.licenses_status)">
                                            {{profile.licensesStatusText }}
                                        </span>
                                        </p>
                                        {{ profile.licenses_message }}
                                    </div>
                                    <div v-for="license in profile.licenses" :key="license.id"
                                         class="text-center text-indigo-600 sm:col-span-2 relative">
                                        <a target="_blank" :href="license.url">
                                            <img :src="license.url" class="w-full">
                                            {{ license.type.name }}
                                        </a>
                                        <InertiaLink
                                            v-if="canEditLicenses"
                                            :href="route('dashboard.profiles.licenses.destroy',{profileId:profile.id,licenseId:license.id})"
                                            method="DELETE"
                                            class="block text-red-600 hover:text-red-400">حذف این تصویر
                                        </InertiaLink>
                                    </div>
                                    <div
                                        v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'"
                                        class="sm:col-span-8 text-left flex my-2">
                                        <a :href="route('dashboard.profiles.licenses.downloadZipArchive',{profileId:profile.id})"
                                           class="ml-auto"
                                           target="_blank">
                                            <jet-button class="bg-blue-500 hover:bg-blue-400">
                                                <i class="material-icons">file_download</i>
                                                دریافت همه مدارک به صورت یکجا
                                            </jet-button>
                                        </a>
                                        <jet-button class="bg-red-500 hover:bg-red-400 ml-1"
                                                    @click.native="changeLicensesStatus(3)"
                                                    v-if="profile.licenses_status===0 || profile.licenses_status===1"><i
                                            class="material-icons">do_not_disturb_on</i>رد
                                            مدارک
                                        </jet-button>
                                        <jet-button class="bg-yellow-500 hover:bg-yellow-400"
                                                    @click.native="changeLicensesStatus(1)"
                                                    v-if="profile.licenses_status===0 || profile.licenses_status===3"><span
                                            class="material-icons-outlined">inventory</span>تایید
                                            موقت مدارک
                                        </jet-button>
                                        <jet-button class="bg-green-500 hover:bg-green-400"
                                                    @click.native="changeLicensesStatus(2)"
                                                    v-else-if="profile.licenses_status===1 || profile.licenses_status===3"><span
                                            class="material-icons-outlined">inventory</span>تایید
                                            نهایی مدارک
                                        </jet-button>
                                    </div>
                                    <template v-if="canEditLicenses">
                                        <div
                                            class="col-span-2 sm:col-span-8">
                                            <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                                ارسال مدارک
                                            </p>
                                        </div>
                                        <div
                                            class="col-span-2 sm:col-span-5">
                                            <div>
                                                <label for="license_type_id">نوع مدرک</label>
                                                <select name="license_type_id"
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                        id="license_type_id"
                                                        v-model="uploadLicenseForm.license_type_id">
                                                    <option v-for="type in licenseTypes" :key="type.id"
                                                            :value="type.id">
                                                        {{ type.name }}
                                                    </option>
                                                </select>
                                                <p class="text-sm text-red-400">چنانچه مدرکی را قبلا ارسال نموده اید با
                                                    ارسال
                                                    مجدد تصویر جدید جایگزین تصویر قبلی می شود.</p>
                                                <jet-input-error
                                                    :message="uploadLicenseForm.error('license_type_id')"
                                                    class="mt-2"/>
                                            </div>
                                            <div>
                                                <label for="account_id">حساب بانکی</label>
                                                <select name="account_id"
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                        id="account_id"
                                                        v-model="uploadLicenseForm.account_id">
                                                    <option v-for="account in profile.accounts" :key="account.id"
                                                            :value="account.account_id">
                                                        {{ account.account.bank.name }} -
                                                        {{ account.account.account_number }}
                                                    </option>
                                                </select>
                                                <p class="text-sm text-red-500">چنانچه مدرک ارسالی، تصویر تاییدیه شبا می
                                                    باشد حتما شماره حساب مرتبط با آن را انتخاب نمایید.</p>
                                                <jet-input-error
                                                    :message="uploadLicenseForm.error('account_id')"
                                                    class="mt-2"/>
                                            </div>
                                        </div>
                                        <div
                                            class="col-span-2 sm:col-span-3">
                                            <div
                                                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <svg v-if="filePreview===''"
                                                         class="mx-auto h-12 w-12 text-gray-400"
                                                         stroke="currentColor"
                                                         fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                        <path
                                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"/>
                                                    </svg>
                                                    <img v-else :src="filePreview">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="file"
                                                               class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>انتخاب فایل</span>
                                                            <input id="file"
                                                                   name="file"
                                                                   type="file"
                                                                   @change="onLicenseFileChange($event)"
                                                                   class="sr-only">
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        فایل مدرک
                                                    </p>

                                                    <jet-input-error
                                                        :message="uploadLicenseForm.error('file')"
                                                        class="mt-2"/>
                                                    <jet-input-error
                                                        :message="fileUploadError"
                                                        class="mt-2"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-span-2 sm:col-span-8 text-left">
                                            <jet-button @click.native="submitLicenseFile">ارسال فایل</jet-button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <div class="col-6 sm:col-span-6 text-left">
                                            <template
                                                v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                                <label for="change_status">تغییر وضعیت پرونده</label>
                                                <select name="change_status"
                                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 inline-block sm:text-sm border-gray-300 rounded-md border"
                                                        id="change_status"
                                                        rel="change_status"
                                                        v-model="newProfileStatus">
                                                    <option v-for="status in statuses" :key="status.id"
                                                            :value="status.id">
                                                        {{ status.name }}
                                                    </option>
                                                </select>
                                                <JetButton @click.native="changeProfileStatus">تغییر</JetButton>
                                                <JetSectionBorder></JetSectionBorder>
                                            </template>
                                            <!-- ثبت پرونده توسط بازاریاب -->
                                            <JetButton @click.native="updateProfileInfo(1)"
                                                       v-if="profile.status===0"
                                                       class="bg-red-600 hover:bg-red-800"
                                                       :disabled="submitProfileFormLoading">
                                                <div v-if="submitProfileFormLoading"
                                                     class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-5 w-5 mx-1"></div>
                                                ثبت نهایی
                                            </JetButton>
                                            <!-- تغییر وضعیت پرونده توسط مدیر به تایید شده یا برگشت پرونده -->
                                            <JetButton @click.native="openErrorModal(10)"
                                                       v-if="profile.status===2 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                       class="bg-red-600 hover:bg-red-800">
                                                عدم تایید به علت نقص مدارک
                                            </JetButton>
                                            <JetButton @click.native="updateProfileInfo(3)"
                                                       v-if="profile.status===2 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                       class="bg-green-600 hover:bg-green-800">
                                                تایید مدارک
                                            </JetButton>
                                            <!-- تغییر وضعیت پرونده توسط مدیر به تایید شده توسط شاپرک یا ردشده توسط شاپرک -->
                                            <JetButton @click.native="openErrorModal(11)"
                                                       v-if="profile.status===4 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                       class="bg-red-600 hover:bg-red-800">
                                                عدم تایید شاپرک
                                            </JetButton>
                                            <JetButton @click.native="updateProfileInfo(5)"
                                                       v-if="profile.status===4 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                       class="bg-indigo-600 hover:bg-indigo-800">
                                                تایید توسط شاپرک
                                            </JetButton>
                                            <!-- تغییر وضعیت پرونده توسط بازاریاب جهت بررسی مجدد -->
                                            <JetButton @click.native="updateProfileInfo(1)"
                                                       v-if="profile.status===10 || profile.status===11"
                                                       class="bg-yellow-600 hover:bg-yellow-800">ارسال جهت بررسی مجدد
                                            </JetButton>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <jet-confirmation-modal :show="viewErrorModal" @close="viewErrorModal = false">
                <template #title>ثبت دلیل عدم تایید</template>
                <template #content>
                    <div class="mt-2">
                                            <textarea v-model="error.message"
                                                      placeholder="متن خطا"
                                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"></textarea>
                        <jet-input-error
                            :message="profileForm.error('message')"
                            class="mt-2"/>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewErrorModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="updateProfileInfo(temporaryStatus)"
                                :class="{ 'opacity-25': profileForm.processing }"
                                :disabled="profileForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <jet-confirmation-modal :show="viewChangeLicensesStatusModal" @close="closeChangeLicensesStatusModal">
                <template #title>تغییر وضعیت مدارک</template>
                <template #content>
                    <div class="mt-2">
                        وضعیت جدید: {{ licensesStatusLabels[licensesStatusForm.status] }}
                    </div>
                    <div class="mt-2">
                                            <textarea v-model="licensesStatusForm.message"
                                                      placeholder="توضیحات"
                                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"></textarea>
                        <jet-input-error
                            :message="licensesStatusForm.error('message')"
                            class="mt-2"/>
                    </div>
                    <div class="mt-2 text-white bg-red-400 rounded my-1 mx-2 py-1 px-2">
                        با تایید این گزینه وضعیت تمامی مدارک پرونده به وضعیت انتخاب شده تغییر خواهد کرد.
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeChangeLicensesStatusModal">
                        انصراف
                    </jet-secondary-button>

                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitLicensesStatusForm"
                                :class="{ 'opacity-25': licensesStatusForm.processing }"
                                :disabled="licensesStatusForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>

        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
import JetButton from '@/Jetstream/Button';
import JetInput from '@/Jetstream/Input';
import JetLabel from '@/Jetstream/Label';
import JetInputError from '@/Jetstream/InputError';
import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
import JetDangerButton from '@/Jetstream/DangerButton';
import JetSecondaryButton from '@/Jetstream/SecondaryButton';
import JetSectionBorder from '@/Jetstream/SectionBorder'
import {Inertia} from "@inertiajs/inertia";

export default {
    name: "CreateProfile",
    components: {
        Dashboard,
        ProfileSteps,
        JetButton,
        JetInput,
        JetLabel,
        JetInputError,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
        JetSectionBorder
    },
    props: {
        errors: Object,
        profile: Object,
        psps: Array,
        statuses: Array,
        licenseTypes: Array,
        deviceTypes: Array,
    },
    data() {
        return {
            newProfileStatus: this.profile.status,
            search: {
                serial: '',
                results: []
            },
            error: {
                title: '',
                message: '',
                type: ''
            },
            temporaryStatus: '',
            viewSearchModal: false,
            viewErrorModal: false,
            imageFiles: {
                transferFilePreview: this.profile.transferFileUrl,
                transferPaymentFilePreview: this.profile.transferPaymentFileUrl,
            },
            fileUploadErrors: {transfer_file: ''},
            profileTypeForm: this.$inertia.form({
                '_method': 'PUT',
                type: this.profile.type,
                previous_name: this.profile.previous_name,
                previous_national_code: this.profile.previous_national_code,
                previous_mobile: this.profile.previous_mobile,
                transfer_file: '',
                transfer_payment_file: '',
            }, {
                bag: 'profileTypeForm',
                resetOnSuccess: false
            }),
            profileForm: this.$inertia.form({
                '_method': 'PUT',
                terminal_id: '',
                merchant_id: '',
                status: '',
                message: '',
                title: '',
            }, {
                bag: 'profileForm',
                resetOnSuccess: true
            }),
            submitProfileFormLoading: false,

            terminalForm: this.$inertia.form({
                '_method': 'PUT',
                terminal_id: this.profile.terminal_id,
                merchant_id: this.profile.merchant_id,
            }, {
                bag: 'terminalForm',
                resetOnSuccess: false,
            }),
            submitTerminalFormLoading: false,

            serialForm: this.$inertia.form({
                '_method': 'PUT',
                serial: this.profile.device ? this.profile.device.serial : '',
                device_type_id: this.profile.device_type_id,
            }, {
                bag: 'serialForm',
                resetOnSuccess: false,
            }),
            submitSerialFormLoading: false,

            filePreview: '',
            fileUploadError: '',
            uploadLicenseForm: this.$inertia.form({
                '_method': 'POST',
                license_type_id: '',
                account_id: '',
                file: '',
            }, {
                bag: 'uploadLicenseForm',
                resetOnSuccess: true,
            }),

            viewChangeLicensesStatusModal: false,
            licensesStatusLabels: [
                'بررسی نشده', 'تایید موقت', 'تایید نهایی', 'رد شده'
            ],
            licensesStatusForm: this.$inertia.form({
                '_method': 'PUT',
                message: null,
                status: null,
            }, {
                bag: 'licensesStatusForm',
                resetOnSuccess: true,
            }),
        }
    },
    mounted() {
        this.profile.messages.reverse();
    },
    methods: {
        changeLicensesStatus(status) {
            this.licensesStatusForm.status = status;
            this.licensesStatusForm.message = this.profile.licenses_status_message;
            this.viewChangeLicensesStatusModal = true;
        },
        closeChangeLicensesStatusModal() {
            this.licensesStatusForm.status = null;
            // this.licensesStatusForm.message = this.profile.licenses_status_message;
            this.viewChangeLicensesStatusModal = false;
        },
        submitLicensesStatusForm() {
            this.licensesStatusForm.put(route('dashboard.profiles.confirm.licenses', {profileId: this.profile.id})).then(() => {
                if (!this.licensesStatusForm.hasErrors()) {
                    this.closeChangeLicensesStatusModal();
                }
            })
        },
        licensesStatusColor(status) {
            switch (status) {
                default:
                case 0:
                    return 'text-gray-800 bg-gray-200';
                case 1:
                    return 'text-yellow-800 bg-yellow-200';
                case 2:
                    return 'text-green-800 bg-green-200';
                case 3:
                    return 'text-red-800 bg-red-200';
            }
        },
        openErrorModal(status) {
            this.viewErrorModal = true;
            this.temporaryStatus = status
        },
        changeProfileStatus() {
            Inertia.visit(route('dashboard.profiles.update.status', {profileId: this.profile.id}), {
                method: 'put',
                data: {
                    newStatus: this.newProfileStatus,
                },
            })
        },
        updateProfileInfo(status) {
            this.submitProfileFormLoading = true;
            if (this.profile.device) {
                this.profileForm.device_id = this.profile.device_id;
            }
            if (status !== 3) {
                delete this.profileForm.device_id;
            }
            //اگر پرونده برگشت خورده است و در حال ارسال مجدد می باشد.
            if (status !== 7) {
                delete this.profileForm.terminal_id;
                delete this.profileForm.merchant_id;
            }
            //اگر پرونده برگشت خورده است و در حال ارسال مجدد می باشد.
            if (this.profile.status !== 0) {
                delete this.profileForm.psp_id;
            }
            this.profileForm.status = status;
            //اگر پرونده در حال برگشت خوردن باشد
            if (status === 10 || status === 11) {
                this.profileForm.title = this.error.title;
                this.profileForm.message = this.error.message;
                this.profileForm.status = this.temporaryStatus;
            }
            //اگر پرونده برگشت خورده است و در حال ارسال مجدد می باشد.
            if (this.profile.status === 10 || this.profile.status === 11) {
                this.profileForm.message = 'ارسال مجدد پرونده جهت بررسی';
            }
            this.profileForm.post(route('dashboard.profiles.update', {profileId: this.profile.id})).then(response => {
                if (!this.profileForm.hasErrors()) {
                    this.viewErrorModal = false;
                }
                this.submitProfileFormLoading = false;
                this.profile.messages.reverse();
            })
        },
        submitTerminal() {
            this.submitTerminalFormLoading = true;
            this.terminalForm.post(route('dashboard.profiles.update.terminal', {
                profileId: this.profile.id,
                byAdmin: true
            }), {
                preserveScroll: true
            })
                .then(response => {
                    if (!this.terminalForm.hasErrors()) {

                    }
                    this.submitTerminalFormLoading = false;
                })
        },
        submitSerial() {
            this.submitSerialFormLoading = true;
            this.serialForm.post(route('dashboard.profiles.update.serial', {
                profileId: this.profile.id,
                byAdmin: true
            }), {
                preserveScroll: true
            })
                .then(response => {
                    if (!this.serialForm.hasErrors()) {

                    }
                    this.submitSerialFormLoading = false;
                })
        },
        onTransferFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadErrors.transfer_file = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadErrors.transfer_file = '';
            this.profileTypeForm.transfer_file = e.target.files[0];
            this.imageFiles.transferFilePreview = URL.createObjectURL(file);
        },
        onTransferPaymentFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadErrors.transfer_file = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadErrors.transfer_payment_file = '';
            this.profileTypeForm.transfer_payment_file = e.target.files[0];
            this.imageFiles.transferPaymentFilePreview = URL.createObjectURL(file);
        },
        submitProfileTypeForm() {
            this.profileTypeForm.post(route('dashboard.profiles.update.type', {profileId: this.profile.id}), {
                preserveScroll: false
            });
        },
        onLicenseFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadError = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadError = '';
            this.uploadLicenseForm.file = e.target.files[0];
            this.filePreview = URL.createObjectURL(file);
        },
        submitLicenseFile() {
            this.fileUploadError = '';
            this.filePreview = '';
            this.uploadLicenseForm.post(route('dashboard.profiles.licenses.store', {profileId: this.profile.id}), {
                preserveScroll: true
            })
                .then(response => {

                })
        }
    },
    computed: {
        canEditLicenses() {
            return (this.profile.status == 0 || this.profile.status == 10 || this.profile.status == 11) || this.$page.user.level === 'ADMIN' || this.$page.user.level === 'OFFICE' || this.$page.user.level === 'SUPERUSER'
        }
    }
}
</script>

<style scoped>

</style>
