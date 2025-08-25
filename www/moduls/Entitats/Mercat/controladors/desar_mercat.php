<?php
// moduls/Entitats/Mercat/controladors/desar_mercat.php

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$errors = [];
$mercat = ['id' => null, 'dia_setmana' => '', 'poblacio' => '', 'notes' => ''];
$titol = 'Afegir Nou Mercat';
$dies_setmana = ['Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte', 'Diumenge'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accio_formulari = $_POST['accio_formulari'] ?? 'guardar';

    if ($accio_formulari === 'esborrar') {
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM mercats_setmanals WHERE id = :id");
                $stmt->execute(['id' => $id]);
                afegirToast('Mercat esborrat correctament.', 'exit');
            } catch (PDOException $e) {
                error_log("Error en esborrar mercat: " . $e->getMessage());
                afegirToast('Error en esborrar el mercat.', 'error');
            }
        }
        redireccionar('index.php?accio=gestionar_mercats');
    } else {
        $mercat['id'] = $id;
        $mercat['dia_setmana'] = in_array($_POST['dia_setmana'], $dies_setmana) ? $_POST['dia_setmana'] : null;
        $mercat['poblacio'] = filter_input(INPUT_POST, 'poblacio', FILTER_SANITIZE_STRING);
        $mercat['notes'] = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

        if (empty($mercat['dia_setmana'])) $errors[] = "El dia de la setmana és obligatori.";
        if (empty($mercat['poblacio'])) $errors[] = "La població és obligatòria.";

        if (empty($errors)) {
            try {
                $params = [
                    'dia_setmana' => $mercat['dia_setmana'],
                    'poblacio' => $mercat['poblacio'],
                    'notes' => $mercat['notes'] ?? null,
                ];
                if ($id) {
                    $params['id'] = $id;
                    $stmt = $pdo->prepare("UPDATE mercats_setmanals SET dia_setmana = :dia_setmana, poblacio = :poblacio, notes = :notes WHERE id = :id");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO mercats_setmanals (dia_setmana, poblacio, notes) VALUES (:dia_setmana, :poblacio, :notes)");
                }
                $stmt->execute($params);
                afegirToast($id ? 'Mercat actualitzat correctament.' : 'Mercat afegit correctament.', 'exit');
                redireccionar('index.php?accio=gestionar_mercats');
            } catch (PDOException $e) {
                error_log("Error en desar mercat: " . $e->getMessage());
                $errors[] = "S'ha produït un error en desar les dades.";
            }
        }
    }
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM mercats_setmanals WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        if ($data) {
            $mercat = $data;
            $titol = 'Editar Mercat';
        } else {
            afegirToast("El mercat sol·licitat no existeix.", "error");
            redireccionar('index.php?accio=gestionar_mercats');
        }
    }
}

include __DIR__ . '/../vistes/formulari_mercat.php';