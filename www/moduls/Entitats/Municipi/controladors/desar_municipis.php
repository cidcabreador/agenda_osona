<?php
if (!esAdmin()) {
    http_response_code(403);
    die("Accés Denegat.");
}

$errors = [];
$municipi = ['id' => null, 'nom' => '', 'latitud' => null, 'longitud' => null];
$titol = 'Crear Nou Municipi';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $municipi['nom'] = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $municipi['latitud'] = filter_input(INPUT_POST, 'latitud', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $municipi['longitud'] = filter_input(INPUT_POST, 'longitud', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);

    if (empty($municipi['nom'])) $errors[] = "El nom del municipi és obligatori.";

    if (empty($errors)) {
        try {
            $params = [
                'nom' => $municipi['nom'],
                'latitud' => $municipi['latitud'],
                'longitud' => $municipi['longitud']
            ];
            if ($id) {
                $params['id'] = $id;
                $stmt = $pdo->prepare("UPDATE municipis SET nom = :nom, latitud = :latitud, longitud = :longitud WHERE id = :id");
            } else {
                $stmt = $pdo->prepare("INSERT INTO municipis (nom, latitud, longitud) VALUES (:nom, :latitud, :longitud)");
            }
            $stmt->execute($params);
            afegirToast($id ? 'Municipi actualitzat.' : 'Municipi creat.', 'exit');
            redireccionar('index.php?accio=llistar_municipis');
        } catch (PDOException $e) {
            error_log("Error en desar municipi: " . $e->getMessage());
            $errors[] = "S'ha produït un error en desar les dades.";
        }
    }
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM municipis WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($data = $stmt->fetch()) {
            $municipi = $data;
            $titol = 'Editar Municipi';
        }
    }
}

include __DIR__ . '/../vistes/formulari_municipi.php';