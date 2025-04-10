<script setup>
import { ref, watch } from 'vue';
import { fetchAllCards, fetchSearchCards } from '../services/cardService.js';

const cards = ref([]);
const loadingCards = ref(true);
const searchQuery = ref('');

async function loadCards() {
    loadingCards.value = true;
    cards.value = await fetchAllCards();
    loadingCards.value = false;
}

async function searchCards() {
    if (searchQuery.value.length < 3) {
        return;
    }
    loadingCards.value = true;
    cards.value = await fetchSearchCards(searchQuery.value);
    loadingCards.value = false;
}

watch(searchQuery, () => {
    if (searchQuery.value.length >= 3) {
        searchCards();
    } else if (searchQuery.value.length === 0) {
        loadCards();
    }
});

loadCards();
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
        <input v-model="searchQuery" placeholder="Rechercher par nom de carte" />
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
