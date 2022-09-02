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
                        <div class="px-4 py-5 bg-white sm:p-6">
                            <div class="lg:hidden flex flex-col items-start justify-start">
                                <div @click="viewTab('general')"
                                     :class="{'font-bold bg-gray-200':currentTab==='merchant'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    اطلاعات کلی
                                </div>
                                <div @click="viewTab('merchant')"
                                     :class="{'font-bold bg-gray-200':currentTab==='merchant'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    مشخصات پذیرنده
                                </div>
                                <div @click="viewTab('business')"
                                     :class="{'font-bold bg-gray-200':currentTab==='business'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    کسب و کار
                                </div>
                                <div @click="viewTab('accounts')"
                                     :class="{'font-bold bg-gray-200':currentTab==='accounts'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    حساب های بانکی
                                </div>
                                <div @click="viewTab('terminals')"
                                     :class="{'font-bold bg-gray-200':currentTab==='terminals'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    ترمینال‌ها
                                </div>
                                <div @click="viewTab('licenses')"
                                     :class="{'font-bold bg-gray-200':currentTab==='licenses'}"
                                     class="w-full border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    مدارک پرونده
                                </div>
                            </div>
                            <div class="hidden lg:flex items-center justify-start">
                                <div @click="viewTab('general')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='general'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    اطلاعات کلی
                                </div>
                                <div @click="viewTab('merchant')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='merchant'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    مشخصات پذیرنده
                                </div>
                                <div @click="viewTab('business')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='business'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    کسب و کار
                                </div>
                                <div @click="viewTab('accounts')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='accounts'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    حساب‌های بانکی
                                </div>
                                <div @click="viewTab('terminals')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='terminals'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    ترمینال‌ها
                                </div>
                                <div @click="viewTab('licenses')"
                                     :class="{'font-bold bg-gray-100 border-b-0':currentTab==='licenses'}"
                                     class="border border-gray-200 border-b-0 py-2 px-3 mx-1 hover:bg-gray-200 cursor-pointer">
                                    مدارک پرونده
                                </div>
                            </div>
                            <div class="border border-gray-200 border-t-0 bg-gray-100">
                                <div v-if="currentTab==='general'" class="py-2 px-3" id="general">
                                    <div class="flex flex-col lg:flex-row items-start justify-start">
                                        <div
                                            class="w-full lg:w-2/3 bg-white border border-gray-200 rounded  my-3 mx-1 py-2 px-3">
                                            <div class="grid md:grid-cols-6 gap-6">
                                                <div class="col-6 sm:col-span-6">
                                                    <p v-show="$page.message"
                                                       class="bg-green-300 rounded mx-2 my-3 p-3">
                                                        {{ $page.message }}</p>
                                                    <jet-label value="نوع پرونده"/>
                                                    <jet-button
                                                        :class="{'bg-blue-600':profileTypeForm.type==='REGISTER'}"
                                                        @click.native="profileTypeForm.type='REGISTER'"
                                                        class="w-full lg:w-1/4 mb-1 justify-center bg-blue-300 hover:bg-blue-400 active:bg-blue-800"
                                                        :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                                        ثبت پرونده
                                                        جدید
                                                    </jet-button>
                                                    <jet-button :class="{'bg-red-600':profileTypeForm.type==='EDIT'}"
                                                                @click.native="profileTypeForm.type='EDIT'"
                                                                class="w-full lg:w-1/4 mb-1 justify-center bg-red-300 hover:bg-red-400 active:bg-red-800"
                                                                :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                                        تغییر مشخصات پرونده
                                                    </jet-button>
                                                    <jet-button
                                                        :class="{'bg-green-600':profileTypeForm.type==='TRANSFER'}"
                                                        @click.native="profileTypeForm.type='TRANSFER'"
                                                        class="w-full lg:w-1/4 mb-1 justify-center bg-green-300 hover:bg-green-400 active:bg-green-800"
                                                        :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                                                        ثبت
                                                        تغییر مالکیت
                                                    </jet-button>
                                                    <p v-if="profileTypeForm.type==='TRANSFER'" class="text-lg mt-3">
                                                        اطلاعات پذیرنده
                                                        قبلی:</p>
                                                    <div v-show="profileTypeForm.type==='TRANSFER'"
                                                         class="grid md:grid-cols-2 gap-3">
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
                                                                <p class="text-red-700 font-bold">حتما پس از ثبت
                                                                    اطلاعات، پرونده را
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
                                                                             fill="none" viewBox="0 0 48 48"
                                                                             aria-hidden="true">
                                                                            <path
                                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                                stroke-width="2" stroke-linecap="round"
                                                                                stroke-linejoin="round"/>
                                                                        </svg>
                                                                        <img v-else
                                                                             :src="imageFiles.transferFilePreview">
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
                                                                        <svg
                                                                            v-if="imageFiles.transferPaymentFilePreview===''"
                                                                            class="mx-auto h-12 w-12 text-gray-400"
                                                                            stroke="currentColor"
                                                                            fill="none" viewBox="0 0 48 48"
                                                                            aria-hidden="true">
                                                                            <path
                                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                                stroke-width="2" stroke-linecap="round"
                                                                                stroke-linejoin="round"/>
                                                                        </svg>
                                                                        <img v-else
                                                                             :src="imageFiles.transferPaymentFilePreview">
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
                                            </div>
                                        </div>
                                        <div
                                            class="w-full lg:w-1/3 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                            <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                                                گزارش پرونده</p>
                                            <div class="h-96 overflow-y-auto">
                                                <div v-for="error in profile.messages"
                                                     :class="'border-'+error.color+'-600 bg-'+error.color+'-100'"
                                                     class="my-1 p-3 border-r-4 col-2 sm:col-span-8">
                                                    <span class="text-xs text-gray-600">{{ error.jDate }}</span>
                                                    <p :class="'text-'+error.color+'-900'">{{ error.title }}</p>
                                                    <p>{{ error.message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="currentTab==='merchant'" class="py-2 px-3" id="merchant">
                                    <div v-if="profile.customer.type==='ORGANIZATION'"
                                         class="grid grid-cols-2 md:grid-cols-8 gap-3 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
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
                                    <div
                                        class="grid grid-cols-2 lg:grid-cols-6 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <div class="border-b border-gray-200 pb-1">نام</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.first_name }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام خانوادگی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.customer.last_name
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام پدر</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.customer.father
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام انگلیسی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.first_name_english }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام خانوادگی انگلیسی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.last_name_english }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام پدر انگلیسی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.father_english }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">جنسیت</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.genderText }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">وضعیت حیات</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.vitalText }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تابعیت</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.residencyText }}
                                        </div>
                                        <template v-if="profile.customer.residency==='iranian'">
                                            <div class="border-b border-gray-200 pb-1">کد ملی</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ profile.customer.national_code }}
                                            </div>
                                            <div class="border-b border-gray-200 pb-1">شماره شناسنامه</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                    profile.customer.id_code
                                                }}
                                            </div>
                                            <div class="border-b border-gray-200 pb-1">سریال شناسنامه</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ birthCertificateSeries }}
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="border-b border-gray-200 pb-1">کشور</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ profile.customer.country }}
                                            </div>
                                            <div class="border-b border-gray-200 pb-1">شماره اتباع خارجه</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ profile.customer.foreign_pervasive_code }}
                                            </div>
                                            <div class="border-b border-gray-200 pb-1">شماره پاسپورت</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ profile.customer.passport_number }}
                                            </div>
                                            <div class="border-b border-gray-200 pb-1">تاریخ اعتبار پاسپورت</div>
                                            <div class="border-b border-gray-200 pb-1 font-bold">
                                                {{ profile.customer.jPassportExpireDate }}
                                            </div>
                                        </template>
                                        <div class="border-b border-gray-200 pb-1">تاریخ تولد</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.customer.jBirthday }}
                                        </div>
                                        <div
                                            class="col-span-2 lg:col-span-6 lg:flex flex-col lg:flex-row items-center justify-center lg:space-x-2 lg:space-x-reverse">
                                            <div
                                                class="w-full lg:w-1/4 lg:h-64 text-center text-indigo-600 col-span-2 hover:text-indigo-400 mb-1">
                                                <a target="_blank" :href="profile.customer.nationalCard1Url">
                                                    <img :src="profile.customer.nationalCard1Url"
                                                         class="h-full object-cover w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر روی کارت ملی
                                                </a>
                                            </div>
                                            <div
                                                class="w-full lg:w-1/4  lg:h-64 text-center text-indigo-600 col-span-2  hover:text-indigo-400 mb-1">
                                                <a target="_blank" :href="profile.customer.nationalCard2Url">
                                                    <img :src="profile.customer.nationalCard2Url"
                                                         class="h-full object-cover w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر پشت کارت ملی
                                                </a>
                                            </div>
                                            <div
                                                class="w-full lg:w-1/4 lg:h-64 text-center text-indigo-600 col-span-2  hover:text-indigo-400 mb-1">
                                                <a target="_blank" :href="profile.customer.idCardUrl">
                                                    <img :src="profile.customer.idCardUrl"
                                                         class="h-full object-cover w-full rounded border border-indigo-600 hover:border-indigo-400">
                                                    تصویر شناسنامه
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="currentTab==='business'" class="py-2 px-3" id="business">
                                    <div
                                        class="grid grid-cols-2 lg:grid-cols-6 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <div class="border-b border-gray-200 pb-1">استان</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.business && profile.business.ostan }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شهرستان</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.business &&
                                                profile.business.shahrestan
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">بخش</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.business &&
                                                profile.business.bakhsh
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شهر</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.business &&
                                                profile.business.shahr
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold lg:col-span-2">{{
                                                profile.business &&
                                                profile.business.name
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نام انگلیسی</div>
                                        <div class="border-b border-gray-200 pb-1 lg:col-span-4 font-bold">
                                            {{ profile.business && profile.business.name_english }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">صنف مرتبط</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold lg:col-span-7">
                                            {{ profile.business.subCategory }}
                                            <template v-if="profile.business.category || profile.business.sub_category">
                                                <span v-if="profile.business.category">
                                                {{ profile.business.category.name }}
                                            </span>
                                                ->
                                                <span v-if="profile.business.sub_category">
                                                {{ profile.business.sub_category.name }}
                                            </span>
                                            </template>
                                            <span
                                                v-else-if="profile.business && profile.business.senf">{{
                                                    profile.business.senf
                                                }}
                                                (<inertia-link
                                                    :href="route('dashboard.profiles.businesses.edit',{profile:profile.id})"
                                                    class="text-red-500 font-bold cursor-pointer hover:underline">نیاز به بروزرسانی</inertia-link>)
                                            </span>
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تلفن تماس</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                profile.business &&
                                                profile.business.fullPhone
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">آدرس</div>
                                        <div class="border-b border-gray-200 pb-1 lg:col-span-3 font-bold">{{
                                                profile.business &&
                                                profile.business.address
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">کد پستی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.business && profile.business.postal_code }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">جواز کسب</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{
                                                profile.business && profile.business.has_license == 'YES' ? 'دارد' :
                                                    'ندارد'
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شماره جواز</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.business && profile.business.license_code }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تاریخ جواز</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ profile.business && profile.business.jLicenseDate }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">کد مالیاتی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
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
                                <div v-if="currentTab==='accounts'" class="py-2 px-3" id="accounts">
                                    <div v-for="account in profile.accounts"
                                         class="grid grid-cols-2 lg:grid-cols-6 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <div class="col-span-3 lg:col-span-1 lg:row-span-4 px-4">
                                            <a target="_blank" :href="account.account.shebaFile"
                                               class="text-indigo-500 hover:text-indigo-400">
                                                <img :src="account.account.shebaFile" class="w-full">
                                                تصویر تاییدیه شبا
                                            </a>
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">بانک</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ account.account.bank.name }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">کد شعبه</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                account.account.branch
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شماره حساب</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ account.account.account_number }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شماره شبا</div>
                                        <div class="col-span-3 border-b border-gray-200 pb-1 font-bold">
                                            {{ account.account.shebaText }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">صاحب حساب</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold lg:col-span-2">
                                            {{ account.account.fullName }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">کدملی</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ account.account.national_code }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تاریخ تولد</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">
                                            {{ account.account.jBirthday }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شماره موبایل</div>
                                        <div class="border-b border-gray-200 pb-1 font-bold">{{
                                                account.account.mobile
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="currentTab==='terminals'" class="py-2 px-3" id="terminals">
                                    <div
                                        class="flex flex-col md:flex-row items-center lg:justify-around bg-white border border-gray-200 rounded m-1 py-2 px-3">
                                        <div class="flex items-center lg:justify-around lg:w-1/2">
                                            <div>شرکت ارائه دهنده (PSP):</div>
                                            <div class="font-bold">{{ profile.psp && profile.psp.name }}</div>
                                        </div>
                                        <div class="flex items-center lg:justify-around lg:w-1/2">
                                            <div>شماره پذیرنده:</div>
                                            <div class="font-bold">
                                                <template v-if="profile.merchant_id">
                                                    {{ profile.merchant_id }}
                                                </template>
                                                <template v-else>
                                                    تخصیص نیافته
                                                </template>
                                                <template
                                                    v-if="$page.user.level==='OFFICE' || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                                    <span @click="showMerchantModal"
                                                          class="text-blue-500 hover:text-blue-400 cursor-pointer">(ویرایش)</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-for="(terminal,i) in profile.terminals" :key="terminal.id"
                                         class="grid grid-cols-2 lg:grid-cols-6 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <div class="border-b border-gray-200 pb-1">نوع ترمینال</div>
                                        <div class="font-bold border-b border-gray-200 pb-1">{{
                                                terminal.typeText
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نوع ارتباط</div>
                                        <div class="font-bold border-b border-gray-200 pb-1">
                                            {{
                                                terminal.device_connection_type && terminal.device_connection_type.name
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">شماره پایانه</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            <template v-if="terminal.terminal_number">
                                                {{ terminal.terminal_number }}
                                            </template>
                                            <template v-else>
                                                تخصیص نیافته
                                            </template>
                                            <template
                                                v-if="$page.user.level==='OFFICE' || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                                <span @click="showTerminalModal(terminal)"
                                                      class="text-blue-500 hover:text-blue-400 cursor-pointer">(ویرایش)</span>
                                            </template>
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">مدل دستگاه</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            {{ terminal.device_type && terminal.device_type.name }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">سریال دستگاه</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            <template v-if="terminal.device">
                                                {{ terminal.device.serial }}
                                            </template>
                                            <template v-else>
                                                تخصیص نیافته
                                            </template>
                                            <span
                                                v-if="$page.user.level==='OFFICE' || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                                @click="viewDevicesModal(terminal)"
                                                class="text-blue-500 hover:text-blue-400 cursor-pointer">(ویرایش)</span>
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">مدل نصب شده</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            <template v-if="terminal.device">
                                                {{ terminal.device.device_type && terminal.device.device_type.name }}
                                            </template>
                                            <template v-else>
                                                تخصیص نیافته
                                            </template>
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تاریخ شروع گارانتی</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            {{ terminal.device ? terminal.device.guarantee_start : 'ثبت نشده' }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">تاریخ پایان گارانتی</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            {{ terminal.device ? terminal.device.guarantee_end : 'ثبت نشده' }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">فیزیک دستگاه</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">
                                            {{ terminal.devicePhysicalStatusText }}
                                        </div>
                                        <div class="border-b border-gray-200 pb-1">نحوه فروش</div>
                                        <div class="font-bold border-gray-200 pb-1 border-b ">{{
                                                terminal.deviceSellTypeText
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 ">مبلغ فروش / امانت / اقساط</div>
                                        <div class="border-b border-gray-200 font-bold">{{
                                                terminal.device_amount
                                            }}
                                        </div>
                                        <div class="border-b border-gray-200 ">شماره پرونده اقساط</div>
                                        <div class="border-b border-gray-200 font-bold">
                                            {{ terminal.device_dept_profile_id }}
                                        </div>
                                        <div class="border-b border-gray-200 ">وضعیت</div>
                                        <div class="border-b border-gray-200 font-bold">{{ terminal.statusText }}</div>
                                        <div v-if="terminal.reject_reason">علت رد شدن سریال</div>
                                        <div v-if="terminal.reject_reason" class="lg:col-span-3 text-red-500">
                                            {{ terminal.reject_reason }}
                                        </div>
                                        <div v-if="terminal.cancel_reason || terminal.change_reason"
                                             class="lg:col-span-2">علت درخواست جابجایی / فسخ
                                        </div>
                                        <div v-if="terminal.cancel_reason || terminal.change_reason"
                                             class="lg:col-span-2 text-red-500 font-bold">
                                            {{ terminal.cancel_reason || terminal.change_reason }}
                                        </div>
                                        <div
                                            class="col-span-2 lg:col-span-6 flex items-center justify-center space-x-reverse space-x-2">
                                            <jet-button
                                                v-if="(profile.status === 5 || profile.status === 13) && (terminal.status === 0 || terminal.status === 2)"
                                                @click.native="selectSerial(terminal)"
                                                class="terminal-actions border-green-600 text-green-600 hover:text-green-700"
                                                title="انتخاب دستگاه">
                                                انتخاب دستگاه
                                                <i class="material-icons">keyboard_hide</i>
                                            </jet-button>
                                            <button v-if="terminal.status === 9 || terminal.status === 3"
                                                    v-on:click="installDevice(terminal)"
                                                    class="terminal-actions border-green-600 text-green-600 hover:text-green-700"
                                                    title="نصب دستگاه">
                                                نصب دستگاه
                                                <i class="material-icons">phonelink_setup</i>
                                            </button>
                                            <button
                                                v-if="terminal.status === 6 || terminal.status === 9 || terminal.status === 3"
                                                v-on:click="changeSerialRequest(terminal)"
                                                class="terminal-actions border-yellow-600 text-yellow-600 hover:text-yellow-700"
                                                title="جابجایی سریال">
                                                جابجایی سریال
                                                <i class="material-icons">undo</i>
                                            </button>
                                            <button
                                                v-if="profile.status===14 && terminal.status === 7 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='AGENT')"
                                                v-on:click="selectSerial(terminal,true)"
                                                class="terminal-actions border-blue-600 text-blue-600 hover:text-blue-700"
                                                title="تخصیص سریال جدید">
                                                تخصیص سریال جدید
                                                <i class="material-icons">change_circle</i>
                                            </button>
                                            <button
                                                v-if="terminal.status === 8 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='OFFICE')"
                                                v-on:click="confirmChangeSerial(terminal)"
                                                class="terminal-actions border-blue-600 text-blue-600 hover:text-blue-700"
                                                title="تایید جابجایی">
                                                تایید جابجایی
                                                <i class="material-icons">change_circle</i>
                                            </button>
                                            <button
                                                v-if="terminal.status === 6 || terminal.status === 9 || terminal.status === 3"
                                                v-on:click="cancelRequest(terminal)"
                                                class="terminal-actions border-red-600 text-red-600 hover:text-red-700"
                                                title="درخواست فسخ">
                                                درخواست فسخ
                                                <i class="material-icons">cancel</i>
                                            </button>
                                            <button
                                                v-if="terminal.status == 4 && ($page.user.level=='SUPERUSER' || $page.user.level=='ADMIN' || $page.user.level=='OFFICE')"
                                                v-on:click="confirmCancel(terminal)"
                                                class="terminal-actions border-yellow-600 text-yellow-600 hover:text-yellow-700"
                                                title="تایید فسخ">
                                                تایید فسخ
                                                <i class="material-icons">block</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-span-2 lg:col-span-6 text-left">
                                        <inertia-link
                                            :href="route('dashboard.profiles.devices.create',{profile:profile.id})">
                                            <jet-button
                                                class="block bg-blue-500 hover:bg-blue-400 mx-auto">
                                                ثبت ترمینال جدید
                                            </jet-button>
                                        </inertia-link>
                                    </div>
                                </div>
                                <div v-if="currentTab==='licenses'" class="py-2 px-3" id="licenses">
                                    <div
                                        class="bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <p class="text-lg py-1">وضعیت مدارک:
                                            <span class="rounded py-1 px-2"
                                                  :class="licensesStatusColor(profile.licenses_status)">
                                            {{ profile.licensesStatusText }}
                                        </span>
                                        </p>
                                        {{ profile.licenses_message }}
                                    </div>
                                    <div
                                        class="grid grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <div v-for="license in profile.licenses" :key="license.id"
                                             class="text-center text-indigo-600 relative">
                                            <a target="_blank" :href="license.url">
                                                <img :src="license.url" class="w-full">
                                                {{ license.type.name }}
                                            </a>
                                            <InertiaLink
                                                v-if="canEditLicenses"
                                                :href="route('dashboard.profiles.licenses.destroy',{profile:profile.id,licenseId:license.id})"
                                                method="DELETE"
                                                class="block text-red-600 hover:text-red-400">حذف این تصویر
                                            </InertiaLink>
                                        </div>
                                        <div
                                            v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'"
                                            class="col-span-2 lg:col-span-4 text-left flex flex-col lg:flex-row space-y-2 my-2">
                                            <a :href="route('dashboard.profiles.licenses.downloadZipArchive',{profile:profile.id})"
                                               class="ml-auto"
                                               target="_blank">
                                                <jet-button class="bg-blue-500 hover:bg-blue-400">
                                                    <i class="material-icons">file_download</i>
                                                    دریافت همه مدارک به صورت یکجا
                                                </jet-button>
                                            </a>
                                            <jet-button class="bg-red-500 hover:bg-red-400 ml-1"
                                                        @click.native="changeLicensesStatus(3)"
                                                        v-if="profile.licenses_status!==3"><i
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
                                    </div>
                                    <div
                                        class="grid grid-cols-2 lg:grid-cols-4 gap-x-3 gap-y-6 bg-white border border-gray-200 rounded my-3 mx-1 py-2 px-3">
                                        <template v-if="canEditLicenses">
                                            <div
                                                class="col-span-2">
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
                                                    <p class="text-sm text-red-400">چنانچه مدرکی را قبلا ارسال نموده
                                                        اید با
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
                                                        <option v-for="account in profile.accounts"
                                                                :key="account.id"
                                                                :value="account.account_id">
                                                            {{ account.account.bank.name }} -
                                                            {{ account.account.account_number }}
                                                        </option>
                                                    </select>
                                                    <p class="text-sm text-red-500">چنانچه مدرک ارسالی، تصویر
                                                        تاییدیه شبا می
                                                        باشد حتما شماره حساب مرتبط با آن را انتخاب نمایید.</p>
                                                    <jet-input-error
                                                        :message="uploadLicenseForm.error('account_id')"
                                                        class="mt-2"/>
                                                </div>
                                            </div>
                                            <div
                                                class="col-span-2">
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
                                                class="col-span-2 lg:col-span-4 text-center lg:text-left">
                                                <jet-button @click.native="submitLicenseFile">ارسال فایل
                                                </jet-button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <div class="grid md:grid-cols-6 gap-6">
                                <div class="col-6 sm:col-span-6">
                                    <div class="col-6 sm:col-span-6 text-left">
                                        <template
                                            v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'">
                                            <div
                                                class="border border-red-700 bg-red-100 rounded m-1 py-2 px-3 text-red-700 flex items-center justify-start">
                                                <div class="mx-1">
                                                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5M11,7H13V13H11M11,15H13V17H11"/>
                                                    </svg>
                                                </div>
                                                <div>لطفا در تغییر وضعیت پرونده ها از این قسمت دقت نمایید.</div>
                                            </div>
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
            <!-- انتخاب سریال -->
            <jet-confirmation-modal :show="viewSearchModal" @close="closeSerialModal">
                <template #title>
                    جستجوی شماره سریال
                </template>
                <template #content>
                    <div>
                        <div v-if="selectedTerminal && selectedTerminal.change_reason"
                             class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست جابجایی</p>
                            <p class="text-md">{{ selectedTerminal && selectedTerminal.change_reason }}</p>
                        </div>
                        <div class="my-3 p-2">
                            <p class="text-lg" v-if="newDeviceType">
                                مدل انتخاب شده: {{ newDeviceType.name }}</p>
                        </div>
                        <div v-if="loadingDevicesList"
                             class="text-center text-lg font-bold my-3 animate-bounce">در حال بارگذاری لیست دستگاه‌ها...
                        </div>
                        <div v-else class="mt-3 text-center sm:mt-0 sm:ml-1 sm:text-right">
                            <input type="text"
                                   :disabled="loadingDevicesList"
                                   :readonly="loadingDevicesList"
                                   class=" inline-flex shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border"
                                   placeholder="جستجوی سریال"
                                   ref="search_serial"
                                   id="search_serial"
                                   v-model="search.serial"/>
                            <button type="button"
                                    :disabled="loadingDevicesList"
                                    :class="loadingDevicesList ? 'bg-gray-100' : 'bg-red-600 hover:bg-red-700'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                    v-on:click="searchDeviceSerials">
                                جستجو
                            </button>
                            <div class="mt-2 h-48 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            شماره سریال
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            کد IMEI
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            مدل دستگاه
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وضعیت فیزیکی
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="device in search.results" :key="device.id">
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ device.serial }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ device.imei ? device.imei : '-' }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ device.device_type.name }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ device.physicalStatusText }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            <button type="button"
                                                    :disabled="selectedTerminal.device_id===device.id"
                                                    :class="selectedTerminal.device_id===device.id ? 'bg-gray-100 text-gray-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white'"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
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
                    <jet-secondary-button @click.native="closeSerialModal">
                        انصراف
                    </jet-secondary-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید سریال انتخاب شده -->
            <jet-confirmation-modal :show="confirmSerial" @close="closeConfirmSerialModal">
                <template #title>
                    تایید شماره سریال
                </template>

                <template #content>
                    آیا از انتخاب این سریال مطمئن هستید؟
                    <div class="mt-4" v-if="selectedDevice">
                        <div class="flex items-center justify-start">
                            <div class="w-1/2">مدل دستگاه:</div>
                            <div class="text-red-400 font-bold">
                                {{ selectedDevice.device_type && selectedDevice.device_type.name }}
                            </div>
                        </div>
                        <div class="flex items-center justify-start">
                            <div class="w-1/2">سریال دستگاه:</div>
                            <div class="text-red-400 font-bold">{{ selectedDevice.serial }}</div>
                        </div>
                        <div class="flex items-center justify-start">
                            <div class="w-1/2">کد IMEI:</div>
                            <div v-if="selectedDevice.imei" class="text-red-400 font-bold">{{
                                    selectedDevice.imei
                                }}
                            </div>
                            <div v-else>
                                <jet-input type="text" placeholder="کد IMEI" class="w-full" v-model="terminalForm.terminal.imei"/>
                                <jet-input-error :message="terminalForm.error('terminal.imei')" class="mt-2"/>
                            </div>
                        </div>
                        <div class="flex items-center justify-start">
                            <div class="w-1/2">شماره سیم‌کارت:</div>
                            <div v-if="selectedDevice.sim_number" class="text-red-400 font-bold">{{
                                    selectedDevice.sim_number
                                }}
                            </div>
                            <div v-else>
                                <jet-input type="text" placeholder="شماره سیم‌کارت" class="w-full" v-model="terminalForm.terminal.sim_number"/>
                                <jet-input-error :message="terminalForm.error('terminal.sim_number')" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="closeConfirmSerialModal">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="updateTerminal"
                                       :class="{ 'opacity-25': terminalForm.processing }"
                                       :disabled="terminalForm.processing">
                        تایید
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- ثبت شماره پذیرنده -->
            <jet-confirmation-modal :show="viewMerchantModal" @close="closeMerchantModal">
                <template #title>
                    ثبت شماره پذیرنده
                </template>
                <template #content>
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                            <div class="mt-2">
                                <label for="merchant_id"
                                       class="block text-sm font-medium text-gray-700">شماره پذیرنده</label>
                                <input type="text"
                                       class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="شماره پذیرنده"
                                       ref="merchant_id"
                                       id="merchant_id"
                                       v-model="merchantForm.merchant_id"/>
                                <jet-input-error :message="merchantForm.error('merchant_id')"
                                                 class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeMerchantModal">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="submitMerchantNumber"
                                :class="{ 'opacity-25': merchantForm.processing }"
                                :disabled="merchantForm.processing">
                        تایید
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- ثبت شماره ترمینال -->
            <jet-confirmation-modal :show="viewTerminalModal" @close="closeTerminalModal">
                <template #title>
                    ثبت شماره پایانه
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
                                       v-model="terminalForm.terminal.terminal_number"/>
                                <jet-input-error :message="terminalForm.error('terminal.terminal_number')"
                                                 class="mt-2"/>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-around">
                                <div class="w-1/2">مدل دستگاه:</div>
                                <div>{{ selectedTerminal && selectedTerminal.device_type && selectedTerminal.device_type.name }}</div>
                            </div>
                            <div class="flex items-center justify-around">
                                <div class="w-1/2">سریال:</div>
                                <div>{{ selectedTerminal && selectedTerminal.device && selectedTerminal.device.serial }}</div>
                            </div>
                            <div class="flex items-center justify-around">
                                <div class="w-1/2">کد IMEI:</div>
                                <div v-if="selectedTerminal && selectedTerminal.device && selectedTerminal.device.imei" class="text-red-400 font-bold">
                                    {{ selectedTerminal && selectedTerminal.device && selectedTerminal.device.imei }}
                                </div>
                                <div v-else>
                                    <jet-input type="text" class="w-full" v-model="terminalForm.terminal.imei"/>
                                    <jet-input-error :message="terminalForm.error('terminal.imei')" class="mt-2"/>
                                </div>
                            </div>
                            <div class="flex items-center justify-start">
                                <div class="w-1/2">شماره سیم‌کارت:</div>
                                <div v-if="selectedTerminal && selectedTerminal.device && selectedTerminal.device.sim_number" class="text-red-400 font-bold">{{
                                        selectedTerminal && selectedTerminal.device && selectedTerminal.device.sim_number
                                    }}
                                </div>
                                <div v-else>
                                    <jet-input type="text" placeholder="شماره سیم‌کارت" class="w-full" v-model="terminalForm.terminal.sim_number"/>
                                    <jet-input-error :message="terminalForm.error('terminal.sim_number')" class="mt-2"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeTerminalModal">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="updateTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
                        تایید
                    </jet-button>
                    <jet-danger-button class="ml-2" @click.native="rejectSerialModal">
                        عدم تایید سریال
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- عدم تایید سریال -->
            <jet-confirmation-modal :show="viewRejectSerialModal" @close="closeRejectSerialModal">
                <template #title>
                    عدم تایید سریال
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <label for="reject_serial_reason"
                               class="block text-sm font-medium text-gray-700">علت عدم تایید</label>
                        <textarea
                            class="mt-1 block w-full form-input"
                            placeholder="علت عدم تایید"
                            ref="reject_serial_reason"
                            id="reject_serial_reason"
                            v-model="terminalForm.terminal.reject_reason"/>
                        <jet-input-error :message="terminalForm.error('terminal.reject_reason')"
                                         class="mt-2"/>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeRejectSerialModal">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="updateTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
                        ذخیره
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- درخواست فسخ -->
            <jet-confirmation-modal :show="viewCancelRequestModal" @close="closeCancelRequest">
                <template #title>
                    درخواست فسخ ترمینال
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
                                v-model="terminalForm.terminal.cancel_reason"/>
                            <jet-input-error :message="terminalForm.error('terminal.cancel_reason')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeCancelRequest">
                        انصراف
                    </jet-secondary-button>
                    <jet-danger-button class="ml-2" @click.native="updateTerminal"
                                       :class="{ 'opacity-25': terminalForm.terminal.processing }"
                                       :disabled="terminalForm.terminal.processing">
                        ارسال
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید یا رد فسخ -->
            <jet-confirmation-modal :show="viewConfirmCancelModal" @close="closeConfirmCancel">
                <template #title>
                    تایید فسخ پرونده
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست فسخ</p>
                            <p class="text-md">{{ selectedTerminal && selectedTerminal.cancel_reason }}</p>
                        </div>
                        <div class="flex">
                            <jet-button
                                @click.native="cancelChangeStatus('confirmCancel')"
                                :class="terminalForm.action==='confirmCancel' ? 'bg-green-600' : 'bg-green-300'"
                                class="mx-auto hover:bg-green-500">
                                تایید
                            </jet-button>
                            <jet-button
                                @click.native="cancelChangeStatus('cancelCancel')"
                                :class="terminalForm.action==='cancelCancel' ? 'bg-red-600' : 'bg-red-300'"
                                class="mx-auto">
                                رد درخواست
                            </jet-button>
                        </div>
                        <jet-input-error :message="terminalForm.error('status')"
                                         class="mt-2"/>
                        <div v-if="terminalForm.action==='cancelCancel'">
                            <label for="message"
                                   class="block text-sm font-medium text-gray-700">علت رد درخواست</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت رد درخواست"
                                ref="message"
                                id="message"
                                v-model="terminalForm.terminal.reject_reason"/>
                            <jet-input-error :message="terminalForm.error('terminal.reject_reason')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeConfirmCancel">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="updateTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- جابجایی سریال-->
            <jet-confirmation-modal :show="viewChangeSerialRequestModal" @close="closeChangeSerialRequest">
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
                                v-model="terminalForm.terminal.change_reason"/>
                            <jet-input-error :message="terminalForm.error('terminal.change_reason')"
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
                                    <h1 class="text-md">{{ deviceType.name }}</h1>
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
                            <jet-input-error :message="terminalForm.error('terminal.new_device_type_id')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeChangeSerialRequest">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="updateTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
            <!-- تایید یا رد جابجایی -->
            <jet-confirmation-modal :show="viewConfirmChangeSerialModal" @close="closeConfirmChangeSerial">
                <template #title>
                    تایید جابجایی پرونده
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="my-3 p-2 bg-yellow-300 border-r-4 border-yellow-500">
                            <p class="text-lg">علت درخواست جابجایی</p>
                            <p class="text-md">{{ selectedTerminal && selectedTerminal.change_reason }}</p>
                        </div>
                        <div v-if="loadingNewDeviceInfo" class="text-center animate-bounce text-lg my-3">در حال دریافت
                            اطلاعات...
                        </div>
                        <div v-else class="my-3 p-2">
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
                                <tr v-if="newDevice">
                                    <td class="text-center">{{ newDevice.device_type.name }}</td>
                                    <td class="text-center">{{ newDevice.serial }}</td>
                                    <td class="text-center">{{ newDevice.physicalStatusText }}</td>
                                    <td class="text-center">{{ newDevice.transportStatusText }}</td>
                                    <td class="text-center">{{ newDevice.pspStatusText }}</td>
                                    <td class="text-center">{{ newDevice.statusText }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="!loadingNewDeviceInfo" class="flex">
                            <jet-button
                                @click.native="confirmChangeStatus('confirm')"
                                :class="terminalForm.action==='confirmChange' ? 'bg-green-600' : ''"
                                class="border-green-600 bg-green-300 mx-auto hover:bg-green-500 hover:text-white">
                                تایید
                            </jet-button>
                            <jet-button
                                @click.native="confirmChangeStatus('cancel')"
                                :class="terminalForm.action==='cancelChange' ? 'bg-red-600' : ''"
                                class="border-red-600 bg-red-300 mx-auto hover:bg-red-500 hover:text-white">
                                رد درخواست
                            </jet-button>
                        </div>
                        <jet-input-error :message="terminalForm.error('status')"
                                         class="mt-2"/>
                        <div v-if="terminalForm.action==='cancelChange'">
                            <label for="change_message"
                                   class="block text-sm font-medium text-gray-700">علت رد درخواست</label>
                            <textarea
                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="علت رد درخواست"
                                ref="change_message"
                                id="change_message"
                                v-model="terminalForm.terminal.reject_reason"/>
                            <jet-input-error :message="terminalForm.error('terminal.reject_reason')"
                                             class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeConfirmChangeSerial">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="updateTerminal"
                                :class="{ 'opacity-25': terminalForm.processing }"
                                :disabled="terminalForm.processing">
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

        selectedTab: {
            type: String,
            default: 'general'
        }
    },
    data() {
        return {
            currentTab: this.selectedTab,
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
            /*
            جستجوی و انتخاب سریال برای ترمینال
             */
            selectedTerminal: null,
            selectedDevice: null,
            confirmSerial: false,
            loadingDevicesList: false,
            submitSerialFormLoading: false,
            /*
            ثبت شماره پذیرنده
             */
            viewMerchantModal: false,
            merchantForm: this.$inertia.form({
                merchant_id: this.profile.merchant_id,
            }, {
                bag: 'merchantForm',
                resetOnSuccess: true,
            }),
            /*
            ثبت شماره ترمینال
             */
            viewTerminalModal: false,
            terminalForm: this.$inertia.form({
                '_method': 'PUT',
                action: null,
                terminal: {
                    terminal_number: null,
                    device_type_id: null,
                    device_id: null,
                    reject_reason: null,
                    cancel_reason: null,
                    new_device_type_id: null,
                    new_device_id: null,
                    change_reason: null,
                    status: null,
                    reserved_device_id: null,
                    imei: null,
                    sim_number: null,
                },
                profile: {
                    status: null
                },
            }, {
                bag: 'terminalForm',
                resetOnSuccess: false,
            }),
            /*
            رد شدن سزیال
            */
            rejectSerialReason: null,
            viewRejectSerialModal: false,
            /*
            درخواست فسخ ترمینال
            */
            viewCancelRequestModal: false,
            /*
            تایید فسخ ترمینال
            */
            viewConfirmCancelModal: false,
            loadingNewDeviceInfo: false,
            /*
            درخواست جابجایی سریال
            */
            viewChangeSerialRequestModal: false,
            deviceTypes: [],
            oldDeviceTypeId: '',
            newDeviceTypeId: '',
            newDeviceType: null,
            /*
            تایید یا رد سریال جدید
            */
            viewConfirmChangeSerialModal: false,
            newDevice: null,
            /*
            بارگذاری مدارک
            */
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
        let hash = window.location.hash;
        if (hash) this.viewTab(hash.substr(1));
    },
    methods: {
        viewTab(tab) {
            this.currentTab = tab;
            window.location.hash = tab;
        },
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
            this.licensesStatusForm.put(route('dashboard.profiles.update.licenses.confirm', {profile: this.profile.id})).then(() => {
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
            Inertia.visit(route('dashboard.profiles.update.status', {profile: this.profile.id}), {
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
            this.profileForm.post(route('dashboard.profiles.update', {profile: this.profile.id})).then(response => {
                if (!this.profileForm.hasErrors()) {
                    this.viewErrorModal = false;
                }
                this.submitProfileFormLoading = false;
                this.profile.messages.reverse();
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
            this.profileTypeForm.post(route('dashboard.profiles.update.type', {profile: this.profile.id}), {
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
            this.uploadLicenseForm.post(route('dashboard.profiles.licenses.store', {profile: this.profile}), {
                preserveScroll: true
            })
                .then(response => {

                })
        },

        async updateTerminalForm(terminal, action) {
            this.terminalForm.action = action;
            this.terminalForm.terminal.terminal_number = terminal.terminal_number;
            this.terminalForm.terminal.device_type_id = terminal.device_type_id;
            this.terminalForm.terminal.device_id = terminal.device_id;
            this.terminalForm.terminal.reject_reason = terminal.reject_reason;
            this.terminalForm.terminal.cancel_reason = terminal.cancel_reason;
            this.terminalForm.terminal.new_device_type_id = terminal.new_device_type_id;
            this.terminalForm.terminal.new_device_id = terminal.new_device_id;
            this.terminalForm.terminal.change_reason = terminal.change_reason;
            this.terminalForm.terminal.status = terminal.status;

            this.terminalForm.profile.status = this.profile.status;
        },

        selectSerial(terminal, change) {
            this.selectedTerminal = terminal;
            let action = change ? 'changeSerial' : 'new';
            this.updateTerminalForm(terminal, action).then(() => {
                this.search.results = [];
                this.devices = [];
                this.viewSearchModal = true;
                let queryString = '?';
                let deviceTypeId = terminal.device_type_id;
                this.loadingDevicesList = true;
                if (change) {
                    deviceTypeId = terminal.new_device_type_id;
                    queryString = '?change=true';
                }
                axios.get(route('dashboard.devices.types', {type: deviceTypeId}))
                    .then(response => {
                        this.newDeviceType = response.data;
                        axios.get(`dashboard/devices/${terminal.id}`)
                            .then(response => {
                                this.devices = response.data;
                            })
                            .catch(error => {
                                console.log(error);
                            }).finally(() => {
                            this.loadingDevicesList = false;
                        });
                    })
                    .catch(error => {
                        console.log(error);
                    })
            });
        },
        searchDeviceSerials() {
            let serial = this.$refs.search_serial.value;
            let condition = new RegExp(serial);
            this.search.results = this.devices.filter(function (device) {
                return condition.test(device.serial);
            });
        },
        selectDevice(device) {
            this.selectedDevice = device;
            if (this.terminalForm.action === 'changeSerial') {
                this.terminalForm.terminal.status = 8;
                this.terminalForm.profile.status = 15;
                this.terminalForm.terminal.new_device_id = device.id;
            } else if (this.terminalForm.action === 'new') {
                this.terminalForm.terminal.status = 1;
                this.terminalForm.profile.status = 6;
                this.terminalForm.terminal.device_id = device.id;
            }
            this.terminalForm.terminal.reject_reason = null;
            this.terminalForm.terminal.cancel_reason = null;
            this.confirmSerial = true;
        },
        closeSerialModal() {
            this.viewSearchModal = false;
            this.$refs.search_serial.value = '';
            this.search.serial = '';
            this.search.results = [];
            this.selectedTerminal = null;
            this.selectedDevice = null;
            this.terminalForm.reset();
        },
        closeConfirmSerialModal() {
            this.confirmSerial = false;
            this.terminalForm.reset();
        },
        installDevice(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'install').then(() => {
                this.terminalForm.terminal.status = 6;
                this.terminalForm.profile.status = 8;
                this.updateTerminal();
            });
        },
        updateTerminal() {
            this.terminalForm.put(route('dashboard.profiles.terminals.update', {
                profile: this.profile.id,
                terminal: this.selectedTerminal.id
            }))
                .then(response => {
                    if (!this.terminalForm.hasErrors()) {
                        switch (this.terminalForm.action) {
                            default:
                            case 'new':
                                this.closeSerialModal();
                                this.closeConfirmSerialModal();
                                break;
                            case 'terminal':
                            case 'reject':
                                this.closeTerminalModal();
                                this.closeRejectSerialModal();
                                break;
                            case 'change':
                                this.closeChangeSerialRequest();
                                break;
                            case 'cancelChange':
                            case 'confirmChange':
                                this.closeConfirmChangeSerial();
                                break;
                            case 'cancel':
                                this.closeCancelRequest();
                                break;
                            case 'confirmCancel':
                            case 'cancelCancel':
                                this.closeConfirmCancel();
                                break;
                        }
                        this.terminalForm.reset();
                    }
                });
        },

        updateIMEIModal(device) {
            this.selectedDevice = device;
            this.viewUpdateIMEIModal = true;
        },
        updateIMEI() {

        },
        closeUpdateIMEIModal() {
            this.selectedDevice = null;
            this.viewUpdateIMEIModal = false;
        },
        /*
        ثبت شماره پذیرنده
         */
        showMerchantModal() {
            this.viewMerchantModal = true
        },
        submitMerchantNumber() {
            this.merchantForm.put(route('dashboard.profiles.update.merchant', {profile: this.profile.id})).then(response => {
                if (!this.merchantForm.hasErrors()) {
                    this.closeMerchantModal();
                }
            })
        },
        closeMerchantModal() {
            this.merchantForm.merchant_id = this.profile.merchant_id;
            this.viewMerchantModal = false
        },
        /*
        ثبت شماره ترمینال
         */
        showTerminalModal(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'terminal').then(() => {
                this.viewTerminalModal = true;
                this.terminalForm.terminal.status = 3;
                this.terminalForm.profile.status = 7;
            });
        },
        closeTerminalModal() {
            this.viewTerminalModal = false;
            this.selectedTerminal = null;
            this.terminalForm.reset();
        },
        /*
        رد سریال
         */
        rejectSerialModal() {
            this.viewRejectSerialModal = true;
            this.terminalForm.action = 'reject';
            this.terminalForm.terminal.device_id = null;
            this.terminalForm.terminal.status = 0;
            this.terminalForm.profile.status = 13;
        },
        closeRejectSerialModal() {
            this.viewRejectSerialModal = false;
            this.terminalForm.reset();
        },
        /*
        درخواست فسخ ترمینال
         */
        cancelRequest(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'cancel').then(() => {
                this.terminalForm.terminal.status = 4;
                this.terminalForm.profile.status = 12;
                this.viewCancelRequestModal = true;
            });
        },
        closeCancelRequest() {
            this.viewCancelRequestModal = false;
        },
        /*
        تایید فسخ ترمینال
         */
        confirmCancel(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'cancel').then(() => {
                this.viewConfirmCancelModal = true;
            });

        },
        cancelChangeStatus(status) {
            if (status === 'confirmCancel') {
                this.terminalForm.terminal.status = 5;
                this.terminalForm.profile.status = 9;
                this.terminalForm.terminal.reserved_device_id = this.selectedTerminal.device_id;
                this.terminalForm.action = 'confirmCancel';
            } else if (status === 'cancelCancel') {
                if (this.selectedTerminal && this.selectedTerminal.device && this.selectedTerminal.device.transport_status === 3) {
                    this.terminalForm.terminal.status = 6;
                    this.terminalForm.profile.status = 8;
                } else {
                    this.terminalForm.terminal.status = 3;
                    this.terminalForm.profile.status = 7;
                }
                this.terminalForm.terminal.cancel_reason = null;
                this.terminalForm.action = 'cancelCancel';
            }
        },
        closeConfirmCancel() {
            this.viewConfirmCancelModal = false;
        },
        /*
        درخواست جابجایی
         */
        changeSerialRequest(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'change').then(() => {
                this.viewChangeSerialRequestModal = true;
                this.selectedTerminal = terminal;
                this.oldDeviceTypeId = terminal.device_type_id;
                this.terminalForm.terminal.new_device_type_id = terminal.device_type_id;
                axios.get('dashboard/deviceTypes/' + terminal.id)
                    .then(response => {
                        this.deviceTypes = response.data;
                        this.selectNewDeviceType(terminal.device_type_id);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            })

        },
        selectNewDeviceType(newDeviceTypeId) {
            this.oldDeviceTypeId = newDeviceTypeId;
            this.terminalForm.terminal.new_device_id = null;
            this.terminalForm.terminal.new_device_type_id = newDeviceTypeId;
            this.terminalForm.terminal.reject_reason = null;
            this.terminalForm.terminal.status = 7;
            this.terminalForm.profile.status = 14;
        },
        closeChangeSerialRequest() {
            this.viewChangeSerialRequestModal = false;
            this.oldDeviceTypeId = null;
        },
        /*
        تایید یا رد درخواست جابجایی
         */
        confirmChangeSerial(terminal) {
            this.selectedTerminal = terminal;
            this.updateTerminalForm(terminal, 'confirmChange').then(() => {
                this.confirmChangeStatus('confirm')
                this.viewConfirmChangeSerialModal = true;
                this.loadingNewDeviceInfo = true;
                axios.get(route('api.dashboard.devices.view', {device: terminal.new_device_id}))
                    .then(response => {
                        this.newDevice = response.data;
                        this.terminalForm.terminal.status = 3;
                        this.terminalForm.profile.status = 6;
                    })
                    .catch(error => {
                        console.log(error);
                    })
                    .finally(() => {
                        this.loadingNewDeviceInfo = false;
                    });
            });
        },
        closeConfirmChangeSerial() {
            this.viewConfirmChangeSerialModal = false;
            this.loadingNewDeviceInfo = false;
        },
        confirmChangeStatus(status) {
            if (status === 'confirm') {
                this.terminalForm.terminal.status = 3;
                this.terminalForm.profile.status = 8;
                this.terminalForm.terminal.device_type_id = this.selectedTerminal.new_device_type_id;
                this.terminalForm.terminal.device_id = this.selectedTerminal.new_device_id;
                this.terminalForm.terminal.reject_reason = null;
                this.terminalForm.terminal.cancel_reason = null;
                this.terminalForm.terminal.new_device_type_id = null;
                this.terminalForm.terminal.new_device_id = null;
                this.terminalForm.terminal.change_reason = null;
                this.terminalForm.action = 'confirmChange';
            } else if (status === 'cancel') {
                if (this.selectedTerminal && this.selectedTerminal.device && this.selectedTerminal.device.transport_status === 3) {
                    this.terminalForm.terminal.status = 6;
                    this.terminalForm.profile.status = 8;

                } else {
                    this.terminalForm.terminal.status = 3;
                    this.terminalForm.profile.status = 7;
                }

                this.terminalForm.action = 'cancelChange';
                this.terminalForm.terminal.reserved_device_id = this.selectedTerminal.new_device_id;
                this.terminalForm.terminal.device_type_id = this.selectedTerminal.device_type_id;
                this.terminalForm.terminal.device_id = this.selectedTerminal.device_id;
                this.terminalForm.terminal.cancel_reason = null;
                this.terminalForm.terminal.new_device_type_id = null;
                this.terminalForm.terminal.new_device_id = null;
                this.terminalForm.terminal.change_reason = null;
            }
        }
    },
    computed: {
        canEditLicenses() {
            return this.profile.licenses_status === 0 || this.profile.licenses_status === 1 || this.profile.licenses_status === 3 || this.$page.user.level === 'ADMIN' || this.$page.user.level === 'OFFICE' || this.$page.user.level === 'SUPERUSER'
        },
        birthCertificateSeries() {
            let serial = this.profile.customer.birth_crtfct_serial;
            let letter;
            let number = this.profile.customer.birth_crtfct_series_number;
            switch (parseInt(this.profile.customer.birth_crtfct_series_letter)) {
                case 0:
                    letter = 'الف';
                    break;
                case 1:
                    letter = 'ب';
                    break;
                case 2:
                    letter = 'ل';
                    break;
                case 3:
                    letter = 'د';
                    break;
                case 4:
                    letter = 'ر';
                    break;
                case 5:
                    letter = '۱';
                    break;
                case 6:
                    letter = '۲';
                    break;
                case 7:
                    letter = '۳';
                    break;
                case 8:
                    letter = '۴';
                    break;
                case 9:
                    letter = '۹';
                    break;
                case 10:
                    letter = '۱۰';
                    break;
                case 11:
                    letter = '۱۱';
                    break;
            }
            return `${serial}-${letter}/${number}`;
        }
    }
}
</script>

<style scoped>
.terminal-actions {
    @apply inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150
}

.terminal-actions:hover {
    @apply bg-gray-200
}

.terminal-actions:focus {
    @apply outline-none border-gray-900 shadow-outline-gray
}

.terminal-actions:active {
    @apply bg-gray-900
}
</style>
