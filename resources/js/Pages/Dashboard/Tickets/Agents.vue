<template>
    <Dashboard>
        <template #breadcrumb> / پشتیبانی / کاربران</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="">
                                <jet-button
                                    class="float-left"
                                    @click.native="newAgent">
                                    ثبت کاربر جدید
                                </jet-button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام کاربر
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        واحد پشتیبانی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="agent in agents" :key="agent.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{agent.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{agent.type && agent.type.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(agent.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{agent.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editAgent(agent)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.tickets.agents.destroy',{id:agent.id})"
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

            <jet-confirmation-modal :show="submitAgentModal" @close="submitAgentModal = false">
                <template #title>
                    {{editAgentModal ? 'ویرایش اطلاعات کاربر' : 'ثبت کاربر جدید'}}
                </template>
                <template #content>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <jet-label for="user_id" value="کاربر"/>
                            <select id="user_id" size="1"
                                    v-model="agentForm.user_id"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                                <option :value="null" v-text=""/>
                                <option v-for="user in users" :key="user.id" :value="user.id" v-text="user.name"/>
                            </select>
                            <jet-input-error :message="agentForm.error('user_id')"/>
                        </div>
                        <div>
                            <jet-label for="ticket_type_id" value="واحد پشتیبانی"/>
                            <select id="ticket_type_id" size="1"
                                    v-model="agentForm.ticket_type_id"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                                <option :value="null" v-text=""/>
                                <option v-for="type in types" :key="type.id" :value="type.id" v-text="type.name"/>
                            </select>
                            <jet-input-error
                                :message="agentForm.error('ticket_type_id')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <jet-label value="وضعیت"/>
                            <button v-on:click="agentForm.status=1"
                                    :class="agentForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="agentForm.status=0"
                                    :class="agentForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="agentForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitAgentModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editAgentModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditAgent"
                                :class="{ 'opacity-25': agentForm.processing }"
                                :disabled="agentForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewAgent"
                                :class="{ 'opacity-25': agentForm.processing }"
                                :disabled="agentForm.processing">
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
    import JetInput from '@/Jetstream/Input';
    import JetLabel from '@/Jetstream/Label';
    import JetInputError from '@/Jetstream/InputError';
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "Agents",
        components: {
            Dashboard,
            JetInput,
            JetLabel,
            JetButton,
            JetInputError,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton
        },
        props: {
            types: Array,
            agents: Array,
            users: Array,
        },
        data() {
            return {
                submitAgentModal: false,
                editAgentModal: false,
                editAgentId: null,
                agentForm: this.$inertia.form({
                    '_method': 'POST',
                    user_id: null,
                    ticket_type_id: null,
                    status: 1,
                }, {
                    bag: 'agentForm',
                    resetOnSuccess: true
                }),
            }
        },
        methods: {
            newAgent() {
                this.editAgentModal = false;
                this.agentForm.reset();
                this.submitAgentModal = true;
            },
            editAgent(agent) {
                this.agentForm.user_id = agent.user_id;
                this.agentForm.ticket_type_id = agent.ticket_type_id;
                this.agentForm.status = agent.status;
                this.editAgentId = agent.id;
                this.submitAgentModal = true;
                this.editAgentModal = true;
            },
            submitNewAgent() {
                this.agentForm.post(route('dashboard.tickets.agents.store')).then(response => {
                    if (!this.agentForm.hasErrors()) {
                        this.submitAgentModal = false;
                    }
                })
            },
            submitEditAgent() {
                this.agentForm.put(route('dashboard.tickets.agents.update', {id: this.editAgentId})).then(response => {
                    if (!this.agentForm.hasErrors()) {
                        this.submitAgentModal = false;
                        this.editAgentModal = false;
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
