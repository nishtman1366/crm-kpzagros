<template>
    <Dashboard>
        <template #breadcrumb> / ثبت درخواست / بررسی اطلاعات {{profile.customer.fullName}}</template>
        <template #dashboardContent>
            <ProfileSteps :step="5"
                          :customer-info="!!profile.customer"
                          :customer-id="profile.customer ? profile.customer.id : ''"
                          :business-info="!!profile.business"
                          :accounts-info="profile.accounts.length > 0"
                          :device-info="!!profile.device_type"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false"
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
                                    <span class="text-xs text-gray-600">{{error.jDate}}</span>
                                    <p :class="'text-'+error.color+'-900'">{{error.title}}</p>
                                    <p>{{error.message}}</p>
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
                            {{error}}
                        </p>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-indigo-600 text-lg m-2">اطلاعات مشتری</p>
                                        <div v-if="profile.customer.type==='ORGANIZATION'"
                                             class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-1">نام شرکت</div>
                                            <div class="col-1 sm:col-span-2">{{profile.customer.company_name}}</div>
                                            <div class="col-1 sm:col-span-2">نام انگلیسی شرکت</div>
                                            <div class="col-1 sm:col-span-3">
                                                {{profile.customer.company_name_english}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">نام تجاری</div>
                                            <div class="col-1 sm:col-span-3">
                                                {{profile.customer.business_name}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">تاریخ ثبت</div>
                                            <div class="col-1 sm:col-span-1">
                                                {{profile.customer.jRegDate}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">شماره ثبت</div>
                                            <div class="col-1 sm:col-span-1">
                                                {{profile.customer.reg_code}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">شناسه ملی</div>
                                            <div class="col-1 sm:col-span-1">
                                                {{profile.customer.company_national_code}}
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
                                        <div v-if="profile.customer.type==='ORGANIZATION'"
                                             class="col-1 sm:col-span-2 md:col-span-4"
                                             aria-hidden="true">
                                            <div class="py-5">
                                                <div
                                                    class="border-t border-gray-200"></div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-1">نام</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.first_name}}</div>
                                            <div class="col-1 sm:col-span-1">نام انگلیسی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.first_name_english}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">نام خانوادگی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.last_name}}</div>
                                            <div class="col-1 sm:col-span-1">نام خانوادگی انگلیسی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.last_name_english}}
                                            </div>
                                            <div class="col-1 sm:col-span-1">نام پدر</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.father}}</div>
                                            <div class="col-1 sm:col-span-1">نام پدر انگلیسی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.father_english}}</div>
                                            <div class="col-1 sm:col-span-1">کد ملی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.national_code}}</div>
                                            <div class="col-1 sm:col-span-1">شماره شناسنامه</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.id_code}}</div>
                                            <div class="col-1 sm:col-span-1">تاریخ تولد</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.jBirthday}}</div>
                                            <div class="col-1 sm:col-span-1">جنسیت</div>
                                            <div class="col-1 sm:col-span-1">{{profile.customer.genderText}}</div>
                                            <div class="col-1 sm:col-span-8"></div>
                                            <div class="col-1 text-center text-indigo-600 sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.nationalCard1Url">
                                                    <img :src="profile.customer.nationalCard1Url" class="w-full">
                                                    تصویر روی کارت ملی
                                                </a>
                                            </div>
                                            <div class="col-1 text-center text-indigo-600  sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.nationalCard2Url">
                                                    <img :src="profile.customer.nationalCard2Url" class="w-full">
                                                    تصویر پشت کارت ملی
                                                </a>
                                            </div>
                                            <div class="col-1 text-center text-indigo-600  sm:col-span-2">
                                                <a target="_blank" :href="profile.customer.idCardUrl">
                                                    <img :src="profile.customer.idCardUrl" class="w-full">
                                                    تصویر شناسنامه
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-indigo-600 text-ld m-2">اطلاعات کسب و کار</p>
                                        <div class="grid  grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-1">استان</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.ostan}}</div>
                                            <div class="col-1 sm:col-span-1">شهرستان</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.shahrestan}}</div>
                                            <div class="col-1 sm:col-span-1">بخش</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.bakhsh}}</div>
                                            <div class="col-1 sm:col-span-1">شهر</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.shahr}}</div>
                                            <div class="col-1 sm:col-span-1">نام</div>
                                            <div class="col-2 sm:col-span-2">{{profile.business.name}}</div>
                                            <div class="col-1 sm:col-span-1">نام انگلیسی</div>
                                            <div class="col-2 sm:col-span-2">{{profile.business.name_english}}</div>
                                            <div class="col-1 sm:col-span-1">صنف مرتبط</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.senf}}</div>
                                            <div class="col-1 sm:col-span-1">تلفن تماس</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.fullPhone}}</div>
                                            <div class="col-1 sm:col-span-1">آدرس</div>
                                            <div class="col-5 sm:col-span-5">{{profile.business.address}}</div>
                                            <div class="col-1 sm:col-span-1">کد پستی</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.postal_code}}</div>
                                            <div class="col-1 sm:col-span-1">جواز کسب</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.has_license}}</div>
                                            <div class="col-1 sm:col-span-1">شماره جواز</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.license_code}}</div>
                                            <div class="col-1 sm:col-span-1">تاریخ جواز</div>
                                            <div class="col-1 sm:col-span-1">{{profile.business.jLicenseDate}}</div>
                                            <div class="col-1 sm:col-span-8"></div>
                                            <div v-if="profile.business.has_license==='YES'"
                                                 class="col-1 text-center text-indigo-600 sm:col-span-2">
                                                <a target="_blank" :href="profile.business.licenseFile">
                                                    <img :src="profile.business.licenseFile" class="w-full">
                                                    تصویر جواز کسب
                                                </a>
                                            </div>
                                            <div v-else class="col-1 text-center text-indigo-600  sm:col-span-2">
                                                <a target="_blank" :href="profile.business.esteshhadFile">
                                                    <img :src="profile.business.esteshhadFile" class="w-full">
                                                    تصویر استشهادنامه
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-indigo-600 text-lg m-2">اطلاعات حساب های بانکی</p>
                                        <div v-for="account in profile.accounts"
                                             class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-1">بانک</div>
                                            <div class="col-1 sm:col-span-1">{{account.account.bank.name}}</div>
                                            <div class="col-1 sm:col-span-1">کد شعبه</div>
                                            <div class="col-1 sm:col-span-1">{{account.account.branch}}</div>
                                            <div class="col-1 sm:col-span-1">شماره حساب</div>
                                            <div class="col-3 sm:col-span-3">{{account.account.account_number}}</div>
                                            <div class="col-span-2 sm:col-span-1">شماره شبا</div>
                                            <div class="col-span-2 sm:col-span-7">{{account.account.shebaText}}</div>
                                            <div class="col-1 sm:col-span-1">صاحب حساب</div>
                                            <div class="col-2 sm:col-span-2">{{account.account.fullName}}</div>
                                            <div class="col-1 sm:col-span-1">کدملی</div>
                                            <div class="col-1 sm:col-span-1">{{account.account.national_code}}</div>
                                            <div class="col-1 sm:col-span-1">تاریخ تولد</div>
                                            <div class="col-2 sm:col-span-2">{{account.account.jBirthday}}</div>
                                            <div class="col-1 sm:col-span-1">شماره موبایل</div>
                                            <div class="col-1 sm:col-span-1">{{account.account.mobile}}</div>
                                            <div class="col-1 sm:col-span-8"></div>
                                            <div class="col-1 text-center text-indigo-600 sm:col-span-2">
                                                <a target="_blank" :href="account.account.shebaFile">
                                                    <img :src="account.account.shebaFile" class="w-full">
                                                    تصویر تاییدیه شبا
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-indigo-600 text-lg m-2">اطلاعات دستگاه</p>
                                        <div class="grid  grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 sm:col-span-1">نوع ارتباط</div>
                                            <div class="col-1 sm:col-span-1">{{profile.device_type.type.name}}</div>
                                            <div class="col-1 sm:col-span-1">مدل دستگاه</div>
                                            <div class="col-1 sm:col-span-1">{{profile.device_type.name}}</div>
                                            <div class="col-1 sm:col-span-1">سریال دستگاه</div>
                                            <div class="col-1 sm:col-span-3">
                                                {{profile.device ? profile.device.serial : 'تخصیص نیافته'}}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ شروع گارانتی</div>
                                            <div class="col-1 sm:col-span-2">{{profile.device ?
                                                profile.device.guarantee_start : 'ثبت نشده'}}
                                            </div>
                                            <div class="col-1 sm:col-span-2">تاریخ پایان گارانتی</div>
                                            <div class="col-1 sm:col-span-2">{{profile.device ?
                                                profile.device.guarantee_end : 'ثبت نشده'}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <p class="text-indigo-600 text-lg m-2">اطلاعات شاپرک</p>
                                        <div class="grid grid-cols-2 md:grid-cols-8 gap-3">
                                            <div class="col-1 self-center sm:col-span-2">شرکت ارائه دهنده (PSP)</div>
                                            <div class="col-1 sm:col-span-6">{{profile.psp ? profile.psp.name :
                                                'نامشخص'}}
                                            </div>
                                            <div class="col-1 self-center sm:col-span-1">شماره پایانه</div>
                                            <div
                                                v-if="profile.status===6 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='AGENT')"
                                                class="col-1 sm:col-span-2">
                                                <input type="text"
                                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                       placeholder="شماره ترمینال"
                                                       ref="terminal_id"
                                                       id="terminal_id"
                                                       v-model="profileForm.terminal_id"/>
                                                <jet-input-error :message="profileForm.error('terminal_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div v-else class="col-1 sm:col-span-2">{{profile.terminal_id ?
                                                profile.terminal_id
                                                : 'تخصیص نیافته'}}
                                            </div>
                                            <div class="col-1 self-center sm:col-span-1">شماره پذیرنده</div>
                                            <div
                                                v-if="profile.status===6 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='AGENT')"
                                                class="col-1 sm:col-span-2">
                                                <input type="text"
                                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                       placeholder="شماره پذیرنده"
                                                       ref="merchant_id"
                                                       id="merchant_id"
                                                       v-model="profileForm.merchant_id"/>
                                                <jet-input-error :message="profileForm.error('merchant_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div v-else class="col-1 sm:col-span-2">{{profile.merchant_id ?
                                                profile.merchant_id
                                                : 'تخصیص نیافته'}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid grid-cols-2 md:grid-cols-8 gap-2">
                                    <div class="col-span-2 sm:col-span-8">
                                        <p class="text-indigo-600 text-lg m-2">مدارک پرونده</p>
                                    </div>
                                    <div v-for="license in profile.licenses" :key="license.id"
                                         class="text-center text-indigo-600 sm:col-span-2">
                                        <a target="_blank" :href="license.url">
                                            <img :src="license.url" class="w-full">
                                            {{license.type.name}}
                                        </a>
                                        <InertiaLink
                                            v-if="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false"
                                            :href="route('dashboard.profiles.licenses.destroy',{profileId:profile.id,licenseId:license.id})"
                                            method="DELETE"
                                            class="block text-red-600 hover:text-red-400">حذف این تصویر
                                        </InertiaLink>
                                    </div>
                                    <div class="col-span-2 sm:col-span-8">
                                        <jet-section-border/>
                                    </div>
                                    <div v-if="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false" class="col-span-2 sm:col-span-8">
                                        <p class="text-indigo-600 text-lg m-2">
                                            ارسال مدارک
                                        </p>
                                    </div>
                                    <div v-if="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false" class="col-span-2 sm:col-span-5">
                                        <div>
                                            <label for="license_type_id">نوع مدرک</label>
                                            <select name="license_type_id"
                                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                    id="license_type_id"
                                                    v-model="uploadLicenseForm.license_type_id">
                                                <option v-for="type in licenseTypes" :key="type.id" :value="type.id">
                                                    {{type.name}}
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
                                                    {{account.account.bank.name}} - {{account.account.account_number}}
                                                </option>
                                            </select>
                                            <p class="text-sm text-red-500">چنانچه مدرک ارسالی، تصویر تاییدیه شبا می
                                                باشد حتما شماره حساب مرتبط با آن را انتخاب نمایید.</p>
                                            <jet-input-error
                                                :message="uploadLicenseForm.error('account_id')"
                                                class="mt-2"/>
                                        </div>
                                    </div>
                                    <div v-if="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false" class="col-span-2 sm:col-span-3">
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
                                    <div v-if="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false" class="col-span-2 sm:col-span-8 text-left">
                                        <jet-button @click.native="submitLicenseFile">ارسال فایل</jet-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-6 sm:col-span-6">
                                        <div class="col-6 sm:col-span-6 text-left">
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
                                                       v-if="profile.status===2 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN')"
                                                       class="bg-red-600 hover:bg-red-800">
                                                عدم تایید به علت نقص مدارک
                                            </JetButton>
                                            <JetButton @click.native="updateProfileInfo(3)"
                                                       v-if="profile.status===2 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN')"
                                                       class="bg-green-600 hover:bg-green-800">
                                                تایید مدارک
                                            </JetButton>
                                            <!-- تغییر وضعیت پرونده توسط مدیر به تایید شده توسط شاپرک یا ردشده توسط شاپرک -->
                                            <JetButton @click.native="openErrorModal(11)"
                                                       v-if="profile.status===4 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN')"
                                                       class="bg-red-600 hover:bg-red-800">
                                                عدم تایید شاپرک
                                            </JetButton>
                                            <JetButton @click.native="updateProfileInfo(5)"
                                                       v-if="profile.status===4 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN')"
                                                       class="bg-indigo-600 hover:bg-indigo-800">
                                                تایید توسط شاپرک
                                            </JetButton>
                                            <!-- تغییر وضعیت پرونده توسط بازاریاب جهت بررسی مجدد -->
                                            <JetButton @click.native="updateProfileInfo(1)"
                                                       v-if="profile.status===10"
                                                       class="bg-yellow-600 hover:bg-yellow-800">ارسال جهت بررسی مجدد
                                            </JetButton>
                                            <JetButton @click.native="updateProfileInfo(4)"
                                                       v-if="profile.status===11"
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

        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
    import JetButton from '@/Jetstream/Button';
    import JetInputError from '@/Jetstream/InputError';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton';
    import JetSectionBorder from '@/Jetstream/SectionBorder'

    export default {
        name: "CreateProfile",
        components: {
            Dashboard,
            ProfileSteps,
            JetButton,
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
            licenseTypes: Array,
        },
        data() {
            return {
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
            }
        },
        mounted() {
            this.profile.messages.reverse();
        },
        methods: {
            openErrorModal(status) {
                this.viewErrorModal = true;
                this.temporaryStatus = status
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
        }
    }
</script>

<style scoped>

</style>
