<template>
    <div class="w-full border-b border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
            <tr>
                <th scope="col"
                    class="list-table-header-cell">
                    نام پذیرنده
                </th>
                <th scope="col"
                    class="list-table-header-cell">
                    کد ملی
                </th>
                <th scope="col"
                    class="list-table-header-cell">
                    تلفن همراه
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <tr>
                <td class="list-table-body-cell">
                    {{
                        device && device.terminal && device.terminal.profile && device.terminal.profile.customer && device.terminal.profile.customer.fullName
                    }}
                </td>
                <td class="list-table-body-cell">
                    {{
                        device && device.terminal && device.terminal.profile && device.terminal.profile.customer && device.terminal.profile.customer.national_code
                    }}
                </td>
                <td class="list-table-body-cell">
                    {{
                        device && device.terminal && device.terminal.profile && device.terminal.profile.customer && device.terminal.profile.customer.mobile
                    }}
                </td>
            </tr>
            <tr>
                <th class="list-table-header-cell" colspan="3">دستگاه‌های پذیرنده</th>
            </tr>
            <tr>
                <th scope="col"
                    class="list-table-header-cell">
                    سرویس‌دهنده
                </th>
                <th scope="col"
                    class="list-table-header-cell">
                    مدل دستگاه
                </th>
                <th scope="col"
                    class="list-table-header-cell">
                    سریال دستگاه
                </th>
            </tr>
            <tr>
                <td class="list-table-body-cell">
                    {{ device && device.terminal && device.terminal.profile && device.terminal.profile.psp && device.terminal.profile.psp.name }}
                </td>
                <td class="list-table-body-cell">
                    {{ device && device.device_type && device.device_type.name }}
                </td>
                <td class="list-table-body-cell">
                    {{ device && device.serial }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-left">
                    <jet-button @click.native="selectDevice(device)">ثبت اطلاعات</jet-button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetButton from "@/Jetstream/Button";
import JetLabel from "@/Jetstream/Label";

export default {
    name: "Device",
    components: {
        JetInput,
        JetInputError,
        JetButton,
        JetLabel,
    },
    props: {
        device: Object
    },
    data() {
        return {}
    },
    methods: {
        selectDevice(device) {
            this.$emit('selectDevice', device);
        }
    }
}
</script>

<style scoped>

</style>
