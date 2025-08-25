<?php
if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}
$titol = 'Gestió de Categories';
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    afegirToast("Error en carregar la llista de categories.", "error");
}
include __DIR__ . '/../vistes/llistar_categories.php';