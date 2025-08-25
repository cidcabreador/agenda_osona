<?php
// moduls/Entitats/Tipologia/controladors/llistar_tipologies.php (Ara llistar_subcategories)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$titol = 'Gestió de Subcategories';

try {
    // NOU: La consulta ara uneix amb la taula 'categories' per obtenir el nom de la categoria pare
    $sql = "SELECT s.id, s.nom, c.nom AS nom_categoria 
            FROM subcategories s 
            LEFT JOIN categories c ON s.id_categoria = c.id 
            ORDER BY c.nom, s.nom ASC";
    $stmt = $pdo->query($sql);
    $subcategories = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error en llistar subcategories: " . $e->getMessage());
    $subcategories = [];
    afegirToast("Error en carregar la llista de subcategories.", "error");
}

include __DIR__ . '/../vistes/llistar_tipologies.php';