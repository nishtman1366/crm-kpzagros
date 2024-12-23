<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت اطلاعات کسب و کار {{ customer.fullName }}</template>
        <template #dashboardContent>
            <ProfileSteps :step="2"
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
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات کسب و کار</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به کسب و کار مشتری را وارد نمایید.
                            </p>
                            <p class="mt-1 text-sm text-gray-600 bg-red-200 text-red-700 px-2 py-1 rounded-md">
                                پر کردن موارد ستاره‌دار (<span class="text-red-500">*</span>) الزامیست
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <div class="grid grid-cols-4 gap-2">
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="ostan_id" class="block text-sm font-medium text-gray-700">
                                                    استان<span class="text-red-500">*</span>
                                                </label>
                                                <select id="ostan_id" name="ostan_id" ref="ostan_id"
                                                        v-model="businessForm.ostan_id"
                                                        autocomplete="ostan_id" v-on:change="listShahrestansItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب استان</option>
                                                    <option v-for="ostan in ostans" :key="ostan.id"
                                                            :value="ostan.id">
                                                        {{ ostan.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('ostan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="shahrestan_id"
                                                       class="block text-sm font-medium text-gray-700">
                                                    شهرستان<span class="text-red-500">*</span>
                                                </label>
                                                <select id="shahrestan_id" name="shahrestan_id" ref="shahrestan_id"
                                                        v-model="businessForm.shahrestan_id"
                                                        autocomplete="shahrestan_id"
                                                        v-on:change="listBakhshItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهرستان</option>
                                                    <option v-for="shahrestan in shahrestanItems"
                                                            :key="shahrestan.id" :value="shahrestan.id">
                                                        {{ shahrestan.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahrestan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="bakhsh_id" class="block text-sm font-medium text-gray-700">
                                                    بخش<span class="text-red-500">*</span>
                                                </label>
                                                <select id="bakhsh_id" name="bakhsh_id" autocomplete="bakhsh_id"
                                                        v-model="businessForm.bakhsh_id"
                                                        ref="bakhsh_id"
                                                        v-on:change="listShahrItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب بخش</option>
                                                    <option v-for="bakhsh in bakhshItems"
                                                            :key="bakhsh.id" :value="bakhsh.id">
                                                        {{ bakhsh.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('bakhsh_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="shahr_id" class="block text-sm font-medium text-gray-700">
                                                    شهر
                                                </label>
                                                <select id="shahr_id" name="shahr_id" autocomplete="shahr_id"
                                                        v-model="businessForm.shahr_id"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهر</option>
                                                    <option v-for="shahr in shahrItems"
                                                            :key="shahr.id" :value="shahr.id">
                                                        {{ shahr.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahr_id')"
                                                                 class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار"
                                               ref="name"
                                               id="name"
                                               v-model="businessForm.name"/>
                                        <jet-input-error :message="businessForm.error('name')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="name_english" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار به انگلیسی:<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار به انگلیسی"
                                               ref="name_english"
                                               id="name_english"
                                               v-model="businessForm.name_english"/>
                                        <jet-input-error :message="businessForm.error('name_english')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="business_category_code"
                                               class="block text-sm font-medium text-gray-700">
                                            صنف مرتبط<span class="text-red-500">*</span>
                                        </label>
                                        <div>{{ senfText }}</div>
                                        <jet-button @click.native="viewCategories">انتخاب</jet-button>
                                        <jet-input-error
                                            :message="businessForm.error('business_category_code') || businessForm.error('business_subCategory_code')"
                                            class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            کد پستی<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد پستی"
                                               ref="postal_code"
                                               id="postal_code"
                                               v-model="businessForm.postal_code"/>
                                        <jet-input-error :message="businessForm.error('postal_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">
                                            آدرس<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="آدرس"
                                               ref="address"
                                               id="address"
                                               v-model="businessForm.address"/>
                                        <jet-input-error :message="businessForm.error('address')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            آدرس ایمیل
                                        </label>
                                        <input type="email"
                                               style="direction:ltr"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="email@site.com"
                                               ref="email"
                                               id="email"
                                               v-model="businessForm.email"/>
                                        <jet-input-error :message="businessForm.error('email')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            آدرس وبسایت
                                        </label>
                                        <input type="url"
                                               style="direction:ltr"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="https://www.website.com"
                                               ref="website"
                                               id="website"
                                               v-model="businessForm.website"/>
                                        <jet-input-error :message="businessForm.error('website')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-3 md:col-span-2">
                                        <label for="phone_code" class="block text-sm font-medium text-gray-700">
                                            پیش شماره شهرستان<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="پیش شماره شهرستان"
                                               ref="phone_code"
                                               id="phone_code"
                                               v-model="businessForm.phone_code"/>
                                        <jet-input-error :message="businessForm.error('phone_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 md:col-span-4">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">
                                            تلفن تماس<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن تماس"
                                               ref="phone"
                                               id="phone"
                                               v-model="businessForm.phone"/>
                                        <jet-input-error :message="businessForm.error('phone')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            جواز کسب<span class="text-red-500">*</span>
                                        </label>
                                        <jet-button @click.native="hasLicense='YES'"
                                                    :class="hasLicense==='YES' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            دارد
                                        </jet-button>
                                        <jet-button @click.native="hasLicense='NO'"
                                                    :class="hasLicense==='NO' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            ندارد
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('has_license')" class="mt-2"/>
                                    </div>
                                    <template v-if="hasLicense==='YES'">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_code" class="block text-sm font-medium text-gray-700">
                                                شماره جواز کسب<span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شماره جواز کسب"
                                                   ref="license_code"
                                                   id="license_code"
                                                   v-model="businessForm.license_code"/>
                                            <jet-input-error :message="businessForm.error('license_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_date" class="block text-sm font-medium text-gray-700">
                                                تاریخ صدور جواز کسب<span class="text-red-500">*</span>
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
                                            <jet-input-error :message="businessForm.error('license_date')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_start_date"
                                                   class="block text-sm font-medium text-gray-700">
                                                تاریخ انقضای جواز کسب
                                            </label>
                                            <date-picker
                                                ref="regDate_cal"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectLicenseStartDate"
                                                element="license_start_date"
                                                v-model="licenseStartDate"></date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ انقضای جواز کسب"
                                                   ref="license_start_date"
                                                   id="license_start_date"
                                                   v-model="licenseStartDate"
                                                   readonly="true"/>
                                            <jet-input-error :message="businessForm.error('license_start_date')"
                                                             class="mt-2"/>
                                        </div>
                                    </template>
                                    <div class="col-span-6 md:col-span-3">
                                        <label for="tax_code" class="block text-sm font-medium text-gray-700">
                                            کد مالیاتی<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد مالیاتی"
                                               ref="tax_code"
                                               id="tax_code"
                                               v-model="businessForm.tax_code"/>
                                        <jet-input-error :message="businessForm.error('tax_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            وضعیت مالکیت:
                                        </label>
                                        <jet-button @click.native="businessForm.ownership_type='owner'"
                                                    :class="businessForm.ownership_type==='owner' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مالک
                                        </jet-button>
                                        <jet-button @click.native="businessForm.ownership_type='tenant'"
                                                    :class="businessForm.ownership_type==='tenant' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مستاجر
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('has_license')" class="mt-2"/>
                                    </div>
                                    <template v-if="businessForm.ownership_type==='tenant'">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_code" class="block text-sm font-medium text-gray-700">
                                                شماره قرارداد اجاره:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شماره جواز کسب"
                                                   ref="rental_contract_number"
                                                   id="rental_contract_number"
                                                   v-model="businessForm.rental_contract_number"/>
                                            <jet-input-error :message="businessForm.error('license_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_date" class="block text-sm font-medium text-gray-700">
                                                تاریخ پایان اجاره:
                                            </label>
                                            <date-picker
                                                ref="rental_contract_number"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectRentalExpiryDate"
                                                element="rental_expiry_date"
                                                v-model="rentalExpiryDate"></date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ پایان اجاره"
                                                   ref="rental_expiry_date"
                                                   id="rental_expiry_date"
                                                   v-model="rentalExpiryDate"
                                                   readonly="true"/>
                                            <jet-input-error :message="businessForm.error('rental_expiry_date')"
                                                             class="mt-2"/>
                                        </div>
                                    </template>
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            نوع فروشگاه:
                                        </label>
                                        <jet-button @click.native="businessForm.business_type='physical'"
                                                    :class="businessForm.business_type==='physical' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            فیزیکی
                                        </jet-button>
                                        <jet-button @click.native="businessForm.business_type='physicalVirtual'"
                                                    :class="businessForm.business_type==='physicalVirtual' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            فیزیکی و مجازی
                                        </jet-button>
                                        <jet-button @click.native="businessForm.business_type='virtual'"
                                                    :class="businessForm.business_type==='virtual' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مجازی
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('business_type')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            نوع نماد اعتماد الکترونیک:
                                        </label>
                                        <jet-button @click.native="businessForm.etrust_certificate_type=0"
                                                    :class="businessForm.etrust_certificate_type===0 ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            یک ستاره
                                        </jet-button>
                                        <jet-button @click.native="businessForm.etrust_certificate_type=1"
                                                    :class="businessForm.etrust_certificate_type===1 ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            دو ستاره
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_type')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="license_date" class="block text-sm font-medium text-gray-700">
                                            تاریخ دریافت نماد:
                                        </label>
                                        <date-picker
                                            ref="etrust_certificate_issue_date"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectEtrustCertificateIssueDateDate"
                                            element="etrust_certificate_issue_date"
                                            v-model="etrustCertificateissueDate"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ دریافت نماد"
                                               ref="etrust_certificate_issue_date"
                                               id="etrust_certificate_issue_date"
                                               v-model="etrustCertificateissueDate"
                                               readonly="true"/>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_issue_date')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="license_date" class="block text-sm font-medium text-gray-700">
                                            تاریخ انقضای نماد:
                                        </label>
                                        <date-picker
                                            ref="etrust_certificate_expiry_date"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectEtrustCertificateExpiryDate"
                                            element="etrust_certificate_expiry_date"
                                            v-model="etrustCertificateExpiryDate"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ انقضای نماد"
                                               ref="etrust_certificate_expiry_date"
                                               id="etrust_certificate_expiry_date"
                                               v-model="etrustCertificateExpiryDate"
                                               readonly="true"/>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_expiry_date')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="hasLicense==='YES'" class="col-span-6 md:col-span-2">
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
                                                    تصویر جواز کسب<span class="text-red-500">*</span>
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
                                    <div v-if="hasLicense==='NO'" class="col-span-6 md:col-span-2">
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
            <!-- دسته بندی اصناف -->
            <jet-confirmation-modal :show="viewCategoriesModal" @close="closeCategoriesModal">
                <template #title>
                    انتخاب صنف مرتبط
                </template>
                <template #content>
                    <div class="flex items-center">
                        <div>
                            <div>
                                <jet-input type="text" placeholder="جستجو در نام اصناف"
                                           class="w-full"
                                           v-model="searchCategoriesValue"/>
                            </div>
                            <div class="my-2 text-lg">
                                <div class="">صنف انتخاب‌شده:</div>
                                <div>{{ senfText }}</div>
                            </div>
                            <div class="mt-3 h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                    <tr>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            صنف
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            کد صنف
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            سرگروه
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            کد سرگروه
                                        </th>
                                        <th scope="col"
                                            class="px-2 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="category in subCategoryList" :key="category.id">
                                        <td class="px-2 py-4 text-gray-900">
                                            {{ category.name }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ category.code }}
                                        </td>
                                        <td class="px-2 py-4 text-gray-900">
                                            {{ category.parent?.name }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            {{ category.parent?.code }}
                                        </td>
                                        <td class="px-2 py-4 text-center text-gray-900">
                                            <button type="button"
                                                    :disabled="businessForm.business_subCategory_code===category.id"
                                                    :class="businessForm.business_subCategory_code===category.id ? 'bg-gray-100 text-gray-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white'"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                                                    v-on:click="selectCategory(category)">
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
                    <jet-secondary-button class="ml-2" @click.native="closeCategoriesModal">
                        بستن
                    </jet-secondary-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
import JetButton from "@/Jetstream/Button";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";

export default {
    name: "EditBusiness",
    components: {
        Dashboard,
        JetInput,
        JetButton,
        JetInputError,
        datePicker: VuePersianDatetimePicker,
        ProfileSteps,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        profile: Object,
        customer: Object,
        ostans: Array,
        shahrestans: Array,
        bakhshs: Array,
        shahrs: Array,
        profileId: Number,
        business: Object,
        categories: Array,
        subCategories: Array,
    },
    data() {
        return {
            shahrestanItems: [],
            bakhshItems: [],
            shahrItems: [],
            hasLicense: this.profile.business.has_license,
            licenseDate: this.profile.business.jLicenseDate,
            imageFiles: {
                licenseFilePreview: this.profile.business.licenseFile,
                esteshhadFilePreview: this.profile.business.esteshhadFile,
            },
            fileUploadErrors: {
                license_file: '',
                esteshhad_file: '',
            },

            viewCategoriesModal: false,
            searchCategoriesValue: null,
            categoryList: this.categories,
            subCategoryList: this.subCategories,
            selectedCategory: null,

            licenseStartDate: '',
            rentalExpiryDate: '',
            etrustCertificateExpiryDate: '',
            etrustCertificateissueDate: '',

            businessForm: this.$inertia.form({
                '_method': 'PUT',
                profile_id: this.profile.business.profile_id,
                ostan_id: this.profile.business.ostan_id,
                shahrestan_id: this.profile.business.shahrestan_id,
                bakhsh_id: this.profile.business.bakhsh_id,
                shahr_id: this.profile.business.shahr_id,
                // dehestan_id: this.business.dehestan_id,
                // abadi_id: this.business.abadi_id,
                phone_code: this.profile.business.phone_code,
                senf: this.profile.business.senf,
                name: this.profile.business.name,
                name_english: this.profile.business.name_english,
                address: this.profile.business.address,
                postal_code: this.profile.business.postal_code,
                phone: this.profile.business.phone,
                tax_code: this.profile.business.tax_code,
                has_license: this.profile.business.has_license,
                license_code: this.profile.business.license_code,
                license_date: this.profile.business.license_date,
                license_file: this.profile.business.license_file,
                esteshhad_file: this.profile.business.esteshhad_file,

                description: this.profile.business.description,
                license_start_date: this.profile.business.license_start_date,

                ownership_type: this.profile.business.ownership_type,
                rental_contract_number: this.profile.business.rental_contract_number,
                rental_expiry_date: this.profile.business.rental_expiry_date,

                business_category_code: this.profile.business.business_category_code,
                business_subCategory_code: this.profile.business.business_subCategory_code,


                email: this.profile.business.email,
                website: this.profile.business.website,

                business_type: this.profile.business.business_type,

                etrust_certificate_type: this.profile.business.etrust_certificate_type,
                etrust_certificate_issue_date: this.profile.business.etrust_certificate_issue_date,
                etrust_certificate_expiry_date: this.profile.business.etrust_certificate_expiry_date,
            }, {
                bag: 'businessForm',
                resetOnSuccess: false
            })
        }
    },
    watch: {
        searchCategoriesValue: function ($val) {
            if ($val) {
                let condition = new RegExp($val);

                this.subCategoryList = this.subCategories.filter(function (category) {
                    return condition.test(category.name);
                });

                //
                //
                //
                //
                //
                // this.categoryList = this.categories.filter(function (category) {
                //     return condition.test(category.name);
                // });
            } else {
                this.subCategoryList = []
            }
        },
        selectedCategory: function ($val) {
            if ($val) {
                this.subCategoryList = this.subCategories.filter(c => c.category_id === $val);
            } else {
                this.subCategoryList = this.subCategories
            }
        }
    },
    computed: {
        senfText: function () {
            let category = this.categories.find(c => c.id === this.businessForm.business_category_code);
            let subCategory = this.subCategories.find(c => c.id === this.businessForm.business_subCategory_code);
            if (category && subCategory) return `${category.name} - ${subCategory.name}`;
        }
    },
    mounted() {
        this.listShahrestansItems(this.business.ostan_id);
        this.listBakhshItems(this.business.shahrestan_id);
        this.listShahrItems(this.business.bakhsh_id);
    },
    methods: {
        listShahrestansItems(id) {
            let ostanId = id && id != '' ? id : this.$refs.ostan_id.value;
            let shahrestanList = [];
            for (let shahrestan of this.shahrestans) {
                if (shahrestan.ostan == ostanId) {
                    shahrestanList.push({id: shahrestan.id, name: shahrestan.name});
                }
            }
            this.shahrestanItems = shahrestanList;
        },
        listBakhshItems(id) {
            let shahrestanId = id && id != '' ? id : this.$refs.shahrestan_id.value;
            let bakhshList = [];
            for (let bakhsh of this.bakhshs) {
                if (bakhsh.shahrestan == shahrestanId) {
                    bakhshList.push({id: bakhsh.id, name: bakhsh.name});
                }
            }
            this.bakhshItems = bakhshList;
        },
        listShahrItems(id) {
            let bakhshId = id && id != '' ? id : this.$refs.bakhsh_id.value;
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
        selectLicenseStartDate(e) {
            this.businessForm.license_start_date = e.format('YYYY/MM/DD');
        },
        selectRentalExpiryDate(e) {
            this.businessForm.rental_expiry_date = e.format('YYYY/MM/DD');
        },
        selectEtrustCertificateIssueDateDate(e) {
            this.businessForm.etrust_certificate_issue_date = e.format('YYYY/MM/DD');
        },
        selectEtrustCertificateExpiryDate(e) {
            this.businessForm.etrust_certificate_expiry_date = e.format('YYYY/MM/DD');
        },
        submitBusinessForm() {
            this.businessForm.profile_id = this.profileId;
            this.businessForm.has_license = this.hasLicense;
            this.resetFileUploadErrors();
            this.businessForm.post(route('dashboard.profiles.businesses.update', {profile: this.profile.id})).then(response => {

            })
        },

        viewCategories() {
            this.viewCategoriesModal = true;
        },

        closeCategoriesModal() {
            this.viewCategoriesModal = false;
        },
        selectCategory(category) {
            this.businessForm.business_category_code = category.category_id;
            this.businessForm.business_subCategory_code = category.id;
            this.closeCategoriesModal();
        }
    }
}
</script>

<style scoped>

</style>
