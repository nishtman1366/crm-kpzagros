<template>
    <Dashboard>
        <template #breadcrumb>مدارک ارسال شده از اپلیکیشن</template>
        <template #dashboardContent>
            <div>
               <div class="flex items-center justify-between">
                   <div class="">
                       <jet-label value="وضعیت"/>
                       <jet-button :class="status_id===0 ? 'bg-green-700 text-white' : 'bg-green-200 text-green-700'"
                                   class="hover:text-white"
                                   @click.native="status_id=0;submitSearchForm()">ارسال شده
                       </jet-button>
                       <jet-button :class="status_id===1 ? 'bg-green-700 text-white' : 'bg-green-200 text-green-700'"
                                   class="hover:text-white"
                                   @click.native="status_id=1;submitSearchForm()">بررسی شده
                       </jet-button>
                   </div>
                   <div class="flex items-center justify-between">
                       <div class=" flex items-center justify-between space-x-reverse space-x-2">
                           <jet-label for="per_page" value="تعداد در صفحه"/>
                           <select id="per_page"
                                   name="per_page"
                                   ref="per_page"
                                   v-model="per_page"
                                   autocomplete="per_page"
                                   v-on:change="submitSearchForm"
                                   title="تعداد در صفحه"
                                   v-b-tooltip.hover
                                   class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                               <option v-for="i in [5,10,25,50,100,250,500]" :key="i"
                                       :value="i">{{ i }}
                               </option>
                           </select>
                       </div>
                   </div>
               </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th scope="col"                            class="list-table-header-cell">
                            نام پذیرنده
                        </th>
                        <th scope="col"
                            class="list-table-header-cell">
                            وضعیت
                        </th>
                        <th scope="col"
                            class="list-table-header-cell">
                            زمان ثبت
                        </th>
                        <th scope="col"
                            class="list-table-header-cell">
                            آخرین تغییرات
                        </th>
                        <th scope="col" class="py-3 bg-gray-50">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template v-for="item in licenses.data">
                        <tr :key="item.id">
                            <td class="list-table-body-cell">
                                {{ item.profile && item.profile.customer && item.profile.customer.fullName }}
                                <p
                                    class="text-indigo-600">({{ item.profile && item.profile.customer && item.profile.customer.national_code }})</p>
                            </td>
                            <td class="list-table-body-cell">
                                        <span
                                            :class="statusColors(item.status)"
                                            class="badge">
                                          {{ item.statusText }}
                                        </span>
                            </td>
                            <td class="list-table-body-cell">{{ item.jCreatedAt }}</td>
                            <td class="list-table-body-cell">{{ item.jUpdatedAt }}</td>
                            <td class="list-table-body-cell flex flex-nowrap items-center justify-center space-x-2 space-x-reverse">
                                <InertiaLink
                                    :href="route('dashboard.profiles.view',{profile: item.profile_id})"
                                    class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                    <button title="مشاهده پرونده"
                                            v-b-tooltip.hover>
                                        <i class="material-icons">folder_shared</i>
                                    </button>
                                </InertiaLink>
                                <InertiaLink method="PUT" v-if="item.status===0"
                                    :href="route('dashboard.profiles.userUploadedLicenses.update',{license: item.id})"
                                    class="tooltip-box text-green-600 hover:text-green-900">
                                    <button title="بررسی شده"
                                            v-b-tooltip.hover>
                                        <i class="material-icons">check</i>
                                    </button>
                                </InertiaLink>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
                <pagination
                    :urlsArray="paginatedLinks"
                    :totalRows="licenses.total"
                    :previousPageUrl="licenses.prev_page_url"
                    :nextPageUrl="licenses.next_page_url"/>
            </div>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import Pagination from "@/Pages/Dashboard/Components/Pagination";
import {Inertia} from "@inertiajs/inertia";
import JetButton from "@/Jetstream/Button";
import JetLabel from "@/Jetstream/Label";

export default {
    name: "UploadedLicenses",
    components: {
        Dashboard, JetConfirmationModal,
        Pagination, JetButton,JetLabel
    },
    props: {
        licenses: Object,
        perPage: Number,
        paginatedLinks: Array,
        status: Number
    },
    data() {
        return {
            status_id: this.status,
            per_page: this.perPage,
        }
    },
    methods: {
        statusColors(status) {
            switch (status) {
                case 0:
                    return 'badge-yellow';
                case 1:
                    return 'badge-green';
            }
        },
        submitSearchForm(status) {
            Inertia.visit(route('dashboard.profiles.userUploadedLicenses.list'), {
                method: 'get',
                data: {
                    perPage: this.per_page,
                    status: this.status_id,
                },
            })
        },
    }
}
</script>

<style scoped>

</style>
