<?php
// Function test script
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/calender/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
$loginHtml = curl_exec($ch);

// Log in as rangel
curl_setopt($ch, CURLOPT_URL, 'http://localhost/calender/login.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['email' => 'rangel', 'senha' => 'admin', 'action' => 'login']));
$loginResp = curl_exec($ch);

// Create Activity
curl_setopt($ch, CURLOPT_URL, 'http://localhost/calender/gerenciar_catalogo.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'create', 'nome' => 'Atividade Curl', 'descricao' => 'Test']));
$createResp = curl_exec($ch);
curl_setopt($ch, CURLOPT_POST, false);

// Read page to find ID
curl_setopt($ch, CURLOPT_URL, 'http://localhost/calender/gerenciar_catalogo.php');
$html = curl_exec($ch);
if (preg_match('/data-nome="Atividade Curl"\s+.*data-id="(\d+)"/sU', $html, $matches)) {
    $newId = $matches[1];
    echo "Created ID: $newId\n";
    
    // Update
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'update', 'id' => $newId, 'nome' => 'Atividade Curl Edit', 'descricao' => 'Test2']));
    curl_exec($ch);
    
    // Verify update
    curl_setopt($ch, CURLOPT_POST, false);
    $html2 = curl_exec($ch);
    if (strpos($html2, 'Atividade Curl Edit') !== false) {
        echo "Update visible in HTML!\n";
    } else {
        echo "Update NOT visible in HTML!\n";
    }
    
    // Delete
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['action' => 'delete', 'id' => $newId]));
    curl_exec($ch);
    
    // Verify delete
    curl_setopt($ch, CURLOPT_POST, false);
    $html3 = curl_exec($ch);
    if (strpos($html3, 'Atividade Curl Edit') === false) {
        echo "Delete SUCCESS, item gone from HTML!\n";
    } else {
        echo "Delete FAILED, item still in HTML!\n";
    }
} else {
    echo "Could not find created item in HTML. Login may have failed or creation failed.\n";
}
curl_close($ch);
?>
