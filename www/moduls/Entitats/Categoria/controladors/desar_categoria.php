<?php
if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$errors = [];
$categoria = ['id' => null, 'nom' => '', 'color' => '#3a87ad', 'icona_fa' => 'fa-star'];
$titol = 'Crear Nova Categoria';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accio_formulari = $_POST['accio_formulari'] ?? 'guardar';

    // ===== NOU: Lògica per gestionar l'esborrat =====
    if ($accio_formulari === 'esborrar') {
        if ($id) {
            // Pas 1: Comprovem si la categoria està en ús per alguna subcategoria
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM subcategories WHERE id_categoria = :id");
            $stmt_check->execute(['id' => $id]);
            
            if ($stmt_check->fetchColumn() > 0) {
                // Si està en ús, mostrem un error i no esborrem
                afegirToast('No es pot esborrar la categoria perquè té subcategories associades.', 'error');
            } else {
                // Si no està en ús, procedim a esborrar
                try {
                    $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = :id");
                    $stmt_delete->execute(['id' => $id]);
                    afegirToast('Categoria esborrada correctament.', 'exit');
                } catch (PDOException $e) {
                    afegirToast('Error en esborrar la categoria.', 'error');
                    error_log($e->getMessage());
                }
            }
        }
        redireccionar('index.php?accio=llistar_categories');
        return; // Important: Aturem l'execució aquí
    }

    // --- La lògica de desar (crear/editar) es manté igual ---
    $categoria['nom'] = htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8');
    $categoria['color'] = htmlspecialchars($_POST['color'] ?? '', ENT_QUOTES, 'UTF-8');
    $categoria['icona_fa'] = htmlspecialchars($_POST['icona_fa'] ?? '', ENT_QUOTES, 'UTF-8');

    if (empty($categoria['nom'])) $errors[] = "El nom és obligatori.";

    if (empty($errors)) {
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE categories SET nom = :nom, color = :color, icona_fa = :icona_fa WHERE id = :id");
                $stmt->execute(['nom' => $categoria['nom'], 'color' => $categoria['color'], 'icona_fa' => $categoria['icona_fa'], 'id' => $id]);
                afegirToast('Categoria actualitzada.', 'exit');
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (nom, color, icona_fa) VALUES (:nom, :color, :icona_fa)");
                $stmt->execute(['nom' => $categoria['nom'], 'color' => $categoria['color'], 'icona_fa' => $categoria['icona_fa']]);
                afegirToast('Categoria creada.', 'exit');
            }
            redireccionar('index.php?accio=llistar_categories');
        } catch (PDOException $e) {
            $errors[] = "S'ha produït un error en desar les dades.";
        }
    }
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        if ($data) {
            $categoria = $data;
            $titol = 'Editar Categoria';
        }
    }
}

include __DIR__ . '/../vistes/formulari_categoria.php';