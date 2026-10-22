<?php
$file = 'c:/xampp/htdocs/InazumaRandomizer/data.json';
$data = json_decode(file_get_contents($file), true);

$teams = [];
foreach ($data as $char) {
    if (!empty($char['team'])) {
        $parts = array_map('trim', explode(',', $char['team']));
        foreach ($parts as $t) {
            if (!empty($t)) {
                $teams[$t] = true;
            }
        }
    }
}

$uniqueTeams = array_keys($teams);
sort($uniqueTeams);
file_put_contents('c:/xampp/htdocs/InazumaRandomizer/teams.json', json_encode($uniqueTeams, JSON_PRETTY_PRINT));
echo "Found " . count($uniqueTeams) . " unique teams.\n";
?>
