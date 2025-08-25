<?php
// moduls/Backend/Admin/controladors/executar_robot_ajax.php (Versió Multi-Scraper)

ob_start();
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../src/ajudants.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!esAdmin()) {
    http_response_code(403);
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['log' => 'ERROR: Accés denegat.']);
    exit();
}

$scraper_seleccionat = $_GET['scraper'] ?? '';
// Validació de seguretat: només caràcters alfanumèrics i guions baixos per evitar atacs (Path Traversal)
if (empty($scraper_seleccionat) || !preg_match('/^[a-zA-Z0-9_]+$/', $scraper_seleccionat)) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['log' => 'ERROR: Nom de scraper no vàlid.']);
    exit();
}

$ruta_scraper = __DIR__ . '/../../../../robot/' . $scraper_seleccionat . '.php';

if (!file_exists($ruta_scraper)) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['log' => "ERROR: El fitxer scraper '$scraper_seleccionat.php' no s'ha trobat."]);
    exit();
}

require_once $ruta_scraper;
$nom_funcio = 'executar_scraper_' . $scraper_seleccionat;

if (!function_exists($nom_funcio)) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['log' => "ERROR: La funció principal '$nom_funcio' no existeix dins del fitxer scraper."]);
    exit();
}

$pdo = connectar();
$resultat_log = "El robot ha començat però no ha retornat cap missatge.";

try {
    $resultat_log = $nom_funcio($pdo);
} catch (Exception $e) {
    error_log("Error greu a l'executar scraper '$scraper_seleccionat': " . $e->getMessage());
    $resultat_log = "S'ha produït un error intern al servidor.\n";
    $resultat_log .= "Missatge per a depuració: " . $e->getMessage();
    http_response_code(500);
}

ob_end_clean();
header('Content-Type: application/json');
echo json_encode(['log' => $resultat_log]);
exit();