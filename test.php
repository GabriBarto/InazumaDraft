<?php
$html = '<tr>
                <td rowspan="2">
                  <label><input type="checkbox" class="my-team-checkbox" 
                     
                     data-chara-id="k/d/w/dwho-wi8ruk" 
                     data-chara-name="Mark Evans"
                     data-nickname="Evans"
                      />
                </td>
                <td rowspan="2">1</td>
                <td rowspan="2">
                  <div class="nameBox">
                    <figure>
                      
                        
                          <a href="/en/chara_model_view/?q=hN2cl56NnpyLmo2glpvdxaTdnM_Oz8_Pz87P3aKC">
                        
                      
                      <picture>
                        
                          
                          <source type="image/webp" srcset="https://dxi4wb638ujep.cloudfront.net/1/k/d/w/dwho-wi8ruk.webp">
                          <img src="https://dxi4wb638ujep.cloudfront.net/1/k/d/w/dwho-wi8ruk.png"
                                  alt="Mark Evans"
                                  onerror="this.onerror=null; this.src=`https://dxi4wb638ujep.cloudfront.net/1/prd/assets/4/img/shared/icn_secret_character.png`;" />
                        
                      </picture>
                      </a>
                    </figure>
                    <p>
                        
                        <a href="/en/chara_param/?q=hN2ZlpOLmo2gnJeejZ6glpugjIuN3cWk3ZzPzs_Pz8_Oz92igg%3D%3D">
                          <span class="name">Mark Evans</span>
                          
                        </a>
                        
                    </p>
                  </div>
                </td>
                <td rowspan="2">
                  
                    Evans
                  
                </td>
                
                <td colspan="9">
                  
                    Inazuma Eleven
                  
                </td>
                
                  <td rowspan="2">Male</td>
                  <td rowspan="2">Mountain</td>
                  <td rowspan="2">GK</td>
                  <td rowspan="2">
                    
                      
                      
                        Player
                      
                    
                  </td>
                  
                  <td rowspan="2">Middle School</td>
                  <td rowspan="2">Grade 8</td>
                  <td rowspan="2">Raimon<br />Inazuma National</td>
                  <td rowspan="2">
                    <p class="description">
                      Has more passion for football than anyone else.<br>His mighty heart will never give up, no matter what.
                    </p>
                  </td>
                  
                
              </tr>';

$tbody = $html; // Normally this would be a tbody but the structure is the same.
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
    // continue
}

// Role (Player)
if (preg_match('/<td rowspan="2">[\s\S]*?(Player)[\s\S]*?<\/td>/i', $tbody, $m)) {
    $char['role'] = 'Player';
} else {
    // continue
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

echo json_encode($char, JSON_PRETTY_PRINT);
