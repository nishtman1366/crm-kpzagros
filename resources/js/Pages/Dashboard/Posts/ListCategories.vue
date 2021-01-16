<template>
    <Dashboard>
        <template #breadcrumb> / اخبار و اطلاعیه ها / دسته بندی ها</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 sm:col-span-4">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newCategory">
                                        ثبت دسته بندی جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام دسته بندی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تعداد مقالات
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="category in categories" :key="category.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{category.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{category.posts_count}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editCategory(category)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.posts.categories.destroy',{categoryId:category.id})"
                                                     class="text-red-600 hover:text-red-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <jet-confirmation-modal :show="submitCategoryModal" @close="submitCategoryModal = false">
                <template #title>
                    {{editCategoryModal ? 'ویرایش اطلاعات دسته بندی' : 'ثبت دسته بندی جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <input type="text"
                                   v-model="categoryForm.name"
                                   placeholder="نام دسته بندی"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="categoryForm.error('name')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitCategoryModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editCategoryModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditCategory"
                                :class="{ 'opacity-25': categoryForm.processing }"
                                :disabled="categoryForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewCategory"
                                :class="{ 'opacity-25': categoryForm.processing }"
                                :disabled="categoryForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button'
    import JetInputError from '@/Jetstream/InputError';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "ListCategories",
        components: {Dashboard, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            categories: Array,
        },
        data() {
            return {
                query: '',
                submitCategoryModal: false,
                editCategoryModal: false,
                editCategoryId: '',
                categoryForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'categoryForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            newCategory() {
                this.editCategoryModal = false;
                this.categoryForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'categoryForm',
                    resetOnSuccess: true
                });
                this.submitCategoryModal = true;
            },
            editCategory(category) {
                this.categoryForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: category.name,
                }, {
                    bag: 'categoryForm',
                    resetOnSuccess: true
                });
                this.editCategoryId = category.id;
                this.submitCategoryModal = true;
                this.editCategoryModal = true;
            },
            submitNewCategory() {
                this.categoryForm.post(route('dashboard.posts.categories.store')).then(response => {
                    if (!this.categoryForm.hasErrors()) {
                        this.submitCategoryModal = false;
                    }
                })
            },
            submitEditCategory() {
                this.categoryForm.psps = this.pspList;
                this.categoryForm.post(route('dashboard.posts.categories.update', {categoryId: this.editCategoryId})).then(response => {
                    if (!this.categoryForm.hasErrors()) {
                        this.submitCategoryModal = false;
                        this.editCategoryModal = false;
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>
