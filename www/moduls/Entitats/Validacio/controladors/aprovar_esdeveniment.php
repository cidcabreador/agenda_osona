<?php
// moduls/Entitats/Validacio/controladors/aprovar_esdeveniment.php (Versió FINAL amb Redimensionat d'Imatge)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$errors = [];
$titol = 'Assistent de Validació d\'Esdeveniments';
$esdeveniment_temporal = null;
$suggeriments_organitzadors = [];
$subcategories_seleccionades = [];

function find_or_create_organizer_simple($pdo, $nom_organitzador) {
    $nom_organitzador = trim($nom_organitzador);
    if (empty($nom_organitzador)) return null;
    $stmt = $pdo->prepare("SELECT id FROM organitzadors WHERE LOWER(nom) = LOWER(?)");
    $stmt->execute([$nom_organitzador]);
    if ($id = $stmt->fetchColumn()) {
        return $id;
    } else {
        $stmt_insert = $pdo->prepare("INSERT INTO organitzadors (nom, password, rol, email) VALUES (?, ?, 'organitzador', NULL)");
        $password_aleatoria = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
        $stmt_insert->execute([$nom_organitzador, $password_aleatoria]);
        return $pdo->lastInsertId();
    }
}

/**
 * Redimensiona una imatge mantenint les proporcions.
 *
 * @param string $ruta_origen Ruta de la imatge original.
 * @param string $ruta_desti On es desarà la imatge nova.
 * @param int $max_ample Ample màxim.
 * @param int $max_alt Altura màxima.
 * @return bool True si ha tingut èxit, false si no.
 */
function redimensionar_imatge($ruta_origen, $ruta_desti, $max_ample, $max_alt) {
    try {
        list($ample_orig, $alt_orig, $tipus) = getimagesize($ruta_origen);
        if (!$ample_orig || !$alt_orig) return false;

        $ratio_orig = $ample_orig / $alt_orig;
        if ($max_ample / $max_alt > $ratio_orig) {
            $ample_nou = $max_alt * $ratio_orig;
            $alt_nou = $max_alt;
        } else {
            $alt_nou = $max_ample / $ratio_orig;
            $ample_nou = $max_ample;
        }

        $imatge_p = imagecreatetruecolor($ample_nou, $alt_nou);

        switch ($tipus) {
            case IMAGETYPE_JPEG:
                $imatge = imagecreatefromjpeg($ruta_origen);
                imagecopyresampled($imatge_p, $imatge, 0, 0, 0, 0, $ample_nou, $alt_nou, $ample_orig, $alt_orig);
                imagejpeg($imatge_p, $ruta_desti, 85); // Qualitat del 85%
                break;
            case IMAGETYPE_PNG:
                $imatge = imagecreatefrompng($ruta_origen);
                imagealphablending($imatge_p, false);
                imagesavealpha($imatge_p, true);
                imagecopyresampled($imatge_p, $imatge, 0, 0, 0, 0, $ample_nou, $alt_nou, $ample_orig, $alt_orig);
                imagepng($imatge_p, $ruta_desti);
                break;
            case IMAGETYPE_GIF:
                $imatge = imagecreatefromgif($ruta_origen);
                imagecopyresampled($imatge_p, $imatge, 0, 0, 0, 0, $ample_nou, $alt_nou, $ample_orig, $alt_orig);
                imagegif($imatge_p, $ruta_desti);
                break;
            default:
                // Si el format no és compatible, simplement copiem l'original
                return copy($ruta_origen, $ruta_desti);
        }
        
        imagedestroy($imatge);
        imagedestroy($imatge_p);
        return true;
    } catch (Exception $e) {
        error_log("Error en redimensionar imatge: " . $e->getMessage());
        return false;
    }
}


/**
 * Descarrega una imatge d'una URL, la redimensiona i la desa.
 */
function descarregar_i_processar_imatge($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) return null;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false, CURLOPT_FOLLOWLOCATION => true
    ]);
    $contingut = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 && $contingut) {
        $nom_base = uniqid() . '_' . basename(parse_url($url, PHP_URL_PATH));
        $ruta_temporal = sys_get_temp_dir() . '/' . $nom_base;
        
        if (file_put_contents($ruta_temporal, $contingut)) {
            $nom_fitxer_final = 'validat_' . $nom_base;
            $ruta_desti_final = __DIR__ . '/../../../../public/uploads/' . $nom_fitxer_final;
            
            if (redimensionar_imatge($ruta_temporal, $ruta_desti_final, 1200, 800)) {
                 @unlink($ruta_temporal);
                 return $nom_fitxer_final;
            }
        }
    }
    return null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... La resta del codi POST es manté igual, només canvia la funció que crida
    $id_temporal = filter_input(INPUT_POST, 'id_temporal', FILTER_VALIDATE_INT);
    $pdo->beginTransaction();
    try {
        $nom_imatge_local = descarregar_i_processar_imatge($_POST['imatge_original_url'] ?? null);

        $latitud = null;
        $longitud = null;
        if (!empty($_POST['municipis'][0])) {
            $primer_municipi_id = filter_var($_POST['municipis'][0], FILTER_VALIDATE_INT);
            if ($primer_municipi_id) {
                $stmt_coords = $pdo->prepare("SELECT latitud, longitud FROM municipis WHERE id = ?");
                $stmt_coords->execute([$primer_municipi_id]);
                if ($coords = $stmt_coords->fetch(PDO::FETCH_ASSOC)) {
                    $latitud = $coords['latitud'];
                    $longitud = $coords['longitud'];
                }
            }
        }

        $esdeveniment_data = [
            'nom' => $_POST['nom'] ?? '', 'descripcio' => $_POST['descripcio'] ?? '',
            'data_inici' => $_POST['data_inici'] ?? '', 'data_fi' => empty($_POST['data_fi']) ? null : $_POST['data_fi'],
            'hora' => empty($_POST['hora']) ? null : $_POST['hora'], 'adreca' => $_POST['adreca'] ?? '',
            'preu' => $_POST['preu'] ?? '', 'imatge' => $nom_imatge_local,
            'latitud' => $latitud, 'longitud' => $longitud
        ];

        $organitzadors_finals_ids = [];
        $decisions = $_POST['org_decision'] ?? [];
        foreach ($decisions as $key => $decision) {
            if ($decision === 'create') {
                $organitzadors_finals_ids[] = find_or_create_organizer_simple($pdo, $_POST['org_name'][$key]);
            } elseif ($decision === 'link') {
                $organitzadors_finals_ids[] = $_POST['org_link_id'][$key];
            }
        }
        $organitzadors_addicionals = $_POST['organitzadors_addicionals'] ?? [];
        $organitzadors_finals_ids = array_unique(array_filter(array_merge($organitzadors_finals_ids, $organitzadors_addicionals)));
        if (empty($organitzadors_finals_ids)) throw new Exception("Cal associar com a mínim un organitzador.");

        $sql = "INSERT INTO esdeveniments (nom, descripcio, data_inici, data_fi, hora, adreca, preu, imatge, latitud, longitud) VALUES (:nom, :descripcio, :data_inici, :data_fi, :hora, :adreca, :preu, :imatge, :latitud, :longitud)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($esdeveniment_data);
        $id_nou_esdeveniment = $pdo->lastInsertId();

        function actualitzarPivote($pdo, $id, $taula, $columna, $valors) {
            if (!empty($valors)) {
                $stmt = $pdo->prepare("INSERT INTO $taula (id_esdeveniment, $columna) VALUES (?, ?)");
                foreach ($valors as $valor) $stmt->execute([$id, $valor]);
            }
        }
        actualitzarPivote($pdo, $id_nou_esdeveniment, 'esdeveniment_municipis', 'id_municipi', $_POST['municipis'] ?? []);
        actualitzarPivote($pdo, $id_nou_esdeveniment, 'esdeveniment_subcategories', 'id_subcategoria', $_POST['subcategories'] ?? []);
        actualitzarPivote($pdo, $id_nou_esdeveniment, 'esdeveniment_organitzadors', 'id_organitzador', $organitzadors_finals_ids);
        
        $pdo->prepare("UPDATE esdeveniments_temporals SET estat = 'aprovat' WHERE id = ?")->execute([$id_temporal]);
        $pdo->commit();
        afegirToast('Esdeveniment validat i publicat correctament.', 'exit');
        redireccionar('index.php?accio=controlar_robot');
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors[] = "Error en desar: " . $e->getMessage();
    }
}


// ... La resta del codi (part GET) es manté exactament igual ...
$id_temporal = filter_input(INPUT_GET, 'id_temporal', FILTER_VALIDATE_INT);
if ($id_temporal) {
    $stmt = $pdo->prepare("SELECT * FROM esdeveniments_temporals WHERE id = ?");
    $stmt->execute([$id_temporal]);
    $esdeveniment_temporal = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($esdeveniment_temporal) {
        $organitzadors_bd = $pdo->query("SELECT id, nom FROM organitzadors ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        $soroll = ['Entrada:', 'Preu:', 'Gratuïta', 'Gratuït', 'Consultar'];
        $prefixes = ['Col·labora:', 'Organitza:', 'Amb el suport de:', 'Amb el suport d’'];
        $stop_words = ['de', 'la', 'el', 'i', 'a', 'els', 'les'];

        $noms_bruts = $esdeveniment_temporal['organitzadors'];
        $noms_sense_soroll = str_ireplace($soroll, '', $noms_bruts);
        $noms_sense_prefixos = str_ireplace($prefixes, '', $noms_sense_soroll);
        $noms_potencials = array_filter(array_map('trim', explode(',', $noms_sense_prefixos)));

        foreach ($noms_potencials as $nom_scraper) {
            $paraules_netes = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($nom_scraper));
            $paraules_clau = array_diff(explode(' ', $paraules_netes), $stop_words);
            $paraules_clau = array_filter($paraules_clau);

            if (count($paraules_clau) < 1) continue;

            $suggeriments_trobats = [];
            foreach ($organitzadors_bd as $org_bd) {
                $nom_bd_lower = strtolower($org_bd['nom']);
                $coincidencies = 0;
                foreach ($paraules_clau as $paraula) {
                    if (strpos($nom_bd_lower, $paraula) !== false) {
                        $coincidencies++;
                    }
                }
                
                if (($coincidencies >= 2) || (count($paraules_clau) == 1 && $coincidencies > 0)) {
                    similar_text(strtolower($nom_scraper), $nom_bd_lower, $percent);
                    $suggeriments_trobats[] = [
                        'id' => $org_bd['id'],
                        'nom' => $org_bd['nom'],
                        'percentatge' => round($percent)
                    ];
                }
            }
            
            usort($suggeriments_trobats, function($a, $b) {
                return $b['percentatge'] <=> $a['percentatge'];
            });

            $suggeriments_organitzadors[] = [
                'nom_original' => $nom_scraper,
                'suggeriments' => $suggeriments_trobats,
            ];
        }
    }
}

try {
    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom, id_categoria FROM subcategories ORDER BY id_categoria, nom ASC")->fetchAll();
    $tots_organitzadors = $pdo->query("SELECT id, nom FROM organitzadors ORDER BY nom ASC")->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Error en carregar les dades per al formulari: " . $e->getMessage();
}

include __DIR__ . '/../vistes/formulari_validacio.php';