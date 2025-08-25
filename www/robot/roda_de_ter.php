<?php
// www/robot/roda_de_ter.php (Versió Final i Funcional)

require_once __DIR__ . '/scraper_helpers.php';

// El nom de la funció ha de coincidir amb el nom del fitxer: executar_scraper_NOM_DEL_FITXER
function executar_scraper_roda_de_ter($pdo) {
    
    // ---- PARÀMETRES ESPECÍFICS D'AQUEST SCRAPER ----
    $id_municipi_scraper = 22; // ID de Roda de Ter a la teva BD
    $nom_municipi_log = "Roda de Ter"; // Nom per als missatges de log
    $url_agenda = "https://www.rodadeter.cat/el-municipi/actualitat/agenda";
    $url_base = "https://www.rodadeter.cat";
    // ---------------------------------------------

    $opts = ['http'=>['user_agent'=>'Mozilla/5.0'],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
    $context = stream_context_create($opts);
    @$html = file_get_contents($url_agenda, false, $context);
    if (!$html) return "ERROR: No s'ha pogut descarregar la pàgina principal de $nom_municipi_log.";

    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($dom);
    $items = $xpath->query("//div[contains(@class,'agenda_item')]");
    
    $log = "[INFO] S'han trobat " . $items->length . " possibles esdeveniments a $nom_municipi_log.\n";
    $insertats = 0; $ignorats = 0;

    $stmt_insert = $pdo->prepare("INSERT INTO esdeveniments_temporals (nom, descripcio, data_inici, lloc, preu, organitzadors, imatge, municipi) VALUES (:nom, :descripcio, :data_inici, :lloc, :preu, :organitzadors, :imatge, :id_municipi)");
    $stmt_check = $pdo->prepare("SELECT id FROM esdeveniments_temporals WHERE nom = ? AND data_inici = ?");

    foreach($items as $item) {
        $nom = trim($xpath->evaluate("string(.//span[@class='titol'])",$item));
        $enllac = $xpath->evaluate("string(.//a/@href)",$item);
        if(!$nom || !$enllac) continue;

        $dia = trim($xpath->evaluate("string(.//div[contains(@class,'data_dia_mes_interior')]/span[contains(@class,'data_gris')])",$item));
        $mes = trim($xpath->evaluate("string(.//div[contains(@class,'data_mes')]/span[contains(@class,'data_gris')])",$item));
        $map_mes = ['GEN'=>'01','FEB'=>'02','MAR'=>'03','ABR'=>'04','MAI'=>'05','JUN'=>'06','JUL'=>'07','AGO'=>'08','SET'=>'09','OCT'=>'10','NOV'=>'11','DES'=>'12'];
        $mes_num = $map_mes[strtoupper($mes)] ?? null;
        
        $any_actual = date('Y');
        $data_actual_obj = new DateTime();
        $data_event_obj = DateTime::createFromFormat('d-m', $dia . '-' . $mes_num);
        $any = ($data_event_obj && $data_event_obj < $data_actual_obj) ? $any_actual + 1 : $any_actual;
        
        $data_text = ($dia && $mes_num) ? str_pad($dia,2,'0',STR_PAD_LEFT).'/'.$mes_num.'/'.$any : null;
        $data_bd = formatar_data_per_bd($data_text);
        if (!$data_bd) continue;

        $stmt_check->execute([$nom, $data_bd]);
        if ($stmt_check->fetch()) { $ignorats++; continue; }
        
        $url_detall = $url_base . $enllac;
        $detalls_text = obtenir_detalls_event($url_detall, $context);
        $dades_parsejades = parse_detalls($detalls_text);
        
        $imatgeRuta = null;
        if($detalls_text){
            @$html_detall = file_get_contents($url_detall,false,$context);
            if ($html_detall) {
                $dom_detall = new DOMDocument();
                @$dom_detall->loadHTML('<?xml encoding="utf-8" ?>' . $html_detall);
                $xpath_detall = new DOMXPath($dom_detall);
                $src_node = $xpath_detall->query("//div[@id='contingut_fotos']//img/@src");
                if ($src_node->length > 0) {
                    $src = $src_node->item(0)->nodeValue;
                    if (strpos($src, "http") !== 0) $src = $url_base . $src;
                    $imatgeRuta = $src;
                }
            }
        }
        
        $organitzadors_str = implode(", ", $dades_parsejades['Organitzadors']);
        $stmt_insert->execute([
            ':nom' => $nom,
            ':descripcio' => $dades_parsejades['Descripcio'],
            ':data_inici' => $data_bd,
            ':lloc' => $dades_parsejades['Lloc'],
            ':preu' => $dades_parsejades['Preu'],
            ':organitzadors' => $organitzadors_str,
            ':imatge' => $imatgeRuta,
            ':id_municipi' => $id_municipi_scraper
        ]);
        $insertats++;
        sleep(1);
    }

    return $log . "[OK] Procés finalitzat. S'han inserit $insertats esdeveniments nous i s'han ignorat $ignorats duplicats.";
}