<?php
// moduls/Entitats/Organitzador/controladors/filtrar_organitzadors_ajax.php (Cerca per text corregida)

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../src/ajudants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!esAdmin()) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Accés denegat.']);
    exit();
}

$pdo = connectar();

// LÒGICA DE PAGINACIÓ I ORDENACIÓ
$items_per_pagina = 25;
$pagina_actual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($pagina_actual - 1) * $items_per_pagina;

$sort_by = $_GET['sort_by'] ?? 'nom';
$sort_order = $_GET['sort_order'] ?? 'ASC';
$columnes_permeses = ['nom', 'email', 'rol'];
if (!in_array($sort_by, $columnes_permeses)) $sort_by = 'nom';
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) $sort_order = 'ASC';

// LÒGICA DE FILTRES
$cerca_text = $_GET['cerca'] ?? '';
$cerca_rol = $_GET['rol'] ?? '';

$condicions = [];
$parametres = [];

// ===== CORRECCIÓ A LA LÒGICA DE CERCA PER TEXT =====
if (!empty($cerca_text)) {
    // Usem dos paràmetres diferents per evitar ambigüitats
    $condicions[] = "(nom LIKE :cerca_nom OR email LIKE :cerca_email)";
    $parametres[':cerca_nom'] = '%' . $cerca_text . '%';
    $parametres[':cerca_email'] = '%' . $cerca_text . '%';
}
if (!empty($cerca_rol)) {
    $condicions[] = "rol = :rol";
    $parametres[':rol'] = $cerca_rol;
}

$clausula_where = !empty($condicions) ? "WHERE " . implode(' AND ', $condicions) : "";

try {
    // Comptem el total de resultats
    $sql_count = "SELECT COUNT(*) FROM organitzadors $clausula_where";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($parametres);
    $total_organitzadors = $stmt_count->fetchColumn();
    $total_pagines = $total_organitzadors > 0 ? ceil($total_organitzadors / $items_per_pagina) : 1;

    // Obtenim els resultats de la pàgina actual
    $sql_select = "SELECT id, nom, email, rol FROM organitzadors $clausula_where ORDER BY $sort_by $sort_order LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql_select);
    $stmt->bindValue(':limit', $items_per_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    foreach ($parametres as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $organitzadors = $stmt->fetchAll();

    // Generem l'HTML per a la taula i la paginació
    ob_start();
    include __DIR__ . '/../vistes/components/taula_organitzadors.php';
    $taula_html = ob_get_clean();

    ob_start();
    include __DIR__ . '/../vistes/components/paginacio_organitzadors.php';
    $paginacio_html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode(['taula_html' => $taula_html, 'paginacio_html' => $paginacio_html]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error AJAX llistant organitzadors: " . $e->getMessage());
    echo json_encode(['error' => 'Error en processar la sol·licitud.']);
}

exit();