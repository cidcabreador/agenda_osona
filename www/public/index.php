<?php
// public/index.php (Versió Definitiva del Router)

// Activar temporalment els errors per a depuració
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/ajudants.php';

$pdo = connectar();
if (!$pdo) die('Error crític de connexió amb la base de dades.');

$accio = $_GET['accio'] ?? 'llistar_esdeveniments';

// Llista d'accions que són públiques
$accions_publiques = ['llistar_esdeveniments', 'veure_esdeveniment', 'login', 'subscriure', 'llistar_mercats_public', 'events_json', 'calendari_json'];

// Llista d'accions que NOMÉS retornen dades (JSON) i no han de carregar la plantilla
$accions_ajax = ['filtrar_esdeveniments_ajax', 'filtrar_organitzadors_ajax', 'executar_robot_ajax'];


// Comprovació de seguretat: si l'acció no és pública ni AJAX, cal estar autenticat
if (!in_array($accio, $accions_publiques) && !in_array($accio, $accions_ajax) && !estaAutenticat()) {
    redireccionar('index.php?accio=login');
}

$base_path = __DIR__ . '/../moduls/';
$paths_a_comprovar = [
    $base_path . 'Portal/controladors/',
    $base_path . 'Backend/Nucli/controladors/',
    $base_path . 'Backend/Admin/controladors/',
    $base_path . 'Backend/Organitzador/controladors/',
    $base_path . 'Entitats/Esdeveniment/controladors/',
    $base_path . 'Entitats/Municipi/controladors/',
    $base_path . 'Entitats/Subcategories/controladors/',
    $base_path . 'Entitats/Organitzador/controladors/',
    $base_path . 'Entitats/Mercat/controladors/',
    $base_path . 'Entitats/Categoria/controladors/',
    $base_path . 'Entitats/Validacio/controladors/',
	
];

$fitxer_accio = null;
foreach ($paths_a_comprovar as $path) {
    if (file_exists($path . $accio . '.php')) {
        $fitxer_accio = $path . $accio . '.php';
        break;
    }
}

if ($fitxer_accio) {
    // Si l'acció és de tipus AJAX, simplement l'executem i parem. No necessita plantilla.
    if (in_array($accio, $accions_ajax)) {
        include $fitxer_accio;
        exit();
    }
    // Per a la resta d'accions, capturem el seu contingut per posar-lo dins la plantilla
    ob_start();
    include $fitxer_accio;
    $contingut = ob_get_clean();
} else {
    http_response_code(404);
    ob_start();
    echo '<h1>Error 404 - Pàgina no trobada</h1><p>L\'acció sol·licitada <strong>' . e($accio) . '</strong> no existeix.</p>';
    $contingut = ob_get_clean();
}

// Accions especials que no fan servir cap plantilla (com el login o el logout)
$accions_sense_layout = ['login', 'logout'];
if (in_array($accio, $accions_sense_layout)) {
    echo $contingut;
} else {
    // Decidim quina plantilla utilitzar: la pública o la del panell d'administració
    $layout = in_array($accio, $accions_publiques) ? 'layout_public.php' : 'layout.php';
    include __DIR__ . '/../plantilles/' . $layout;
}