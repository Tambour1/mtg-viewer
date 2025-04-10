<script setup>
import { onMounted, ref, watch } from 'vue';
import { fetchAllCards, fetchSetCodes } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const selectedSetCode = ref('');
const setCodes = ref([]);

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards(selectedSetCode.value);
    loadingCards.value = false;
}

async function loadSetCodes() {
    setCodes.value = await fetchSetCodes();
}

watch(selectedSetCode, () => {
    loadCards();
});

onMounted(() => {
    loadCards();
    loadSetCodes();
});
</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
        <select v-model="selectedSetCode">
            <option value="">Tous les sets</option>
            <option v-for="setCode in setCodes" :key="setCode" :value="setCode">{{ setCode }}</option>
        </select>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
</template>
