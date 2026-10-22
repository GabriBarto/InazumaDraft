// opponents.js - generate 5 random opponent teams
// This script is loaded by opponents.html

// Fetch character data and extract unique team names
async function loadOpponents() {
  try {
    const response = await fetch('data.json');
    const characters = await response.json();
    // Build a set of unique team names (excluding "Unaffiliated" and empty)
    const teamSet = new Set();
    characters.forEach(c => {
      if (c.team && c.team !== 'Unaffiliated') {
        // Some entries have multiple teams separated by commas
        c.team.split(',').forEach(t => {
          const trimmed = t.trim();
          if (trimmed) teamSet.add(trimmed);
        });
      }
    });
    const uniqueTeams = Array.from(teamSet);
    // Pick 5 random distinct teams
    const chosen = [];
    const copy = [...uniqueTeams];
    const count = Math.min(5, copy.length);
    for (let i = 0; i < count; i++) {
      const idx = Math.floor(Math.random() * copy.length);
      chosen.push(copy.splice(idx, 1)[0]);
    }
    renderOpponents(chosen);
  } catch (e) {
    console.error('Failed to load opponents:', e);
  }
}

function renderOpponents(teams) {
  const listEl = document.getElementById('opponents-list');
  listEl.innerHTML = '';
  teams.forEach(team => {
    const li = document.createElement('li');
    li.className = 'opponent-item';
    li.textContent = team;
    listEl.appendChild(li);
  });
}

// Button handler
const generateBtn = document.getElementById('generate-opponents');
if (generateBtn) {
  generateBtn.addEventListener('click', loadOpponents);
}

// Optionally generate on page load
// loadOpponents();
