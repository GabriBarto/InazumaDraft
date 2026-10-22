const formations = {
    '4-4-2 Rombo': [
        { role: 'FW', count: 2 },
        { role: 'MF', count: 3 },
        { role: 'DF', count: 4 },
        { role: 'GK', count: 1 }
    ],
    '4-4-2 In Linea': [
        { role: 'FW', count: 2 },
        { role: 'MF', count: 4 },
        { role: 'DF', count: 4 },
        { role: 'GK', count: 1 }
    ],
    '3-5-2 Libertà': [
        { role: 'FW', count: 2 },
        { role: 'MF', count: 5 },
        { role: 'DF', count: 3 },
        { role: 'GK', count: 1 }
    ],
    '4-3-3 Triangolo': [
        { role: 'FW', count: 3 },
        { role: 'MF', count: 3 },
        { role: 'DF', count: 4 },
        { role: 'GK', count: 1 }
    ],
    '4-3-3 Delta': [
        { role: 'FW', count: 3 },
        { role: 'MF', count: 4 },
        { role: 'DF', count: 3 },
        { role: 'GK', count: 1 }
    ],
    '4-5-1 Equilibrata': [
        { role: 'FW', count: 1 },
        { role: 'MF', count: 5 },
        { role: 'DF', count: 4 },
        { role: 'GK', count: 1 }
    ],
    '3-6-1 Esagono': [
        { role: 'FW', count: 1 },
        { role: 'MF', count: 6 },
        { role: 'DF', count: 3 },
        { role: 'GK', count: 1 }
    ],
    '5-4-1 Centrocampo raddoppiato': [
        { role: 'FW', count: 1 },
        { role: 'MF', count: 4 },
        { role: 'DF', count: 5 },
        { role: 'GK', count: 1 }
    ]
};


const teamTranslations = {
    "Inazuma National": "Inazuma Japan",
    "Neo National": "Neo Japan",
    "Royal Academy Redux": "Absolute Royal Academy",
    "Little Gigantes": "I Piccoli Giganti",
    "Queen's Knights": "I Cavalieri della Regina",
    "The Empire": "L'Impero",
    "Unicorn": "Liocorno",
    "Orpheus": "Orfeo",
    "The Kingdom": "Il Regno",
    "Team D": "Squadra K",
    "Team Ogre": "Ogre",
    "Devil Army Z": "Esercito del Demone Z",
    "Apostles from the Sky": "Apostoli del Cielo",
    "The Sherwinds": "I Sherwind (Tenmas)",
    "Mount Olympus": "Monte Olimpo",
    "Zanark's Domain": "Dominio di Zanark",
    "Protocol Omega": "Protocollo Omega",
    "Alia Academy": "Alius Academy",
    "Genesis": "The Genesis",
    "Brainwashing": "Brainwashing",
    "Occult": "Occult",
    "Wild": "Wild",
    "Shuriken": "Shuriken",
    "Farm": "Farm",
    "Kirkwood": "Kirkwood",
    "Mirage": "Mirage",
    "Alpine": "Alpine",
    "Cloister Divinity": "Cloister Divinity",
    "Mary Times": "Mary Times",
    "Fire Dragon": "Fire Dragon"
};

let charactersData = [];
let draftedPlayers = {}; // slotId -> player obj
let currentActiveSlot = null;

// DOM Elements
const pitchSlotsContainer = document.getElementById('slots-container');
const benchSlotsContainer = document.getElementById('bench-slots');
const formationSelect = document.getElementById('formation-select');
const draftModal = document.getElementById('draft-modal');
const draftChoicesContainer = document.getElementById('draft-choices');
const closeModalBtn = document.getElementById('close-modal');
const draftPositionLabel = document.getElementById('draft-position-label');
const resetBtn = document.getElementById('reset-draft');
const teamRatingEl = document.getElementById('team-rating');

// Sidebar DOM
const teamsSidebar = document.getElementById('teams-sidebar');
const toggleSidebarBtn = document.getElementById('toggle-sidebar');
const closeSidebarBtn = document.getElementById('close-sidebar');
const teamsListContainer = document.getElementById('teams-list');
const teamSearchInput = document.getElementById('team-search');

// Templates
const pitchSlotTemplate = document.getElementById('pitch-slot-template');
const playerCardTemplate = document.getElementById('player-card-template');

// Generate consistent pseudo-random rating based on string
function generateRating(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    // Return rating between 75 and 95
    return 75 + (Math.abs(hash) % 21);
}

// Fetch data
async function loadData() {
    try {
        const response = await fetch('data.json');
        charactersData = await response.json();
        // pre-calculate ratings
        charactersData.forEach(c => {
            c.rating = generateRating(c.id);
        });
        
        initSidebar();
    } catch (e) {
        console.error("Failed to load character data:", e);
    }
}

// Sidebar Logic
let uniqueTeamsList = [];

function initSidebar() {
    const teamsSet = new Set();
    charactersData.forEach(c => {
        if (c.team && c.team !== 'Unknown Team') {
            const parts = c.team.split(',').map(t => t.trim());
            parts.forEach(p => {
                if(p) teamsSet.add(p);
            });
        }
    });
    
    uniqueTeamsList = Array.from(teamsSet).sort();
    renderTeamsList(uniqueTeamsList);
}

function renderTeamsList(teams) {
    teamsListContainer.innerHTML = '';
    teams.forEach(t => {
        const li = document.createElement('li');
        
        const engSpan = document.createElement('span');
        engSpan.className = 'team-eng';
        engSpan.textContent = t;
        
        const itaSpan = document.createElement('span');
        itaSpan.className = 'team-ita';
        itaSpan.textContent = '→ ' + (teamTranslations[t] || t);
        
        li.appendChild(engSpan);
        li.appendChild(itaSpan);
        teamsListContainer.appendChild(li);
    });
}

// Render Pitch
function renderPitch(formationKey) {
    pitchSlotsContainer.innerHTML = '';
    draftedPlayers = {};
    updateTeamRating();
    
    if (!formations[formationKey]) return;

    const lines = formations[formationKey];
    
    lines.forEach((line, lineIndex) => {
        const row = document.createElement('div');
        row.className = 'formation-row';
        
        for (let i = 0; i < line.count; i++) {
            const slotId = `slot-${lineIndex}-${i}`;
            const clone = pitchSlotTemplate.content.cloneNode(true);
            const slotEl = clone.querySelector('.pitch-slot');
            
            slotEl.dataset.slotId = slotId;
            slotEl.dataset.position = line.role;
            
            slotEl.querySelector('.slot-position').textContent = '+ ' + line.role;
            
            slotEl.addEventListener('click', () => openDraftModal(slotId, line.role));
            
            row.appendChild(slotEl);
        }
        pitchSlotsContainer.appendChild(row);
    });

    // Render Bench
    benchSlotsContainer.innerHTML = '';
    for(let i=0; i<5; i++) {
        const slotId = `bench-${i}`;
        const clone = pitchSlotTemplate.content.cloneNode(true);
        const slotEl = clone.querySelector('.pitch-slot');
        
        slotEl.dataset.slotId = slotId;
        slotEl.dataset.position = 'Misto';
        
        slotEl.querySelector('.slot-position').textContent = '+ SUB';
        
        slotEl.addEventListener('click', () => openDraftModal(slotId, 'Misto'));
        
        benchSlotsContainer.appendChild(slotEl);
    }
}

// Create Card DOM Element
function createCardElement(player, isDraftChoice = false) {
    const clone = playerCardTemplate.content.cloneNode(true);
    const card = clone.querySelector('.player-card');
    
    card.classList.add(`card-${player.element.toLowerCase()}`);
    
    card.querySelector('.player-rating').textContent = player.rating;
    card.querySelector('.player-position').textContent = player.position;
    card.querySelector('.player-name').textContent = player.name;
    card.querySelector('.player-team').textContent = player.team || 'Sconosciuta';
    card.querySelector('.player-game').textContent = player.game || 'Sconosciuto';
    card.querySelector('.element-name').textContent = player.element;
    
    // Set image using webp if available, fallback to png handled in HTML error
    card.querySelector('.player-image').src = player.image_webp || player.image;
    
    if (isDraftChoice) {
        // Add spawn animation delay
        card.classList.add('animate-spawn');
    }
    
    return card;
}

// Select random players
function getRandomPlayers(position, count) {
    let pool;
    if (position === 'Misto') {
        // Bench allows any position
        pool = charactersData;
    } else {
        // Filter by position
        pool = charactersData.filter(c => c.position === position);
        
        // If we don't have enough players for this position, pad with other positions
        if (pool.length < count) {
            const others = charactersData.filter(c => c.position !== position);
            // Shuffle others
            others.sort(() => 0.5 - Math.random());
            pool = pool.concat(others);
        }
    }
    
    // Remove already drafted players to avoid duplicates if possible
    const draftedIds = Object.values(draftedPlayers).map(p => p.id);
    let availablePool = pool.filter(c => !draftedIds.includes(c.id));
    
    if (availablePool.length < count) {
        // Fallback: allow duplicates if pool is exhausted
        availablePool = pool;
    }
    
    // Shuffle and pick
    availablePool.sort(() => 0.5 - Math.random());
    return availablePool.slice(0, count);
}

function updateTeamRating() {
    const players = Object.values(draftedPlayers);
    if (players.length === 0) {
        teamRatingEl.textContent = '0';
        return;
    }
    const sum = players.reduce((acc, p) => acc + p.rating, 0);
    teamRatingEl.textContent = Math.round(sum / players.length);
}

// Modal Logic
function openDraftModal(slotId, position) {
    currentActiveSlot = slotId;
    draftPositionLabel.textContent = position;
    draftChoicesContainer.innerHTML = '';
    
    const choices = getRandomPlayers(position, 5);
    
    choices.forEach((player, index) => {
        const card = createCardElement(player, true);
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.addEventListener('click', () => {
            selectPlayerForSlot(player);
        });
        
        draftChoicesContainer.appendChild(card);
    });
    
    draftModal.classList.add('active');
}

function selectPlayerForSlot(player) {
    if (!currentActiveSlot) return;
    
    // Save to state
    draftedPlayers[currentActiveSlot] = player;
    
    // Update UI
    const slotEl = document.querySelector(`.pitch-slot[data-slot-id="${currentActiveSlot}"]`);
    const cardContainer = slotEl.querySelector('.slot-card-container');
    const placeholder = slotEl.querySelector('.slot-placeholder');
    
    cardContainer.innerHTML = '';
    const card = createCardElement(player);
    cardContainer.appendChild(card);
    
    placeholder.style.opacity = '0'; // Hide placeholder
    
    updateTeamRating();
    closeModal();
}

function closeModal() {
    draftModal.classList.remove('active');
    currentActiveSlot = null;
}

// Event Listeners
formationSelect.addEventListener('change', (e) => {
    if (Object.keys(draftedPlayers).length > 0) {
        if(confirm("Cambiare modulo resetterà il draft attuale. Procedere?")) {
            renderPitch(e.target.value);
        } else {
            // Revert select
            // Note: simple implementation, ideal would be to save previous value
        }
    } else {
        renderPitch(e.target.value);
    }
});

closeModalBtn.addEventListener('click', closeModal);

resetBtn.addEventListener('click', () => {
    if(confirm("Vuoi riavviare il draft?")) {
        renderPitch(formationSelect.value);
    }
});

// Sidebar Listeners
toggleSidebarBtn.addEventListener('click', () => {
    teamsSidebar.classList.add('open');
});

closeSidebarBtn.addEventListener('click', () => {
    teamsSidebar.classList.remove('open');
});

teamSearchInput.addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase();
    const filtered = uniqueTeamsList.filter(t => {
        const eng = t.toLowerCase();
        const ita = (teamTranslations[t] || t).toLowerCase();
        return eng.includes(q) || ita.includes(q);
    });
    renderTeamsList(filtered);
});

// Init
loadData();
