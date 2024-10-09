<template>
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4 text-green-400">Manage Topics</h2>
        <ul class="space-y-2">
            <li v-for="topic in topics" :key="topic.arn"
                class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">name:</span>
                    <span class="text-white">{{ topic.arn }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="viewMessages(topic)"
                            class="text-blue-400 hover:text-blue-300 transition-colors duration-200 text-sm"
                            alt="Ver mensagens">
                        <svg class="w-6 h-6 text-blue-200 dark:text-white" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                        </svg>
                    </button>
                    <button @click="$emit('removeTopic', topic.arn)"
                            class="text-red-400 hover:text-red-300 transition-colors duration-200" alt="Remover tópico">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </li>
        </ul>
        <div class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
            <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Topic</h3>
            <div class="flex space-x-2">
                <input
                    v-model="newTopicName"
                    type="text"
                    placeholder="Enter topic name"
                    class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                />
                <button @click="addTopic"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
                    Add Topic
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps(['topics']);
const emit = defineEmits(['addTopic', 'removeTopic', 'viewMessages']);

const newTopicName = ref('');

const addTopic = () => {
    if (newTopicName.value.trim() !== '') {
        emit('addTopic', newTopicName.value);
        newTopicName.value = '';
    }
};

const viewMessages = (topic) => {
    emit('viewMessages', topic);
};
</script>
