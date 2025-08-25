<?php
// www/robot/scraper_helpers.php

if (count(get_included_files()) == 1) {
    exit("Accés directe no permès.");
}

// -----------------------------------------------------
// Funciones helper COMUNES para todos los scrapers
// -----------------------------------------------------
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
    if (preg_match($pattern, $text, $m)) return trim($m[1]);
    return null;
}

function _map_mes_cat($mes) {
    $mes = mb_strtolower(trim($mes), 'UTF-8');
    $map = ['gener'=>'01','febrer'=>'02','març'=>'03','abril'=>'04','maig'=>'05','juny'=>'06','juliol'=>'07','agost'=>'08','setembre'=>'09','octubre'=>'10','novembre'=>'11','desembre'=>'12'];
    return $map[$mes] ?? null;
}

function formatar_data_per_bd($data_text) {
    if (!$data_text || !is_string($data_text)) return null;
    $parts = explode('/', $data_text);
    if (count($parts) === 3 && checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2])) {
        return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
    }
    return null;
}

function _extract_data_inici($text) {
    $labels = _labels_regex();
    $seg = _extract_segment($text, '(?:Data|Dates)', $labels);
    if (!$seg) return null;
    $segLow = mb_strtolower($seg, 'UTF-8');
    if (preg_match('/(\d{1,2})\s+de\s+([a-zàéèíïòóúü]+)\s+de\s+(\d{4})/iu', $segLow, $m)) {
        $dia = (int)$m[1];
        $mes = _map_mes_cat($m[2]);
        $any = $m[3];
        if ($mes) return str_pad($dia,2,'0',STR_PAD_LEFT).'/'.$mes.'/'.$any;
    }
    return trim($seg);
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
    if (!$rawText) return [
        'Descripcio'=>'', 'Data'=>null, 'Hora'=>'', 'Lloc'=>'', 'Preu'=>'', 'Organitzadors'=>[]
    ];

    $text = _normalize_text($rawText);

    // Detecta todos los bloques "Etiqueta: valor"
    preg_match_all('/\b([A-ZÀ-Úa-zà-ú0-9\.\·\s]+)\s*:\s*(.*?)(?=(?:\b[A-ZÀ-Úa-zà-ú0-9\.\·\s]+:)|$)/is', $text, $matches, PREG_SET_ORDER);

    $campos = [
        'Data'=>null,
        'Hora'=>'',
        'Lloc'=>'',
        'Preu'=>'',
        'Organitzadors'=>[]
    ];

    $descripcio = $text;

    foreach ($matches as $m) {
        $label = mb_strtolower(trim($m[1]), 'UTF-8');
        $valor = trim($m[2]);

        // Detectamos la descripción como todo antes de la primera etiqueta
        $pos = mb_strpos($descripcio, $m[0]);
        if ($pos !== false) $descripcio = trim(mb_substr($descripcio, 0, $pos));

        // Mapear etiquetas conocidas a campos
        if (preg_match('/^data|dates$/i', $label)) {
            // Intentar parsear formato "12 de març de 2025"
            if (preg_match('/(\d{1,2})\s+de\s+([a-zàéèíïòóúü]+)\s+de\s+(\d{4})/iu', $valor, $mm)) {
                $dia = (int)$mm[1];
                $mes = _map_mes_cat($mm[2]);
                $any = $mm[3];
                if ($mes) $campos['Data'] = str_pad($dia,2,'0',STR_PAD_LEFT).'/'.$mes.'/'.$any;
            } else {
                $campos['Data'] = $valor;
            }
        } elseif (preg_match('/^hora|horari$/i', $label)) {
            $campos['Hora'] = str_replace("\n"," ", $valor);
        } elseif (preg_match('/^lloc$/i', $label)) {
            $campos['Lloc'] = $valor;
        } elseif (preg_match('/^preu$/i', $label)) {
            $campos['Preu'] = $valor;
        } elseif (preg_match('/^organitza$/i', $label) || preg_match('/col[·\.]?labora|amb\s+el\s+suport(?:\s+de)?/i', $label)) {
            $campos['Organitzadors'] = array_merge($campos['Organitzadors'], _split_orgs($valor));
        }
    }

    $campos['Descripcio'] = trim($descripcio);
    $campos['Organitzadors'] = array_values(array_unique($campos['Organitzadors']));

    return $campos;
}


function obtenir_detalls_event($url, $context) {
    @$html = file_get_contents($url,false,$context);
    if (!$html) return null;
    $dom = new DOMDocument();
    // Afegim el prefix per a una correcta interpretació d'UTF-8
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXPath($dom);
    $node = $xpath->query("//div[contains(@class,'text-maquetat')]");
    return ($node->length>0) ? _normalize_text($xpath->evaluate("string(.)",$node->item(0))) : null;
}