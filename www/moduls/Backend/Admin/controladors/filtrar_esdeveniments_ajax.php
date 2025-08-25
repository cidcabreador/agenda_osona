<?php
// moduls/Backend/Admin/controladors/filtrar_esdeveniments_ajax.php (Ordenació de categories eliminada)

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

// LÒGICA DE PAGINACIÓ
$events_per_pagina = 25;
$pagina_actual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($pagina_actual - 1) * $events_per_pagina;

// Recollim paràmetres de cerca
$cerca_nom = $_GET['nom'] ?? '';
$cerca_municipi = filter_input(INPUT_GET, 'id_municipi', FILTER_VALIDATE_INT);
$cerca_categoria = filter_input(INPUT_GET, 'id_categoria', FILTER_VALIDATE_INT);
$cerca_subcategoria = filter_input(INPUT_GET, 'id_subcategoria', FILTER_VALIDATE_INT);
$data_des_de = $_GET['data_des_de'] ?? '';
$data_fins_a = $_GET['data_fins_a'] ?? '';

// Recollim paràmetres d'ordenació
$sort_by = $_GET['sort_by'] ?? 'data_inici';
$sort_order = $_GET['sort_order'] ?? 'DESC';
// ===== CANVI AQUÍ: Hem eliminat 'categories' de les columnes permeses =====
$columnes_permeses = ['nom', 'data_inici', 'municipis', 'organitzadors'];
if (!in_array($sort_by, $columnes_permeses)) {
    $sort_by = 'data_inici';
}
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC';
}

try {
    // ... (la resta de la lògica de filtres es manté igual)
    $condicions = [];
    $parametres = [];
    if (!empty($cerca_nom)) { $condicions[] = "e.nom LIKE :nom"; $parametres[':nom'] = '%' . $cerca_nom . '%'; }
    if (!empty($cerca_municipi)) { $condicions[] = "em.id_municipi = :id_municipi"; $parametres[':id_municipi'] = $cerca_municipi; }
    if (!empty($data_des_de)) { $condicions[] = "e.data_inici >= :data_des_de"; $parametres[':data_des_de'] = $data_des_de; }
    if (!empty($data_fins_a)) { $condicions[] = "e.data_inici <= :data_fins_a"; $parametres[':data_fins_a'] = $data_fins_a; }
    if (!empty($cerca_subcategoria)) { $condicions[] = "esc.id_subcategoria = :id_subcategoria"; $parametres[':id_subcategoria'] = $cerca_subcategoria; }
    elseif (!empty($cerca_categoria)) { $condicions[] = "sc.id_categoria = :id_categoria"; $parametres[':id_categoria'] = $cerca_categoria; }

    $clausula_where = !empty($condicions) ? "WHERE " . implode(' AND ', $condicions) : "";

    $sql_count = "SELECT COUNT(DISTINCT e.id)
                  FROM esdeveniments e
                  LEFT JOIN esdeveniment_municipis em ON e.id = em.id_esdeveniment
                  LEFT JOIN esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
                  LEFT JOIN subcategories sc ON esc.id_subcategoria = sc.id
                  $clausula_where";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($parametres);
    $total_esdeveniments = $stmt_count->fetchColumn();
    $total_pagines = $total_esdeveniments > 0 ? ceil($total_esdeveniments / $events_per_pagina) : 1;

    $sql_select = "
        SELECT
            e.id, e.nom, e.data_inici,
            GROUP_CONCAT(DISTINCT m.nom ORDER BY m.nom SEPARATOR ', ') AS municipis,
            GROUP_CONCAT(DISTINCT CONCAT(c.nom, '|', c.color) ORDER BY c.nom SEPARATOR ',') AS categories_data,
            GROUP_CONCAT(DISTINCT o.nom ORDER BY o.nom SEPARATOR ', ') AS organitzadors
        FROM esdeveniments e
        LEFT JOIN esdeveniment_municipis em ON e.id = em.id_esdeveniment
        LEFT JOIN municipis m ON em.id_municipi = m.id
        LEFT JOIN esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
        LEFT JOIN subcategories sc ON esc.id_subcategoria = sc.id
        LEFT JOIN categories c ON sc.id_categoria = c.id
        LEFT JOIN esdeveniment_organitzadors eo ON e.id = eo.id_esdeveniment
        LEFT JOIN organitzadors o ON eo.id_organitzador = o.id
        $clausula_where
        GROUP BY e.id
        ORDER BY $sort_by $sort_order
        LIMIT :limit OFFSET :offset
    ";

    $stmt_esdeveniments = $pdo->prepare($sql_select);
    $stmt_esdeveniments->bindValue(':limit', $events_per_pagina, PDO::PARAM_INT);
    $stmt_esdeveniments->bindValue(':offset', $offset, PDO::PARAM_INT);
    foreach ($parametres as $key => $value) {
        $stmt_esdeveniments->bindValue($key, $value);
    }
    $stmt_esdeveniments->execute();
    $esdeveniments = $stmt_esdeveniments->fetchAll();

    ob_start();
    include __DIR__ . '/../vistes/components/taula_esdeveniments.php';
    $contingut_taula = ob_get_clean();

    ob_start();
    include __DIR__ . '/../vistes/components/paginacio_esdeveniments.php';
    $contingut_paginacio = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'taula_html' => $contingut_taula,
        'paginacio_html' => $contingut_paginacio
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error a l'AJAX del panell d'admin: " . $e->getMessage());
    echo json_encode(['error' => 'S\'ha produït un error en carregar les dades.']);
}

exit();