<script setup>
import { ref, watch, onMounted } from 'vue';
import { fetchAllCards, fetchSearchCards, fetchSetCodes } from '../services/cardService.js';

const cards = ref([]);
const loadingCards = ref(true);
const searchQuery = ref('');
const selectedSetCode = ref('');
const setCodes = ref([]);

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards(selectedSetCode.value);
    loadingCards.value = false;
}

async function searchCards() {
    if (searchQuery.value.length < 3) {
        return;
    }
    loadingCards.value = true;
    cards.value = await fetchSearchCards(searchQuery.value, selectedSetCode.value);
    loadingCards.value = false;
}

async function loadSetCodes() {
    setCodes.value = await fetchSetCodes();
}

watch(searchQuery, () => {
    if (searchQuery.value.length >= 3) {
        searchCards();
    } else if (searchQuery.value.length === 0) {
        loadCards();
    }
});

watch(selectedSetCode, () => {
    if (searchQuery.value.length >= 3) {
        searchCards();
    } else {
        loadCards();
    }
});

onMounted(() => {
    loadCards();
    loadSetCodes();
});
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
        <input v-model="searchQuery" placeholder="Rechercher par nom de carte" />
        <select v-model="selectedSetCode">
            <option value="">Tous les sets</option>
            <option v-for="setCode in setCodes" :key="setCode" :value="setCode">{{ setCode }}</option>
        </select>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} - {{ card.uuid }}
                </router-link>
            </div>
        </div>
    </div>
</template>
