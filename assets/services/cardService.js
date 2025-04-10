export async function fetchAllCards(setCode = '') {
    const params = new URLSearchParams();
    if (setCode) {
        params.append('setCode', setCode);
    }
    const response = await fetch(`/api/card/all?${params.toString()}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchSearchCards(query, setCode = '') {
    const params = new URLSearchParams();
    params.append('query', query);
    if (setCode) {
        params.append('setCode', setCode);
    }
    const response = await fetch(`/api/card/search?${params.toString()}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/setCodes');
    if (!response.ok) throw new Error('Failed to fetch set codes');
    const result = await response.json();
    return result;
}
