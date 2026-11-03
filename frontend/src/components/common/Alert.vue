<script setup>
import { computed } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faCircleCheck,
    faCircleInfo,
    faCircleExclamation,
    faCircleXmark,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    type: {
        type: String,
        default: "success",
        validator: (value) =>
            ["success", "error", "warning", "info"].includes(value),
    },
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        required: true,
    },
});

const alertConfig = {
    success: {
        icon: faCircleCheck,
        class: "border-green-500 bg-green-50 text-green-700",
    },
    error: {
        icon: faCircleXmark,
        class: "border-red-500 bg-red-50 text-red-700",
    },
    warning: {
        icon: faCircleExclamation,
        class: "border-amber-500 bg-amber-50 text-amber-700",
    },
    info: {
        icon: faCircleInfo,
        class: "border-blue-400 bg-blue-50 text-blue-700",
    },
};

const currentAlert = computed(
    () => alertConfig[props.type] ?? alertConfig.info
);
</script>

<template>
    <div class="flex items-start gap-3 rounded-lg border p-4" :class="currentAlert.class" role="alert">
        <FontAwesomeIcon :icon="currentAlert.icon" class="mt-1 shrink-0" />

        <div class="flex flex-col gap-1">
            <p v-if="title" class="font-semibold">
                {{ title }}
            </p>

            <p class="text-sm">
                {{ message }}
            </p>
        </div>
    </div>
</template>
