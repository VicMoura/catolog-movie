<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-300 mb-1">
            {{ label }}
        </label>
        <input :id="id" :type="type" :value="modelValue" :placeholder="placeholder" :required="required" class="w-full px-4 py-3 border border-gray-600 bg-gray-700 text-white rounded-md
             focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @input="$emit('update:modelValue', $event.target.value)" />
    </div>
</template>

<script setup>
defineProps({
    id: String,
    label: String,
    type: { type: String, default: 'text' },
    modelValue: String,
    placeholder: String,
    required: Boolean,
})
</script>
