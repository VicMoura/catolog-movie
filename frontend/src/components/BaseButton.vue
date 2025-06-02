<template>
    <button :type="type" :disabled="disabled" @click="$emit('click')" :class="buttonClasses">
        <slot />
    </button>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
    type: { type: String, default: 'button' },
    disabled: { type: Boolean, default: false },
    variant: { type: String, default: 'primary' }
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
    const base =
        'w-full font-semibold py-3 rounded-md transition duration-200 flex items-center justify-center'
    if (props.variant === 'primary') {
        return base + ' bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white'
    } else if (props.variant === 'secondary') {
        return base + ' bg-gray-600 hover:bg-gray-700 text-white'
    }
    return base
})
</script>
