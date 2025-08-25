<?php
// moduls/Entitats/Mercat/controladors/gestionar_mercats.php

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$titol = 'Gestió de Mercats Setmanals';

try {
    $stmt = $pdo->query("SELECT * FROM mercats_setmanals ORDER BY FIELD(dia_setmana, 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte', 'Diumenge'), poblacio ASC");
    $mercats = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error en llistar mercats (admin): " . $e->getMessage());
    $mercats = [];
    afegirToast("Error en carregar la llista de mercats.", "error");
}

// CORREGIT: El controlador ara busca a la carpeta 'vistes'
include __DIR__ . '/../vistes/gestionar_mercats.php';