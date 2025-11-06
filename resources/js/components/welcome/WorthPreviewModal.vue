<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { computed } from 'vue';

export type ComparableProperty = {
    id: string;
    address: string;
    value: number;
};

const props = defineProps<{
    open: boolean;
    address?: string;
    estimatedValue?: number;
    comps?: ComparableProperty[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'appraise'): void;
}>();

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
});

const formattedEstimate = computed(() =>
    props.estimatedValue
        ? currencyFormatter.format(props.estimatedValue)
        : undefined,
);

const displayComps = computed(() => props.comps ?? []);

const handleOpenChange = (value: boolean) => {
    emit('update:open', value);
};
</script>

<template>
    <Dialog :open="props.open" @update:open="handleOpenChange">
        <DialogContent
            class="w-[90vw] max-w-lg rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl"
        >
            <Card class="border-0">
                <CardHeader
                    class="border-b border-slate-100 bg-gradient-to-b from-slate-50 to-white px-6 py-5"
                >
                    <DialogTitle
                        class="text-left text-lg font-semibold text-slate-900"
                    >
                        {{ props.address }}
                    </DialogTitle>
                    <DialogDescription class="text-left text-sm text-slate-500">
                        Quick PixrWorth preview
                    </DialogDescription>
                </CardHeader>

                <CardContent class="grid gap-6 px-6 py-6">
                    <section class="grid gap-1.5">
                        <p
                            class="text-xs font-medium tracking-wide text-slate-500 uppercase"
                        >
                            Estimated Value
                        </p>
                        <p class="text-3xl font-semibold text-slate-900">
                            {{ formattedEstimate ?? 'Coming soon' }}
                        </p>
                        <p class="text-sm text-slate-500">
                            Estimated using recent sales nearby. Connect
                            HouseCanary for live data.
                        </p>
                    </section>

                    <section class="grid gap-3">
                        <p
                            class="text-xs font-medium tracking-wide text-slate-500 uppercase"
                        >
                            Comparable Properties
                        </p>

                        <ul class="grid gap-2">
                            <li
                                v-for="comp in displayComps"
                                :key="comp.id"
                                class="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50 px-4 py-3 text-sm text-slate-700"
                            >
                                <span class="font-medium">{{
                                    comp.address
                                }}</span>
                                <span class="font-semibold text-slate-900">
                                    {{ currencyFormatter.format(comp.value) }}
                                </span>
                            </li>
                        </ul>

                        <p
                            v-if="displayComps.length === 0"
                            class="text-sm text-slate-500"
                        >
                            Comparable properties will appear here when
                            available.
                        </p>
                    </section>
                </CardContent>

                <DialogFooter
                    class="flex flex-col gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4"
                >
                    <Button class="w-full" size="lg" @click="emit('appraise')">
                        Appraise Full Property
                    </Button>
                </DialogFooter>
            </Card>
        </DialogContent>
    </Dialog>
</template>
