<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inazuma Draft</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <header class="header">
            <h1 class="logo">INAZUMA <span>DRAFT</span></h1>
            <div class="controls">
                <button id="toggle-sidebar" class="btn">Traduci Squadre</button>
                <label for="formation-select">Modulo:</label>
                <select id="formation-select">
                    <option value="" disabled selected>Seleziona un modulo</option>
                    <option value="4-4-2 Rombo">4-4-2 Rombo</option>
                    <option value="4-4-2 In Linea">4-4-2 In Linea</option>
                    <option value="3-5-2 Libertà">3-5-2 Libertà</option>
                    <option value="4-3-3 Triangolo">4-3-3 Triangolo</option>
                    <option value="4-3-3 Delta">4-3-3 Delta</option>
                    <option value="4-5-1 Equilibrata">4-5-1 Equilibrata</option>
                    <option value="3-6-1 Esagono">3-6-1 Esagono</option>
                    <option value="5-4-1 Centrocampo raddoppiato">5-4-1 Centrocampo raddoppiato</option>
                </select>
                <button id="reset-draft" class="btn">Riavvia Draft</button>
            </div>
            <div class="team-stats">
                <div class="stat">
                    <span class="stat-label">Voto Squadra</span>
                    <span class="stat-value" id="team-rating">0</span>
                </div>
            </div>
        </header>

        <main class="pitch-container">
            <div class="pitch-wrapper">
                <div class="pitch" id="pitch">
                    <div class="pitch-lines"></div>
                    <div class="slots-container" id="slots-container">
                        <!-- Slots will be injected here -->
                    </div>
                </div>
                
                <div class="bench-container">
                    <h3 class="bench-title">Panchina</h3>
                    <div class="bench-slots" id="bench-slots">
                        <!-- Bench slots injected here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Teams Sidebar -->
    <aside class="sidebar glass-panel" id="teams-sidebar">
        <div class="sidebar-header">
            <h2>Squadre (ENG &rarr; ITA)</h2>
            <button class="btn-close" id="close-sidebar">&times;</button>
        </div>
        <div class="sidebar-search">
            <input type="text" id="team-search" placeholder="Cerca squadra...">
        </div>
        <ul class="teams-list" id="teams-list">
            <!-- Populated by JS -->
        </ul>
    </aside>

    <!-- Draft Selection Modal -->
    <div class="modal-overlay" id="draft-modal">
        <div class="modal-content glass-panel">
            <h2 class="modal-title">Scegli un giocatore per il ruolo: <span id="draft-position-label"></span></h2>
            <div class="draft-choices" id="draft-choices">
                <!-- 5 random player cards will be injected here -->
            </div>
            <button class="btn btn-close" id="close-modal">Annulla</button>
        </div>
    </div>

    <!-- Template for a single player card -->
    <template id="player-card-template">
        <div class="player-card">
            <div class="card-background"></div>
            <div class="card-content">
                <div class="card-header">
                    <span class="player-rating">85</span>
                    <span class="player-position">POS</span>
                </div>
                <div class="player-image-container">
                    <img src="" alt="Player" class="player-image" loading="lazy" onerror="this.src='https://dxi4wb638ujep.cloudfront.net/1/prd/assets/4/img/shared/icn_secret_character.png'">
                </div>
                <div class="player-info">
                    <h3 class="player-name">Name</h3>
                    <div class="player-details">
                        <div class="player-team">Team</div>
                        <div class="player-game">Game</div>
                    </div>
                    <div class="player-element">
                        <span class="element-icon"></span>
                        <span class="element-name">Element</span>
                    </div>
                </div>
            </div>
            <div class="card-glow"></div>
        </div>
    </template>

    <!-- Template for a pitch slot -->
    <template id="pitch-slot-template">
        <div class="pitch-slot">
            <div class="slot-placeholder">
                <span class="slot-position">+ POS</span>
            </div>
            <div class="slot-card-container">
                <!-- Selected card goes here -->
            </div>
        </div>
    </template>

    <script src="script.js"></script>
</body>
</html>
