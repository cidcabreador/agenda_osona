<?php
// ======================================================
// Funciones de ayuda
// ======================================================
function _normalize_text($text) {
    if ($text === null) return '';
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xc2\xa0", ' ', $text);
    $text = preg_replace('/\r\n?/', "\n", $text);
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n\s*\n+/', "\n\n", $text);
    return trim($text);
}

function _labels_regex() {
    return '(?:Data|Dates|Hora|Horari|Lloc|Preu|Organitza|Col[·\.]?labora|Amb\s+el\s+suport(?:\s+de)?)';
}

function _extract_segment($text, $labelRegex, $allLabelsRegex) {
    $pattern = '/\b' . $labelRegex . '\s*:\s*(.*?)(?=\b' . $allLabelsRegex . '\s*:|$)/is';
    if (preg_match($pattern, $text, $m)) {
        return trim($m[1]);
    }
    return null;
}

function _map_mes_cat($mes) {
    $mes = mb_strtolower(trim($mes), 'UTF-8');
    $map = [
        'gener'=>'01','febrer'=>'02','març'=>'03','abril'=>'04','maig'=>'05','juny'=>'06',
        'juliol'=>'07','agost'=>'08','setembre'=>'09','octubre'=>'10','novembre'=>'11','desembre'=>'12'
    ];
    return $map[$mes] ?? null;
}



function _extract_data_inici($text) {
    $labels = _labels_regex();
    $seg = _extract_segment($text, '(?:Data|Dates)', $labels);
    if (!$seg) return null;

    $segLow = mb_strtolower($seg, 'UTF-8');

    // Caso rango de fechas: "del 5 de juliol al 7 de juliol de 2025"
    if (preg_match('/del\s+(\d{1,2})\s+de\s+([a-zàéèíïòóúü]+).*?(\d{4})/iu', $segLow, $m)) {
        $dia = (int)$m[1];
        $mes = _map_mes_cat($m[2]);
        $any = $m[3];
        if ($mes) return str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$any;
    }

    // Caso fecha simple: "5 de juliol de 2025"
    if (preg_match('/(\d{1,2})\s+de\s+([a-zàéèíïòóúü]+)\s+de\s+(\d{4})/iu', $segLow, $m)) {
        $dia = (int)$m[1];
        $mes = _map_mes_cat($m[2]);
        $any = $m[3];
        if ($mes) return str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$any;
    }

    return null;
}

function _split_orgs($txt) {
    if (!$txt) return [];
    $tmp = preg_split('/\s*,\s*|\s+i\s+|\s+amb\s+/u', $txt);
    $out = [];
    foreach ($tmp as $t) {
        $t = trim($t, " \t\n\r\0\x0B.");
        if ($t !== '' && mb_strlen($t) > 2) $out[] = $t;
    }
    return array_values(array_unique($out));
}

function parse_detalls($rawText) {
    $text = _normalize_text($rawText);
    $labels = _labels_regex();

    if (preg_match('/^(.*?)(?=\b(?:INFORMACIÓ|Data|Dates)\s*:?\s)/is', $text, $m)) {
        $descripcio = trim($m[1]);
    } else { $descripcio = ''; }

    $data = _extract_data_inici($text);
    $hora = _extract_segment($text, '(?:Hora|Horari)', $labels);
    $lloc = _extract_segment($text, 'Lloc', $labels);
    $preu = _extract_segment($text, 'Preu', $labels);
    $org1 = _extract_segment($text, 'Organitza', $labels);
    $org2 = _extract_segment($text, '(?:Col[·\.]?labora|Amb\s+el\s+suport(?:\s+de)?)', $labels);
    $organitzadors = array_merge(_split_orgs($org1), _split_orgs($org2));

    return [
        'Descripcio' => $descripcio,
        'Data' => $data,
        'Hora' => trim(str_replace("\n", " ", $hora ?? '')),
        'Lloc' => $lloc,
        'Preu' => $preu,
        'Organitzadors' => $organitzadors
    ];
}

function obtenir_detalls_event($url, $context) {
    @$html = file_get_contents($url, false, $context);
    if (!$html) return null;
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $node = $xpath->query("//div[contains(@class, 'text-maquetat')]");
    return ($node->length > 0) ? _normalize_text($xpath->evaluate("string(.)", $node->item(0))) : null;
}

// ======================================================
// Función principal del scraper
// ======================================================
function executar_scraper_roda_de_ter() {
    $url = "https://www.rodadeter.cat/el-municipi/actualitat/agenda";
    $context = stream_context_create([
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        'http' => ['user_agent' => 'Mozilla/5.0']
    ]);

    @$html = file_get_contents($url, false, $context);
    if (!$html) return ['error' => "No se pudo descargar la página principal."];

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $items = $xpath->query("//div[contains(@class, 'agenda_item')]");
    $events_extrets = [];
    $log = ["[INFO] Se han encontrado " . $items->length . " posibles eventos."];

    foreach ($items as $item) {
        $nom = trim($xpath->evaluate("string(.//span[@class='titol'])", $item));
        $enllac = $xpath->evaluate("string(.//a/@href)", $item);
        if (!$enllac || !$nom) continue;

        $url_detall = "https://www.rodadeter.cat" . $enllac;
        $detalls_text = obtenir_detalls_event($url_detall, $context);

        if (!empty($detalls_text)) {
            $dades_parsejades = parse_detalls($detalls_text);
            $data_bd = $dades_parsejades['Data'];

            if ($data_bd) {
                $events_extrets[] = [
                    'nom' => $nom,
                    'descripcio' => $dades_parsejades['Descripcio'],
                    'data_inici' => $data_bd,
                    'data_fi' => null,
                    'hora' => $dades_parsejades['Hora'],
                    'adreca' => $dades_parsejades['Lloc'] ?: 'Roda de Ter',
                    'preu' => $dades_parsejades['Preu'] ?: 'Consultar',
                    'organitzador_nom' => implode(', ', $dades_parsejades['Organitzadors']),
                    'origen_url' => $url_detall
                ];
            }
        }
        sleep(0.25);
    }

    $log[] = "[OK] Proceso finalizado. Se han extraído " . count($events_extrets) . " eventos válidos.";
    return ['events' => $events_extrets, 'log' => implode("\n", $log)];
}

// ======================================================
// Función de procesamiento para la base de datos
// ======================================================
function processar_resultats_scraper($pdo, $resultats) {
    if (isset($resultats['error'])) return $resultats['error'];
    $nous_events = 0;
    $events_ignorats = 0;
    $log = $resultats['log'];

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($resultats['events'] as $event) {
        $stmt_check_temp = $pdo->prepare("SELECT id FROM esdeveniments_temporals WHERE nom = ? AND data_inici = ?");
        $stmt_check_temp->execute([$event['nom'], $event['data_inici']]);

        $stmt_check_main = $pdo->prepare("SELECT id FROM esdeveniments WHERE nom = ? AND data_inici = ?");
        $stmt_check_main->execute([$event['nom'], $event['data_inici']]);

        if ($stmt_check_temp->fetch() || $stmt_check_main->fetch()) {
            $events_ignorats++;
            continue;
        }

        $sql = "INSERT INTO esdeveniments_temporals 
            (nom, descripcio, data_inici, data_fi, hora, adreca, preu, organitzador_nom, origen_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                $event['nom'],
                $event['descripcio'],
                $event['data_inici'],
                $event['data_fi'],
                $event['hora'],
                $event['adreca'],
                $event['preu'],
                $event['organitzador_nom'],
                $event['origen_url']
            ]);
            $nous_events++;
        } catch (PDOException $e) {
            $log .= "\n[ERROR] Insert fallo: " . $e->getMessage();
        }
    }

    $log .= "\n[NOU] Inseridos: $nous_events, Ignorados: $events_ignorats";
    return $log;
}

// ======================================================
// EJECUCIÓN
// ======================================================

// Configuración de PDO (ajusta tus datos)
$pdo = new PDO("mysql:host=localhost;dbname=agenda_osona;charset=utf8", "root", "");

// Ejecuta el scraper
$resultats = executar_scraper_roda_de_ter();

// Muestra log y resultados por pantalla
echo "<pre>";
print_r($resultats);
echo "</pre>";

// Si quieres también insertarlo en la BD y ver log
$log_inserts = processar_resultats_scraper($pdo, $resultats);
echo "<pre>";
echo $log_inserts;
echo "</pre>";
?>
