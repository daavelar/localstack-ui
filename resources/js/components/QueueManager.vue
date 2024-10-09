<template>
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4 text-green-400">Manage Queues</h2>
        <ul class="space-y-2">
            <li v-for="queue in queues" :key="queue.arn"
                class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                <div class="flex items-center">
                    <span class="text-xs text-gray-500 mr-1">name:</span>
                    <span class="text-white">{{ queue.arn }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="selectQueue(queue)"
                            class="text-blue-200 hover:text-blue-600 transition-colors duration-200 text-sm" alt="">
                        <svg class="w-6 h-6 text-blue-300 dark:text-white" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                        </svg>

                    </button>
                    <button @click="$emit('removeQueue', queue.arn)"
                            class="text-red-400 hover:text-red-300 transition-colors duration-200">
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
            <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Queue</h3>
            <div class="flex space-x-2">
                <input
                    v-model="newQueueName"
                    type="text"
                    placeholder="Enter queue name"
                    class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                />
                <button @click="addQueue"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
                    Add Queue
                </button>
            </div>
        </div>
        <MessageList v-if="selectedQueue"
                     :selectedQueue="selectedQueue"
                     :messages="messages"
                     @addMessage="addMessage"
                     @removeMessage="removeMessage"/>
    </div>
</template>

<script setup>
import {ref} from 'vue';
import MessageList from './MessageList.vue';
import axios from 'axios';

const props = defineProps(['queues']);
const emit = defineEmits(['addQueue', 'removeQueue', 'selectQueue']);

const newQueueName = ref('');
const selectedQueue = ref(null);
const messages = ref([]);

const addQueue = () => {
    if (newQueueName.value.trim() !== '') {
        emit('addQueue', newQueueName.value);
        newQueueName.value = '';
    }
};

const selectQueue = async (queue) => {
    selectedQueue.value = queue;
    messages.value = [];

    try {
        const response = await axios.get(`/api/queues/${queue.name}/messages`);
        messages.value = response.data.messages;
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
};

const addMessage = (message) => {
    messages.value.push(message);
};

const removeMessage = (index) => {
    messages.value.splice(index, 1);
};
</script>

<style scoped>
.loader {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 4px solid #fff;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
