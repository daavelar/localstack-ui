<template>
    <div class="mb-4 flex space-x-2 overflow-x-auto">
        <button
            v-for="tab in tabs"
            :key="tab"
            @click="$emit('setTab', tab)"
            :class="[
        'px-3 py-1.5 rounded-md transition-colors duration-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500',
        currentTab === tab ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
      ]"
            :ref="el => { if (currentTab === tab) tabRefs[tab] = el }"
        >
            {{ tab }}
        </button>
    </div>
</template>

<script setup>
import {ref, watch, onMounted} from 'vue';

const props = defineProps(['tabs', 'currentTab']);
const emit = defineEmits(['setTab']);

const tabRefs = ref({});

const focusCurrentTab = () => {
    if (tabRefs.value[props.currentTab]) {
        tabRefs.value[props.currentTab].focus();
    }
};

watch(() => props.currentTab, focusCurrentTab);

onMounted(focusCurrentTab);
</script>
