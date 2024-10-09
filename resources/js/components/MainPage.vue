<template>
    <div class="bg-gray-800 min-h-screen">
        <div class="container mx-auto p-4 bg-gray-900 text-gray-100 min-h-screen font-quicksand text-sm">
            <h1 class="text-3xl font-bold mb-6 text-blue-400">Localstack UI</h1>

            <div class="flex space-x-4 mb-6">
                <button v-for="tab in tabs" :key="tab" @click="setCurrentTab(tab)"
                        :class="{'text-blue-400': currentTab === tab, 'text-gray-400': currentTab !== tab}"
                        class="px-3 py-2 rounded-md focus:outline-none">
                    {{ tab }}
                </button>
            </div>

            <div v-if="currentTab === 'Topics'">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Topics</h2>
                <div v-if="!topics.length" class="bg-amber-900 p-4 rounded-lg shadow-md">
                    <p class="text-gray-400">No topics found</p>
                </div>
                <ul v-else class="space-y-2">
                    <li v-for="topic in topics" :key="topic.arn"
                        class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                        <span class="text-white">{{ topic.arn }}</span>
                        <button @click="removeTopic(topic.arn)"
                                class="text-red-400 hover:text-red-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                 fill="red">
                                <path
                                    d="M3 6h18v2H3V6zm2 3h14v13H5V9zm3 2v9h2v-9H8zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM9 4V2h6v2h5v2H4V4h5z"/>
                            </svg>
                        </button>
                    </li>
                </ul>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
                    <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Topic</h3>
                    <div class="flex space-x-2">
                        <input v-model="newTopicName" type="text" placeholder="Enter topic name"
                               class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"/>
                        <button @click="addTopic(newTopicName)"
                                class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
                            Add Topic
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="currentTab === 'Queues'">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Queues</h2>
                <div v-if="!queues.length" class="bg-amber-900 p-4 rounded-lg shadow-md">
                    <p class="text-gray-400">No queues found</p>
                </div>
                <ul class="space-y-2" v-else>
                    <li v-for="queue in queues" :key="queue.arn"
                        class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                        <div class="flex items-center">
                            <span class="text-xs text-gray-500 mr-1">name:</span>
                            <span class="text-white">{{ queue.arn }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="selectQueue(queue)"
                                    class="text-blue-400 hover:text-blue-300 transition-colors duration-200 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                     fill="blue">
                                    <rect x="1" y="1" width="22" height="22" stroke="blue" fill="none"
                                          stroke-width="2"/>
                                    <line x1="4" y1="6" x2="20" y2="6" stroke="blue" stroke-width="2"/>
                                    <line x1="4" y1="12" x2="20" y2="12" stroke="blue" stroke-width="2"/>
                                    <line x1="4" y1="18" x2="20" y2="18" stroke="blue" stroke-width="2"/>
                                </svg>
                            </button>
                            <button @click="removeQueue(queue.url)"
                                    class="text-red-400 hover:text-red-300 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                     fill="red">
                                    <path
                                        d="M3 6h18v2H3V6zm2 3h14v13H5V9zm3 2v9h2v-9H8zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM9 4V2h6v2h5v2H4V4h5z"/>
                                </svg>
                            </button>
                        </div>
                    </li>
                </ul>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
                    <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Queue</h3>
                    <div class="flex space-x-2">
                        <input v-model="newQueueName" type="text" placeholder="Enter queue name"
                               class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"/>
                        <button @click="addQueue(newQueueName)"
                                class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
                            Add Queue
                        </button>
                    </div>
                </div>

                <div v-if="selectedQueue" class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
                    <h3 class="text-lg font-semibold mb-3 text-purple-400">Messages in {{ selectedQueue.name }}</h3>
                    <div class="bg-gray-800 rounded-lg shadow-md mt-4">
                        <ul class="space-y-2 max-h-80 overflow-y-auto">
                            <li v-for="(message, index) in queueMessages" :key="index"
                                class="bg-gray-700 p-2 rounded-lg shadow-md">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-semibold text-yellow-300 text-xs">
                                        {{ message.message_id }}
                                        <small class="font-xs text-gray-200">({{ message.created_at }})</small>
                                    </span>
                                    <button @click="removeMessage(index)"
                                            class="text-red-400 hover:text-red-300 transition-colors duration-200 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                             height="24"
                                             fill="red">
                                            <path
                                                d="M3 6h18v2H3V6zm2 3h14v13H5V9zm3 2v9h2v-9H8zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM9 4V2h6v2h5v2H4V4h5z"/>
                                        </svg>
                                    </button>
                                </div>
                                <pre class="text-xs text-gray-300 whitespace-pre-wrap">{{ message.body }}</pre>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="currentTab === 'Subscriptions'">
                <h2 class="text-xl font-semibold mb-4 text-green-400">Subscriptions</h2>
                <ul class="space-y-2">
                    <li v-for="subscription in subscriptions" :key="subscription.SubscriptionId"
                        class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                        <span class="text-white flex items-center space-x-2">
                        <small class="text-gray-400">topic:</small>
                        <span class="font-semibold">{{ subscription.topic }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"
                             class="text-blue-400">
                            <path d="M10 6l6 6-6 6" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <small class="text-gray-400">queue:</small>
                        <span class="font-semibold">{{ subscription.queue }}</span>
                    </span>
                        <button @click="deleteSubscription(subscription)"
                                class="text-red-400 hover:text-red-300 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                                 fill="red">
                                <path
                                    d="M3 6h18v2H3V6zm2 3h14v13H5V9zm3 2v9h2v-9H8zm4 0v9h2v-9h-2zm4 0v9h2v-9h-2zM9 4V2h6v2h5v2H4V4h5z"/>
                            </svg>
                        </button>
                    </li>
                </ul>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
                    <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Subscription</h3>
                    <div class="flex space-x-2">
                        <select v-model="newSubscriptionQueue"
                                class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Choose a queue</option>
                            <option v-for="queue in queues" :key="queue.arn" :value="queue">{{ queue.name }}</option>
                        </select>
                        <select v-model="newSubscriptionTopic"
                                class="flex-grow px-2 py-1.5 bg-gray-700 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Choose a topic</option>
                            <option v-for="topic in topics" :key="topic.arn" :value="topic">{{ topic.name }}</option>
                        </select>
                        <button
                            @click="createSubscription({ queue: newSubscriptionQueue, topic: newSubscriptionTopic })"
                            class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-200 text-sm">
                            Add Subscription
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted, watch} from 'vue';
import axios from 'axios';

const tabs = ['Topics', 'Queues', 'Subscriptions'];
const currentTab = ref(localStorage.getItem('currentTab') || 'Topics');

const topics = ref([]);
const queues = ref([]);
const subscriptions = ref([]);
const selectedQueue = ref(null);
const queueMessages = ref([]);

const newTopicName = ref('');
const newQueueName = ref('');
const newSubscriptionQueue = ref('');
const newSubscriptionTopic = ref('');

const setCurrentTab = (tab) => {
    currentTab.value = tab;
    localStorage.setItem('currentTab', tab);
    if (tab === 'Queues') {
        selectedQueue.value = null;
        queueMessages.value = [];
    }
};

const fetchTopics = async () => {
    try {
        const response = await axios.get('/api/topics');
        topics.value = response.data;
    } catch (error) {
        console.error('Error fetching topics:', error);
    }
};

const fetchQueues = async () => {
    try {
        const response = await axios.get('/api/queues');
        queues.value = response.data.map(queue => ({
            name: queue.name,
            url: queue.url,
            arn: queue.arn
        }));
    } catch (error) {
        console.error('Error fetching queues:', error);
    }
};

const fetchSubscriptions = async () => {
    try {
        const response = await axios.get('/api/subscriptions');
        subscriptions.value = response.data;
    } catch (error) {
        console.error('Error fetching subscriptions:', error);
    }
};

const addTopic = async (name) => {
    try {
        await axios.post('/api/topics', {name});
        await fetchTopics();
        newTopicName.value = '';
    } catch (error) {
        console.error('Error adding topic:', error);
    }
};

const addQueue = async (name) => {
    try {
        await axios.post('/api/queues', {name});
        await fetchQueues();
    } catch (error) {
        console.error('Error adding queue:', error);
    }
};

const removeQueue = async (queueUrl) => {
    try {
        await axios.post('/api/queues/delete', {queueUrl});
        await fetchQueues();
        if (selectedQueue.value && selectedQueue.value.url === queueUrl) {
            selectedQueue.value = null;
            queueMessages.value = [];
        }
    } catch (error) {
        console.error('Error removing queue:', error);
    }
};

const removeTopic = async (name) => {
    try {
        await axios.delete(`/api/topics/${name.split(':').pop()}`);
        await fetchTopics();
    } catch (error) {
        console.error('Error removing topic:', error);
    }
};

const selectQueue = async (queue) => {
    selectedQueue.value = queue;
    await fetchQueueMessages(queue);
};

const fetchQueueMessages = async (queue) => {
    try {
        const response = await axios.get(`/api/queues/${queue.name}/messages`);
        queueMessages.value = response.data;
    } catch (error) {
        console.error('Error fetching queue messages:', error);
    }
};

const addMessage = async (message) => {
    try {
        await axios.post(`/api/queues/${selectedQueue.value}/messages`, {content: message});
        await fetchQueueMessages(selectedQueue.value);
    } catch (error) {
        console.error('Error adding message:', error);
    }
};

const removeMessage = async (index) => {
    try {
        const messageId = queueMessages.value[index].id;
        await axios.delete(`/api/messages/${messageId}`);
        await fetchQueueMessages(selectedQueue.value);
    } catch (error) {
        console.error('Error removing message:', error);
    }
};

const createSubscription = async (subscription) => {
    try {
        await axios.post('/api/subscriptions', {
            queue: subscription.queue.arn,
            topic: subscription.topic.arn
        });
        await fetchSubscriptions();
        newSubscriptionQueue.value = '';
        newSubscriptionTopic.value = '';
    } catch (error) {
        console.error('Error adding subscription:', error);
    }
};

const deleteSubscription = async (subscription) => {
    try {
        await axios.post('/api/subscriptions/delete', subscription);

        await fetchSubscriptions();
    } catch (error) {
        console.error('Error deleting subscription:', error);
    }
};

onMounted(async () => {
    await fetchTopics();
    await fetchQueues();
    await fetchSubscriptions();

    await Echo.channel('queues').listen('QueueCreated', (event) => {
        if (currentTab.value === 'Queues') fetchQueues();
    });

    await Echo.channel('queues').listen('QueueDeleted', (event) => {
        if (currentTab.value === 'Queues') fetchQueues();
    });

    await Echo.channel('topics').listen('TopicCreated', (event) => {
        if (currentTab.value === 'Topics') fetchTopics();
    });

    await Echo.channel('messages').listen('MessageReceived', (event) => {
        if (currentTab.value === 'Queues') fetchQueueMessages(selectedQueue.value);
    });
});

watch(currentTab, async (newTab) => {
    if (newTab === 'Topics') await fetchTopics();
    if (newTab === 'Queues') await fetchQueues();
    if (newTab === 'Subscriptions') await fetchSubscriptions();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

body {
    font-family: 'Quicksand', sans-serif;
}

.font-quicksand {
    font-family: 'Quicksand', sans-serif;
}
</style>
