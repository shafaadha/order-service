<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faCircleCheck,
    faCircleInfo,
    faCircleExclamation,
    faCircleXmark,
    faXmark,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    type: {
        type: String,
        default: "Success",
        validator: (value) =>
            ["Success", "Error", "Warning", "Info"].includes(value),
    },
    title: {
        type: String,
        default: "",
    },
    message: {
        type: String,
        required: true,
    },
    duration: {
        type: Number,
        default: 4000,
    },
});

const emit = defineEmits(["close"]);

const visible = ref(true);
let timer = null;

const closeAlert = () => {
    visible.value = false;
    emit("close");
};

const resetTimer = () => {
    clearTimeout(timer);

    visible.value = true;
    if (props.duration > 0) {
        timer = setTimeout(closeAlert, props.duration);
    }
}

watch(
    () => props.message,
    () => {
        resetTimer();
    }
);

const alertConfig = {
    Success: {
        icon: faCircleCheck,
        class: "border-green-500 bg-green-50 text-green-700",
    },
    Error: {
        icon: faCircleXmark,
        class: "border-red-500 bg-red-50 text-red-700",
    },
    Warning: {
        icon: faCircleExclamation,
        class: "border-amber-500 bg-amber-50 text-amber-700",
    },
    Info: {
        icon: faCircleInfo,
        class: "border-blue-400 bg-blue-50 text-blue-700",
    },
};

const currentAlert = computed(
    () => alertConfig[props.type] ?? alertConfig.Info
);


onMounted(() => {
    resetTimer();
});

onUnmounted(() => {
    clearTimeout(timer);
});
</script>

<template>
    <div v-if="visible" class="flex items-start justify-between gap-3 rounded-lg border p-4" :class="currentAlert.class"
        role="alert">
        <div class="flex items-start gap-3">
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

        <button type="button" @click="closeAlert" class="shrink-0 opacity-60 transition hover:opacity-100"
            aria-label="Tutup notifikasi">
            <FontAwesomeIcon :icon="faXmark" />
        </button>
    </div>
</template>
