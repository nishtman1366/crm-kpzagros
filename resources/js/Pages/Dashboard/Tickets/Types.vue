<template>
    <Dashboard>
        <template #breadcrumb> / پشتیبانی / واحد ها</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="">
                                <jet-button
                                    class="float-left"
                                    @click.native="newType">
                                    ثبت واحد جدید
                                </jet-button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام واحد
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="type in types" :key="type.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{type.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(type.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{type.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editType(type)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.tickets.types.destroy',{id:type.id})"
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

            <jet-confirmation-modal :show="submitTypeModal" @close="submitTypeModal = false">
                <template #title>
                    {{editTypeModal ? 'ویرایش اطلاعات واحد' : 'ثبت واحد جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <input type="text"
                                   v-model="typeForm.name"
                                   placeholder="نام واحد"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="typeForm.error('name')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <button v-on:click="typeForm.status=1"
                                    :class="typeForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="typeForm.status=0"
                                    :class="typeForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="typeForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitTypeModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editTypeModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditType"
                                :class="{ 'opacity-25': typeForm.processing }"
                                :disabled="typeForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewType"
                                :class="{ 'opacity-25': typeForm.processing }"
                                :disabled="typeForm.processing">
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
        name: "Types",
        components: {Dashboard, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            types: Array,
        },
        data() {
            return {
                submitTypeModal: false,
                editTypeModal: false,
                editTypeId: '',
                typeForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                }, {
                    bag: 'typeForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {

        },
        methods: {
            newType() {
                this.editTypeModal = false;
                this.typeForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                }, {
                    bag: 'typeForm',
                    resetOnSuccess: true
                });
                this.submitTypeModal = true;
            },
            editType(type) {
                this.typeForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: type.name,
                    status: type.status,
                }, {
                    bag: 'typeForm',
                    resetOnSuccess: true
                });
                this.editTypeId = type.id;
                this.submitTypeModal = true;
                this.editTypeModal = true;
            },
            submitNewType() {
                this.typeForm.post(route('dashboard.tickets.types.store')).then(response => {
                    if (!this.typeForm.hasErrors()) {
                        this.submitTypeModal = false;
                    }
                })
            },
            submitEditType() {
                this.typeForm.psps = this.pspList;
                this.typeForm.post(route('dashboard.tickets.types.update', {id: this.editTypeId})).then(response => {
                    if (!this.typeForm.hasErrors()) {
                        this.submitTypeModal = false;
                        this.editTypeModal = false;
                    }
                })
            },
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 0:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
