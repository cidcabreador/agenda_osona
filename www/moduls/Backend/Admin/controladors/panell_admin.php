<?php
// moduls/Backend/Admin/controladors/panell_admin.php (Paginació a 25)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$titol = 'Panell de Gestió d\'Esdeveniments';

// ===== CANVI AQUÍ: Paginació ajustada a 25 =====
$events_per_pagina = 25; 
$pagina_actual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($pagina_actual - 1) * $events_per_pagina;

// La resta del fitxer es manté igual per a la càrrega inicial
$cerca_nom = $_GET['nom'] ?? '';
$cerca_municipi = filter_input(INPUT_GET, 'id_municipi', FILTER_VALIDATE_INT);
$cerca_categoria = filter_input(INPUT_GET, 'id_categoria', FILTER_VALIDATE_INT);
$cerca_subcategoria = filter_input(INPUT_GET, 'id_subcategoria', FILTER_VALIDATE_INT);

try {
    $clausula_where = "";
    $parametres = [];
    
    $sql_count = "SELECT COUNT(DISTINCT e.id) FROM esdeveniments e";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($parametres);
    $total_esdeveniments = $stmt_count->fetchColumn();
    $total_pagines = ceil($total_esdeveniments / $events_per_pagina);

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
        GROUP BY e.id 
        ORDER BY e.data_inici DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt_esdeveniments = $pdo->prepare($sql_select);
    $stmt_esdeveniments->bindValue(':limit', $events_per_pagina, PDO::PARAM_INT);
    $stmt_esdeveniments->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_esdeveniments->execute();
    $esdeveniments = $stmt_esdeveniments->fetchAll();

    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom, id_categoria FROM subcategories ORDER BY nom ASC")->fetchAll();

} catch (PDOException $e) {
    error_log("Error al carregar el panell d'admin: " . $e->getMessage());
    afegirToast("S'ha produït un error en carregar les dades del panell.", "error");
    $esdeveniments = [];
    $total_pagines = 0;
}

include __DIR__ . '/../vistes/panell_admin.php';