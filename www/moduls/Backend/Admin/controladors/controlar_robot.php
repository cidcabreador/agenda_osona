<?php
// moduls/Backend/Admin/controladors/controlar_robot.php (Versió Final i Correcta)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}
$titol = 'Panell de Control del Robot Scraper';

// Gestió del formulari per descartar un esdeveniment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accio_robot'] ?? '') === 'descartar' && !empty($_POST['id_temporal'])) {
    $id_temporal = filter_input(INPUT_POST, 'id_temporal', FILTER_VALIDATE_INT);
    if ($id_temporal) {
        $stmt = $pdo->prepare("UPDATE esdeveniments_temporals SET estat = 'descartat' WHERE id = ?");
        $stmt->execute([$id_temporal]);
        afegirToast("Esdeveniment descartat.", 'info');
    }
    redireccionar('index.php?accio=controlar_robot');
}

// ===== LÒGICA PER TROBAR SCRAPERS DISPONIBLES =====
$municipis_amb_scraper = [];
$ruta_scrapers = __DIR__ . '/../../../../robot/';

// Escanejem tots els fitxers .php a la carpeta del robot
$fitxers_php = glob($ruta_scrapers . '*.php');

// Excloem els fitxers que no són scrapers executables (com les plantilles o els helpers)
$fitxers_a_excloure = ['plantilla_scraper.php', 'scraper_helpers.php'];
$fitxers_filtrats = array_filter($fitxers_php, function($file) use ($fitxers_a_excloure) {
    return !in_array(basename($file), $fitxers_a_excloure);
});

// Processem els fitxers trobats per crear la llista per al desplegable
foreach ($fitxers_filtrats as $fitxer) {
    // Obtenim el nom del fitxer sense l'extensió (ex: "roda_de_ter")
    $nom_fitxer = basename($fitxer, '.php');
    
    // Convertim el nom del fitxer a un nom més llegible (ex: "Roda De Ter")
    $nom_municipi = ucwords(str_replace('_', ' ', $nom_fitxer));
    
    $municipis_amb_scraper[] = [
        'nom_fitxer' => $nom_fitxer,
        'nom' => $nom_municipi
    ];
}

// Obtenim els esdeveniments pendents de validació
try {
    $esdeveniments_pendents = $pdo->query("SELECT * FROM esdeveniments_temporals WHERE estat = 'pendent' ORDER BY data_recollida DESC")->fetchAll();
} catch (PDOException $e) {
    error_log("Error en carregar els esdeveniments pendents: " . $e->getMessage());
    $esdeveniments_pendents = [];
    afegirToast("Error en carregar els esdeveniments pendents.", "error");
}

// Finalment, incloem la vista que mostrarà la pàgina
include __DIR__ . '/../vistes/controlar_robot.php';