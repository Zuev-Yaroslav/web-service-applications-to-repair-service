<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import Select from 'primevue/select';
import type { BreadcrumbItem } from '@/types';

type RequestRecord = {
    id: number;
    client_name: string;
    phone: string;
    address: string;
    problem_text: string;
    status: 'new' | 'assigned' | 'in_progress' | 'done' | 'canceled';
    assigned_to: number | null;
    assigned_to_user?: {
        id: number;
        name: string;
    } | null;
};

type Master = {
    id: number;
    name: string;
};

type Props = {
    requestRecords: RequestRecord[];
    masters: Master[] | null;
    role: 'dispatcher' | 'master';
    statusFilter?: string | null;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Request Panel',
        href: route('request-record-panel.index'),
    },
];

const statusFilter = ref<string | null>(props.statusFilter || null);

const filteredRecords = computed(() => {
    if (!statusFilter.value) {
        return props.requestRecords;
    }
    return props.requestRecords.filter((record) => record.status === statusFilter.value);
});

const getStatusColor = (status: string): string => {
    switch (status) {
        case 'new':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'assigned':
            return 'bg-blue-200 text-blue-900 dark:bg-blue-800 dark:text-blue-100';
        case 'in_progress':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'done':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'canceled':
            return 'bg-gray-600 text-white dark:bg-gray-800 dark:text-gray-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
    }
};

const statusOptions = [
    { label: 'Assigned', value: 'assigned' },
    { label: 'Canceled', value: 'canceled' },
];

const filterOptions = [
    { label: 'All', value: null },
    { label: 'New', value: 'new' },
    { label: 'Assigned', value: 'assigned' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Done', value: 'done' },
    { label: 'Canceled', value: 'canceled' },
];

const updateStatus = (recordId: number, status: object) => {
    router.patch(
        `/request-record-panel/${recordId}/status`,
        { status: status.value },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const assignToMaster = (recordId: number, masterId: number) => {
    router.post(
        `/request-record-panel/${recordId}/assign`,
        { master_id: masterId },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const startWork = (recordId: number) => {
    router.post(
        `/request-record-panel/${recordId}/start-work`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const finish = (recordId: number) => {
    router.post(
        `/request-record-panel/${recordId}/finish`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const applyFilter = () => {
    const queryParams: Record<string, string | null> = {};
    if (statusFilter.value !== null) {
        queryParams.status = statusFilter.value;
    }

    router.get(
        route('request-record-panel.index'),
        queryParams,
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};
</script>

<template>
    <Head :title="role === 'dispatcher' ? 'Dispatch Panel' : 'Master Panel'" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">
                    {{ role === 'dispatcher' ? 'Dispatch Panel' : 'Master Panel' }}
                </h1>
                <div v-if="role === 'dispatcher'" class="flex items-center gap-4">
                    <Select
                        v-model="statusFilter"
                        :options="filterOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Filter by status"
                        class="w-48"
                        @change="applyFilter"
                    />
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-muted">
                            <th class="border p-3 text-left font-semibold">Client</th>
                            <th class="border p-3 text-left font-semibold">Phone</th>
                            <th class="border p-3 text-left font-semibold">Address</th>
                            <th class="border p-3 text-left font-semibold">Problem</th>
                            <th class="border p-3 text-left font-semibold">Status</th>
                            <th v-if="role === 'dispatcher'" class="border p-3 text-left font-semibold">
                                Change Status
                            </th>
                            <th v-if="role === 'dispatcher'" class="border p-3 text-left font-semibold">
                                Assign To
                            </th>
                            <th v-if="role === 'master'" class="border p-3 text-left font-semibold">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="record in filteredRecords"
                            :key="record.id"
                            class="hover:bg-muted/50"
                        >
                            <td class="border p-3">{{ record.client_name }}</td>
                            <td class="border p-3">{{ record.phone }}</td>
                            <td class="border p-3">{{ record.address }}</td>
                            <td class="border p-3">{{ record.problem_text }}</td>
                            <td class="border p-3">
                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold',
                                        getStatusColor(record.status),
                                    ]"
                                >
                                    {{ record.status }}
                                </span>
                            </td>
                            <td v-if="role === 'dispatcher'" class="border p-3">
                                <Select
                                    :model-value="record.status"
                                    :options="statusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Select status"
                                    class="w-full"
                                    @change="(value) => updateStatus(record.id, value)"
                                />
                            </td>
                            <td v-if="role === 'dispatcher'" class="border p-3">
                                <div class="space-y-2">
                                    <Select
                                        v-if="masters"
                                        :model-value="record.assigned_to"
                                        :options="masters"
                                        optionLabel="name"
                                        optionValue="id"
                                        placeholder="Select master"
                                        class="w-full"
                                        @update:model-value="(value) => {
                                            if (value) {
                                                assignToMaster(record.id, value);
                                            }
                                        }"
                                    >
                                        <template #option="slotProps">
                                            <div>{{ slotProps.option.name }} ({{ slotProps.option.email }})</div>
                                        </template>
                                    </Select>
                                    <Button
                                        v-if="masters && record.assigned_to"
                                        size="sm"
                                        @click="assignToMaster(record.id, record.assigned_to)"
                                    >
                                        Assign
                                    </Button>
                                </div>
                            </td>
                            <td v-if="role === 'master'" class="border p-3">
                                <div class="flex gap-2">
                                    <Button
                                        v-if="record.status === 'assigned'"
                                        size="sm"
                                        @click="startWork(record.id)"
                                    >
                                        Assign to work
                                    </Button>
                                    <Button
                                        v-if="record.status === 'in_progress'"
                                        size="sm"
                                        variant="default"
                                        @click="finish(record.id)"
                                    >
                                        Finish
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredRecords.length === 0">
                            <td :colspan="role === 'dispatcher' ? 7 : 6" class="border p-6 text-center text-muted-foreground">
                                No requests found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
