<?php
$inputFile = 'C:/Users/Utente/.gemini/antigravity-ide/brain/d542a148-7f55-4f62-860b-22228d53e871/.system_generated/steps/9/content.md';
$outputFile = 'c:/xampp/htdocs/InazumaRandomizer/data.json';

$content = file_get_contents($inputFile);

// Use regex to find all <tbody ...>...</tbody> blocks
preg_match_all('/<tbody\b[^>]*>(.*?)<\/tbody>/is', $content, $matches);

$characters = [];

foreach ($matches[1] as $tbody) {
    if (strpos($tbody, 'data-chara-id') === false) {
        continue;
    }

    $char = [];
    
    // ID
    if (preg_match('/data-chara-id="([^"]+)"/', $tbody, $m)) {
        $char['id'] = $m[1];
    }
    // Name
    if (preg_match('/data-chara-name="([^"]+)"/', $tbody, $m)) {
        $char['name'] = $m[1];
    }
    
    // Element (Wind, Fire, Mountain, Forest)
    if (preg_match('/<td rowspan="2">\s*(Wind|Fire|Mountain|Forest)\s*<\/td>/i', $tbody, $m)) {
        $char['element'] = trim($m[1]);
    }

    // Position (GK, DF, MF, FW)
    if (preg_match('/<td rowspan="2">\s*(GK|DF|MF|FW)\s*<\/td>/i', $tbody, $m)) {
        $char['position'] = trim($m[1]);
    }
    
    // Role (Player, Coach, etc.)
    if (preg_match('/<td rowspan="2">[\s\S]*?(Player)[\s\S]*?<\/td>/i', $tbody, $m)) {
        $char['role'] = 'Player';
    }

    if (isset($char['id']) && isset($char['name']) && isset($char['element']) && isset($char['position']) && isset($char['role'])) {
        $char['image'] = 'https://dxi4wb638ujep.cloudfront.net/1/' . $char['id'] . '.png';
        $char['image_webp'] = 'https://dxi4wb638ujep.cloudfront.net/1/' . $char['id'] . '.webp';
        $characters[] = $char;
    }
}

file_put_contents($outputFile, json_encode($characters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Extracted " . count($characters) . " characters.\n";
?>
