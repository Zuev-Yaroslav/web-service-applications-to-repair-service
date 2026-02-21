<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    status?: string;
};

defineProps<Props>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col bg-background p-6 lg:items-center lg:justify-center lg:p-8"
    >
        <Head title="Create Request" />

        <header class="mb-6 w-full max-w-md lg:max-w-lg">
            <nav class="flex items-center justify-end">
                <Link
                    :href="route('home')"
                    class="text-sm text-muted-foreground underline-offset-4 hover:underline"
                >
                    Home
                </Link>
            </nav>
        </header>

        <main class="w-full max-w-md space-y-6 lg:max-w-lg">
            <h1 class="text-xl font-semibold">Create Request</h1>

            <Form
                :action="route('request-record.store')"
                method="post"
                class="space-y-6"
                v-slot="{ errors, processing, wasSuccessful }"
            >
                <div class="grid gap-2">
                    <Label for="client_name">Client</Label>
                    <Input
                        id="client_name"
                        type="text"
                        name="client_name"
                        required
                        placeholder="Client name"
                    />
                    <InputError :message="errors.client_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        type="text"
                        name="phone"
                        required
                        placeholder="Phone"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="address">Address</Label>
                    <Input
                        id="address"
                        type="text"
                        name="address"
                        required
                        placeholder="Address"
                    />
                    <InputError :message="errors.address" />
                </div>

                <div class="grid gap-2">
                    <Label for="problem_text">Description</Label>
                    <textarea
                        id="problem_text"
                        name="problem_text"
                        required
                        rows="4"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        placeholder="Problem description"
                    />
                    <InputError :message="errors.problem_text" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Creating...' : 'Create' }}
                    </Button>

                    <p
                        v-if="wasSuccessful || status === 'Created'"
                        class="text-sm text-green-600"
                    >
                        Created.
                    </p>
                </div>
            </Form>
        </main>
    </div>
</template>
