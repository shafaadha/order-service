<script setup>
defineProps({
    text: {
        type: String,
        default: "Button",
    },
    type: {
        type: String,
        default: "button",
    },
    className: {
        type: String,
        default: "bg-blue-500 hover:bg-blue-600 text-white",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["click"]);
</script>

<template>
    <button :type="type" :disabled="disabled" @click="emit('click')" :class="[
        'px-3 py-1.5 rounded-lg transition-colors duration-200',
        className,
        disabled && 'opacity-50 cursor-not-allowed',
    ]">
        {{ text }}
    </button>
</template>
