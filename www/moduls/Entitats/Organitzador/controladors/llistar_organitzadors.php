<?php
if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$titol = 'Gestió d\'Organitzadors';
$items_per_pagina = 25;
$pagina_actual = 1;

try {
    $total_organitzadors = $pdo->query("SELECT COUNT(*) FROM organitzadors")->fetchColumn();
    $total_pagines = ceil($total_organitzadors / $items_per_pagina);

    $stmt = $pdo->prepare("SELECT id, nom, email, rol FROM organitzadors ORDER BY nom ASC LIMIT :limit OFFSET 0");
    $stmt->bindValue(':limit', $items_per_pagina, PDO::PARAM_INT);
    $stmt->execute();
    $organitzadors = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error en llistar organitzadors: " . $e->getMessage());
    $organitzadors = [];
    $total_organitzadors = 0;
    $total_pagines = 1;
    afegirToast("Error en carregar la llista d'organitzadors.", "error");
}

include __DIR__ . '/../vistes/llistar_organitzadors.php';