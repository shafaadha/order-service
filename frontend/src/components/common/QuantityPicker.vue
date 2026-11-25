<script setup>
import { faMinus, faPlus } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    modelValue: { type: Number, default: 1 },
    minValue: { type: Number, default: 1 },
    maxValue: { type: Number, default: 99 },
});

const emit = defineEmits(["update:modelValue"]);

const decrease = () => {
    if (props.modelValue > props.minValue) {
        emit("update:modelValue", props.modelValue - 1);
    }
};

const increase = () => {
    if (props.modelValue < props.maxValue) {
        emit("update:modelValue", props.modelValue + 1);
    }
};
</script>

<template>
    <div class="flex items-center w-28 h-8 text-gray-700 border border-stone-200 rounded-lg overflow-hidden">
        <button type="button" @click="decrease"
            class="w-8 h-full flex items-center justify-center border-r border-stone-200 hover:bg-gray-100">
            <FontAwesomeIcon :icon="faMinus" class="text-xs" />
        </button>

        <span class="flex-1 text-center text-xs">
            {{ modelValue }}
        </span>

        <button type="button" @click="increase"
            class="w-8 h-full flex items-center justify-center border-l border-stone-200 hover:bg-gray-100">
            <FontAwesomeIcon :icon="faPlus" class="text-xs" />
        </button>
    </div>
</template>
