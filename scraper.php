<?php
$outputFile = 'c:/xampp/htdocs/InazumaRandomizer/data.json';
$baseUrl = 'https://zukan.inazuma.jp/en/chara_list/?per_page=200&page=';

$characters = [];
$page = 1;
$maxPages = 30; // 5454 / 200 is approx 28 pages. Set an upper limit just in case.

echo "Starting scrape...\n";

// Function to fetch with cURL
function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36');
    // Disable SSL verification for simplicity if needed, but usually works
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

while ($page <= $maxPages) {
    echo "Fetching page $page...\n";
    $url = $baseUrl . $page;
    $content = fetchUrl($url);
    
    if (!$content) {
        echo "Failed to fetch page $page. Stopping.\n";
        break;
    }

    preg_match_all('/<tbody\b[^>]*>(.*?)<\/tbody>/is', $content, $matches);
    
    if (empty($matches[1])) {
        echo "No characters found on page $page. Finished.\n";
        break;
    }

    $addedOnThisPage = 0;

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
            $char['name'] = html_entity_decode($m[1], ENT_QUOTES | ENT_XML1, 'UTF-8');
        }
        
        // Element (Wind, Fire, Mountain, Forest, Void)
        if (preg_match('/<td rowspan="2">\s*(Wind|Fire|Mountain|Forest|Void)\s*<\/td>/i', $tbody, $m)) {
            $char['element'] = trim($m[1]);
        } else {
            $char['element'] = 'Void'; 
        }

        // Position (GK, DF, MF, FW)
        if (preg_match('/<td rowspan="2">\s*(GK|DF|MF|FW)\s*<\/td>/i', $tbody, $m)) {
            $char['position'] = trim($m[1]);
        } else {
            continue; 
        }
        
        // Role (Player)
        if (preg_match('/<td rowspan="2">[\s\S]*?(Player)[\s\S]*?<\/td>/i', $tbody, $m)) {
            $char['role'] = 'Player';
        } else {
            continue; 
        }

        // Game
        if (preg_match('/<td colspan="9">\s*([^<]+)\s*<\/td>/is', $tbody, $m)) {
            $char['game'] = trim($m[1]);
        } else {
            $char['game'] = 'Unknown Game';
        }

        // Extract all <td rowspan="2"> to find the Team (Index 10)
        if (preg_match_all('/<td rowspan="2">(.*?)<\/td>/is', $tbody, $tdMatches)) {
            if (isset($tdMatches[1][10])) {
                // Replace <br> with comma
                $teamHtml = preg_replace('/<br\s*\/?>/i', ', ', $tdMatches[1][10]);
                $char['team'] = trim(strip_tags($teamHtml));
            } else {
                $char['team'] = 'Unknown Team';
            }
        }

        if (isset($char['id']) && isset($char['name']) && isset($char['element']) && isset($char['position'])) {
            $char['image'] = 'https://dxi4wb638ujep.cloudfront.net/1/' . $char['id'] . '.png';
            $char['image_webp'] = 'https://dxi4wb638ujep.cloudfront.net/1/' . $char['id'] . '.webp';
            $characters[] = $char;
            $addedOnThisPage++;
        }
    }
    
    echo "Extracted $addedOnThisPage players from page $page.\n";
    
    if ($addedOnThisPage === 0) {
        // If we found tbodys but no valid players (e.g. all managers), we still continue, 
        // but maybe we reached the end of the list. Let's just continue until no tbodys.
    }

    $page++;
    usleep(500000); // Wait 0.5s to not hammer the server
}

file_put_contents($outputFile, json_encode($characters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Total extracted: " . count($characters) . " characters.\n";
?>
