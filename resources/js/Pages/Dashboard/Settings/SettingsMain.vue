<template>
    <Dashboard>
        <template #breadcrumb> / تنظیمات{{title}}</template>
        <template #dashboardContent>
            <div class="p-3 bg-gray-300">
                <div>
                    <slot name="settings"></slot>
                    <jet-form-section v-if="!slotIsLoaded" @submitted="submitUpdateSettingsForm">
                        <template #title>
                            تنظیمات سیستم
                        </template>

                        <template #description>
                            <p class="text-justify my-2">در این بخش میتوانید تمام فاکتورهای قابل تنظیم در سامانه را به
                                دلخواه خود تغییر دهید.</p>
                            <p class="text-justify my-2">تیتر صفحات: عبارتی که در این کادر قرار میگیرد در بالای تمامی
                                صفحات سامانه نمایش داده خواهد شد.</p>
                            <p class="text-justify my-2">نام شرکت: عبارتی که در این کادر قرار میگیرد در بالای صفحه ورود
                                به کاربران نمایش داده خواهد شد.</p>
                            <p class="text-justify my-2">لوگوی شرکت: در این بخش میتوانید تصویر آرم یا لوگوی شرکت خود را
                                انتخاب کنید که در بالای صفحات و همچنین بالای فرم ورود نمایش داده خواهد شد.</p>
                            <p class="text-justify my-2">وضعیت سامانه: بوسیله این قسمت میتوانید سامانه را موقتا غیرفعال
                                نمایید.</p>
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="PAGE_TITLE" value="تیتر صفحات"/>
                                <jet-input id="PAGE_TITLE"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateSettings.PAGE_TITLE"
                                           ref="PAGE_TITLE"
                                           autocomplete="PAGE_TITLE"/>
                                <jet-input-error :message="updateSettings.error('PAGE_TITLE')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="COMPANY_NAME" value="نام شرکت"/>
                                <jet-input id="COMPANY_NAME"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateSettings.COMPANY_NAME"
                                           ref="COMPANY_NAME"
                                           autocomplete="COMPANY_NAME"/>
                                <jet-input-error :message="updateSettings.error('COMPANY_NAME')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="COMPANY_LOGO" value="لوگوی شرکت"/>
                                <div class="mt-2" v-show="updateSettings.COMPANY_LOGO">
                                    <img :src="updateSettings.COMPANY_LOGO" alt="Current Profile Photo"
                                         class="rounded-full h-20 w-20 object-cover">
                                </div>
                                <div class="mt-2" v-show="photoPreview">
                                    <span class="block rounded-full w-20 h-20"
                                          :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                                    </span>
                                </div>
                                <input type="file" class="hidden"
                                       ref="photo"
                                       @change="updateLogoPreview">
                                <jet-secondary-button class="mt-2 mr-2" type="button"
                                                      @click.native.prevent="selectNewPhoto">
                                    انتخاب لوگوی شرکت
                                </jet-secondary-button>

                                <jet-secondary-button type="button" class="mt-2" @click.native.prevent="deletePhoto"
                                                      v-if="updateSettings.COMPANY_LOGO">
                                    حذف تصویر کنونی
                                </jet-secondary-button>
                                <jet-input-error :message="updateSettings.error('COMPANY_LOGO')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="STATUS" value="وضعیت سامانه"/>
                                <jet-button type="button"
                                            @click.native="updateSettings.STATUS=1"
                                            :class="{ 'bg-green-500': updateSettings.STATUS==1 }"
                                            class="bg-green-300 hover:bg-green-700">فعال
                                </jet-button>
                                <jet-button type="button"
                                            @click.native="updateSettings.STATUS=0"
                                            :class="{ 'bg-red-500': updateSettings.STATUS==0 }"
                                            class="bg-red-300 hover:bg-red-700">غیرفعال
                                </jet-button>
                                <jet-input-error :message="updateSettings.error('STATUS')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="MAXIMUM_UPLOAD_SIZE"
                                           value="حداکثر سایز فایل های های ارسالی به کیلوبایت"/>
                                <jet-input id="MAXIMUM_UPLOAD_SIZE"
                                           type="text"
                                           class="mt-1 block"
                                           v-model="updateSettings.MAXIMUM_UPLOAD_SIZE"
                                           ref="COMPANY_NAME"
                                           autocomplete="MAXIMUM_UPLOAD_SIZE"/>
                                <jet-input-error :message="updateSettings.error('MAXIMUM_UPLOAD_SIZE')" class="mt-2"/>
                            </div>
                        </template>
                        <template #actions>
                            <jet-action-message :on="updateSettings.recentlySuccessful" class="mr-3">
                                ذخیره شد.
                            </jet-action-message>

                            <jet-button :class="{ 'opacity-25': updateSettings.processing }"
                                        :disabled="updateSettings.processing">
                                ذخیره
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>
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

    export default {
        name: "SettingsMain",
        components: {
            Dashboard,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton
        },
        props: {
            active: {
                type: String,
                default: 'main'
            },
            title: {
                type: String,
                default: ' / تنظیمات سیستم'
            },
        },
        data() {
            return {
                updateSettings: this.$inertia.form({
                    '_method': 'PUT',
                    PAGE_TITLE: this.$page.configs.pageTitle,
                    COMPANY_NAME: this.$page.configs.companyName,
                    COMPANY_LOGO: this.$page.configs.companyLogo,
                    STATUS: this.$page.configs.status,
                    MAXIMUM_UPLOAD_SIZE: this.$page.configs.maximumUploadSize,
                    deleteLogo: false,
                }, {
                    bag: 'updateSettings',
                }),
                photoPreview: null,
            }
        },
        methods: {
            submitUpdateSettingsForm() {
                if (this.$refs.photo) {
                    this.updateSettings.COMPANY_LOGO = this.$refs.photo.files[0]
                }

                this.updateSettings.post(route('dashboard.settings.update'), {
                    preserveScroll: true
                }).then(() => {
                    if (!this.updateSettings.hasErrors()) {
                        window.location.reload();
                    }
                });
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updateLogoPreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.photo.files[0]);
            },

            deletePhoto() {
                this.updateSettings.COMPANY_LOGO = ''
                this.updateSettings.deleteLogo = true
            },
        },
        computed: {
            slotIsLoaded() {
                return this.$slots.settings;
            }
        }
    }
</script>

<style scoped>

</style>
