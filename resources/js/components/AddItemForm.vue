<template>
    <div class="mt-4 flex space-x-2">
        <input
            v-model="newItemName"
            type="text"
            :placeholder="placeholder"
            class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
        />
        <button @click="addItem"
                class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
            {{ buttonText }}
        </button>
    </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps(['placeholder', 'buttonText']);
const emit = defineEmits(['addItem']);

const newItemName = ref('');

const addItem = () => {
    if (newItemName.value.trim() !== '') {
        emit('addItem', newItemName.value);
        newItemName.value = '';
    }
};
</script>
