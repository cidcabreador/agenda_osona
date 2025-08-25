<?php
// moduls/Entitats/Tipologia/controladors/desar_tipologies.php (Amb funcionalitat d'esborrar)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$errors = [];
$subcategoria = ['id' => null, 'id_categoria' => null, 'nom' => ''];
$titol = 'Crear Nova Subcategoria';

try {
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    $errors[] = "Error en carregar les categories principals.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accio_formulari = $_POST['accio_formulari'] ?? 'guardar';

    // ===== NOU: Lògica per gestionar l'esborrat =====
    if ($accio_formulari === 'esborrar') {
        if ($id) {
            // Comprovem si la subcategoria està en ús per algun esdeveniment
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM esdeveniment_subcategories WHERE id_subcategoria = :id");
            $stmt_check->execute(['id' => $id]);
            
            if ($stmt_check->fetchColumn() > 0) {
                afegirToast('No es pot esborrar, la subcategoria està en ús per un o més esdeveniments.', 'error');
            } else {
                try {
                    $stmt_delete = $pdo->prepare("DELETE FROM subcategories WHERE id = :id");
                    $stmt_delete->execute(['id' => $id]);
                    afegirToast('Subcategoria esborrada correctament.', 'exit');
                } catch (PDOException $e) {
                    afegirToast('Error en esborrar la subcategoria.', 'error');
                    error_log($e->getMessage());
                }
            }
        }
        redireccionar('index.php?accio=llistar_tipologies');
        return;
    }

    // --- La lògica de desar (crear/editar) es manté igual ---
    $subcategoria['nom'] = htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8');
    $subcategoria['id_categoria'] = filter_input(INPUT_POST, 'id_categoria', FILTER_VALIDATE_INT);

    if (empty($subcategoria['nom'])) $errors[] = "El nom és obligatori.";
    if (empty($subcategoria['id_categoria'])) $errors[] = "Has de seleccionar una categoria principal.";

    if (empty($errors)) {
        try {
            $params = [
                'nom' => $subcategoria['nom'],
                'id_categoria' => $subcategoria['id_categoria']
            ];
            if ($id) {
                $params['id'] = $id;
                $stmt = $pdo->prepare("UPDATE subcategories SET nom = :nom, id_categoria = :id_categoria WHERE id = :id");
            } else {
                $stmt = $pdo->prepare("INSERT INTO subcategories (nom, id_categoria) VALUES (:nom, :id_categoria)");
            }
            $stmt->execute($params);
            afegirToast($id ? 'Subcategoria actualitzada.' : 'Subcategoria creada.', 'exit');
            redireccionar('index.php?accio=llistar_tipologies');
        } catch (PDOException $e) {
            $errors[] = "S'ha produït un error en desar les dades.";
            error_log($e->getMessage());
        }
    }
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM subcategories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($data = $stmt->fetch()) {
            $subcategoria = $data;
            $titol = 'Editar Subcategoria';
        }
    }
}

include __DIR__ . '/../vistes/formulari_tipologia.php';