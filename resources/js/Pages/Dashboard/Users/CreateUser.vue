<template>
    <Dashboard>
        <template #breadcrumb> / ثبت {{userType}} جدید</template>
        <template #dashboardContent>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0 text-justify">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات کاربر جدید</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به کاربر جدید را وارد نمایید.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت نام کاربری، یک نام کاربری که از حروف انگلیسی تشکیل شده است را نماید. نام کاربری
                                نباید قبلا برای کاربر دیگری استفاده شده باشد.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت کلمه عبور، یک کلمه عبور برای کاربر جدید وارد نماید. بهتر است کلمه عبور انتخابی
                                پیچیدگی لازم جهت جلوگیری از رخنه های امنیتی را داشته باشد.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت تلفن تماس، شماره تلفن تماس کاربر جدید وارد نماید. بهتر است در این قسمت شماره
                                موبایل کاربر را وارد نمایید که در صورت نیاز اطلاع رسانی از طریق پیامک صورت گیرد.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-3 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            نام:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام"
                                               ref="name"
                                               id="name"
                                               v-model="userForm.name"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('name')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="mobile" class="block text-sm font-medium text-gray-700">
                                            تلفن تماس:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن تماس"
                                               ref="mobile"
                                               id="mobile"
                                               v-model="userForm.mobile"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('mobile')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="username" class="block text-sm font-medium text-gray-700">
                                            نام کاربری:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کاربری"
                                               ref="username"
                                               id="username"
                                               v-model="userForm.username"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('username')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            کلمه عبور:
                                        </label>
                                        <input type="password"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کلمه عبور"
                                               ref="password"
                                               id="password"
                                               v-model="userForm.password"
                                               @keyup.enter.native="submitUserForm"/>
                                        <jet-input-error :message="userForm.error('password')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="$page.user.level==='ADMIN' && type==='marketer'"
                                         class="col-2 sm:col-span-2">
                                        <label for="parent_id" class="block text-sm font-medium text-gray-700">
                                            نماینده:
                                        </label>
                                        <select id="parent_id" name="parent_id" ref="parent_id"
                                                v-model="userForm.parent_id"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value="$page.user.id">خودم</option>
                                            <option v-for="agent in agents" :key="agent.id"
                                                    :value="agent.id">
                                                {{agent.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="userForm.error('parent_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="status" class="block text-sm font-medium text-gray-700">
                                            وضعیت:
                                        </label>
                                        <select id="status" name="status" ref="status"
                                                v-model="userForm.status"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option v-for="status in statuses" :key="status.id"
                                                    :value="status.id">
                                                {{status.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="userForm.error('status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-6 sm:col-span-6 text-left">
                                        <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                v-on:click="submitUserForm">
                                            ذخیره اطلاعات
                                        </button>
                                    </div>
                                </div>
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
    import JetButton from '@/Jetstream/Button';
    import JetInputError from '@/Jetstream/InputError';

    export default {
        name: "CreateUser",
        components: {Dashboard, JetButton, JetInputError},
        props: {
            type: String,
            userType: String,
            statuses: String,
            agents: Array,
        },
        data() {
            return {
                imageFiles: {
                    imageFilePreview: '',
                },
                userForm: this.$inertia.form({
                    '_method': 'POST',
                    parent_id: this.$page.user.id,
                    name: '',
                    username: '',
                    password: '',
                    mobile: '',
                    status: 1,
                }, {
                    bag: 'userForm',
                    resetOnSuccess: false
                })
            }
        },
        methods: {
            submitUserForm() {
                this.userForm.post(route('dashboard.users.store', {type: this.type}), {
                    preserveScroll: true
                }).then(response => {

                })
            }
        }
    }
</script>

<style scoped>

</style>
