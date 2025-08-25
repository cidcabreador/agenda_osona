<?php
// moduls/Portal/controladors/llistar_mercats_public.php

$titol = 'Mercats Setmanals a Osona';

try {
    $stmt = $pdo->query("SELECT * FROM mercats_setmanals ORDER BY FIELD(dia_setmana, 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte', 'Diumenge'), poblacio ASC");
    $mercats = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error en llistar mercats públics: " . $e->getMessage());
    $mercats = [];
    afegirToast("Error en carregar la llista de mercats.", "error");
}

include __DIR__ . '/../vistes/llistar_mercats_public.php';