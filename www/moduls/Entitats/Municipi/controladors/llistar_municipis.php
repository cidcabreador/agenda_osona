<?php
// moduls/Entitats/Municipi/controladors/llistar_municipis.php

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$titol = 'Gestió de Municipis';

try {
    $stmt = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC");
    $municipis = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error en llistar municipis: " . $e->getMessage());
    $municipis = [];
    afegirToast("Error en carregar la llista de municipis.", "error");
}

// CORREGIT: El controlador ara busca a la carpeta 'vistes'
include __DIR__ . '/../vistes/llistar_municipis.php';