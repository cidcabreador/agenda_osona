<?php
// www/robot/balenya.php (Versió Corregida amb els selectors de detall correctes)

require_once __DIR__ . '/scraper_helpers.php';

function executar_scraper_balenya($pdo) {
    
    // ---- PARÀMETRES ESPECÍFICS D'AQUEST SCRAPER ----
    $id_municipi_scraper = 2; // ID de Balenyà a la teva BD
    $nom_municipi_log = "Balenyà";
    $url_agenda = "https://www.balenya.cat/actualitat/agenda";
    $url_base = "https://www.balenya.cat";
    // ---------------------------------------------

    $opts = ['http'=>['user_agent'=>'Mozilla/5.0'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
    $context = stream_context_create($opts);
    @$html = file_get_contents($url_agenda, false, $context);
    if (!$html) return "ERROR: No s'ha pogut descarregar la pàgina principal de $nom_municipi_log.";

    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($dom);
    
    // Selector per al llistat (aquest ja era correcte)
    $items = $xpath->query("//li[contains(@class,'article-item')]");

    $log = "[INFO] S'han trobat " . $items->length . " possibles esdeveniments a $nom_municipi_log.\n";
    $insertats = 0; $ignorats = 0;

    $stmt_insert = $pdo->prepare("INSERT INTO esdeveniments_temporals (nom, descripcio, data_inici, lloc, preu, organitzadors, imatge, municipi) VALUES (:nom, :descripcio, :data_inici, :lloc, :preu, :organitzadors, :imatge, :id_municipi)");
    $stmt_check = $pdo->prepare("SELECT id FROM esdeveniments_temporals WHERE nom = ? AND data_inici = ?");

    foreach ($items as $item) {
        $enllac_node = $xpath->query(".//a", $item);
        if ($enllac_node->length === 0) continue;
        
        $enllac = $enllac_node->item(0)->getAttribute("href");
        if (strpos($enllac, "http") !== 0) $url_detall = $url_base . $enllac;
        else $url_detall = $enllac;

        @$html_detall = file_get_contents($url_detall, false, $context);
        if (!$html_detall) continue;

        $dom_detall = new DOMDocument();
        @$dom_detall->loadHTML('<?xml encoding="utf-8" ?>' . $html_detall);
        $xpath_detall = new DOMXPath($dom_detall);

        // ===== INICI DELS NOUS SELECTORS CORREGITS =====
        
        $nom = trim($xpath_detall->evaluate("string(//h1[contains(@class,'title')])"));
        $data_raw = trim($xpath_detall->evaluate("string(//div[@class='date'])"));
        $descripcio = trim($xpath_detall->evaluate("string(//span[@class='entradeta'])"));
        $lloc = trim($xpath_detall->evaluate("string(//h2[contains(text(),'Adreça:')]/following-sibling::p[1])"));
        
        $imatge_node = $xpath_detall->query("//div[contains(@class,'image')]//img/@src | //div[contains(@class,'div-img')]//img/@src");
        $imatge = ($imatge_node->length > 0) ? $imatge_node->item(0)->nodeValue : null;

        if ($imatge && strpos($imatge,"http") !== 0) {
            $imatge = $url_base . $imatge;
        }

        $organitzadors = '';
        if (preg_match('/Organitza:\s*(.*)/i', $descripcio, $matches)) {
            $organitzadors = trim($matches[1]);
        }

        // ===== FI DELS NOUS SELECTORS CORREGITS =====

        // Lògica per extreure i formatar la data (més robusta)
        $data_bd = null;
        if (preg_match('/(\d{1,2})[\/\.-](\d{2})[\/\.-](\d{4})/', $data_raw, $m)) {
            $data_bd = "{$m[3]}-{$m[2]}-{$m[1]}";
        } elseif (preg_match('/(\d{1,2})\s+d[e\’\']\s*([a-zàéèíïòóúü]+)\s+de\s+(\d{4})/iu', $data_raw, $m)) {
            $dia = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mes = _map_mes_cat($m[2]);
            $any = $m[3];
            if ($mes) $data_bd = "$any-$mes-$dia";
        }
        
        if (!$nom || !$data_bd) {
            $log .= "[AVÍS] Ometent esdeveniment sense nom o data: '$nom'\n";
            continue;
        }

        $stmt_check->execute([$nom, $data_bd]);
        if ($stmt_check->fetch()) { $ignorats++; continue; }

        $stmt_insert->execute([
            ':nom' => $nom,
            ':descripcio' => $descripcio,
            ':data_inici' => $data_bd,
            ':lloc' => $lloc,
            ':preu' => '', // Aquesta web no sembla tenir camp de preu
            ':organitzadors' => $organitzadors,
            ':imatge' => $imatge,
            ':id_municipi' => $id_municipi_scraper
        ]);
        $insertats++;
        sleep(1);
    }

    return $log . "[OK] Procés finalitzat. S'han inserit $insertats esdeveniments nous i s'han ignorat $ignorats duplicats.";
}