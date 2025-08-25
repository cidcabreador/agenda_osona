<?php
// moduls/Entitats/Esdeveniment/controladors/guardar_esdeveniment.php (Versió Final Segura)

if (!estaAutenticat()) {
    redireccionar('index.php?accio=login');
}

$errors = [];
$titol = 'Crear Nou Esdeveniment';
$esdeveniment = ['id' => null, 'nom' => '', 'descripcio' => '', 'data_inici' => '', 'data_fi' => null, 'hora' => null, 'adreca' => null, 'preu' => null, 'imatge' => null, 'latitud' => null, 'longitud' => null];
$municipis_seleccionats = [];
$subcategories_seleccionades = [];
$organitzadors_seleccionats = [];
$perfils_edat_seleccionats = [];

function verificarPropietat($pdo, $id_esdeveniment) {
    if (esAdmin()) return true;
    $id_organitzador = $_SESSION['organitzador_id'];
    $stmt = $pdo->prepare("SELECT 1 FROM esdeveniment_organitzadors WHERE id_esdeveniment = ? AND id_organitzador = ?");
    $stmt->execute([$id_esdeveniment, $id_organitzador]);
    return $stmt->fetchColumn() !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if ($id && !verificarPropietat($pdo, $id)) {
        afegirToast("No tens permisos per modificar aquest esdeveniment.", "error");
        redireccionar(esAdmin() ? 'index.php?accio=panell_admin' : 'index.php?accio=panell_organitzadors');
    }

    $accio_formulari = $_POST['accio_formulari'] ?? 'guardar';

    if ($accio_formulari === 'esborrar' && $id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM esdeveniments WHERE id = ?");
            $stmt->execute([$id]);
            afegirToast('Esdeveniment esborrat correctament.', 'exit');
        } catch (PDOException $e) {
            error_log("Error en esborrar esdeveniment: " . $e->getMessage());
            afegirToast('Error en esborrar l\'esdeveniment.', 'error');
        }
        redireccionar(esAdmin() ? 'index.php?accio=panell_admin' : 'index.php?accio=panell_organitzadors');
        return;
    }
    
    $esdeveniment_data = [
        'nom' => htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'),
        'descripcio' => htmlspecialchars($_POST['descripcio'] ?? '', ENT_QUOTES, 'UTF-8'),
        'data_inici' => $_POST['data_inici'] ?? '',
        'data_fi' => empty($_POST['data_fi']) ? null : $_POST['data_fi'],
        'hora' => empty($_POST['hora']) ? null : htmlspecialchars($_POST['hora'], ENT_QUOTES, 'UTF-8'),
        'adreca' => htmlspecialchars($_POST['adreca'] ?? '', ENT_QUOTES, 'UTF-8'),
        'preu' => htmlspecialchars($_POST['preu'] ?? '', ENT_QUOTES, 'UTF-8'),
        'latitud' => filter_input(INPUT_POST, 'latitud', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
        'longitud' => filter_input(INPUT_POST, 'longitud', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
    ];

    $municipis_seleccionats = $_POST['municipis'] ?? [];
    $subcategories_seleccionades = $_POST['subcategories'] ?? [];
    $perfils_edat_seleccionats = $_POST['perfils_edat'] ?? [];
    $organitzadors_seleccionats = esAdmin() ? ($_POST['organitzadors'] ?? []) : [$_SESSION['organitzador_id']];

    if (empty($esdeveniment_data['nom'])) $errors[] = "El nom és obligatori.";
    if (empty($esdeveniment_data['data_inici'])) $errors[] = "La data d'inici és obligatòria.";
    if (empty($municipis_seleccionats)) $errors[] = "Cal seleccionar almenys un municipi.";
    if (empty($subcategories_seleccionades)) $errors[] = "Cal seleccionar almenys una subcategoria.";
    if (empty($organitzadors_seleccionats)) $errors[] = "Cal assignar l'esdeveniment almenys a un organitzador.";

    $imatge_actual = htmlspecialchars($_POST['imatge_actual'] ?? '', ENT_QUOTES, 'UTF-8');
    $esdeveniment_data['imatge'] = $imatge_actual;

    if (isset($_FILES['imatge']) && $_FILES['imatge']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../../public/uploads/';
        $nomImatge = uniqid() . '-' . basename($_FILES['imatge']['name']);
        $rutaImatge = $uploadDir . $nomImatge;
        if (move_uploaded_file($_FILES['imatge']['tmp_name'], $rutaImatge)) {
            if ($imatge_actual && file_exists($uploadDir . $imatge_actual)) {
                @unlink($uploadDir . $imatge_actual);
            }
            $esdeveniment_data['imatge'] = $nomImatge;
        } else {
            $errors[] = "Error en pujar la imatge.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            if ($id) {
                $sql = "UPDATE esdeveniments SET nom=:nom, descripcio=:descripcio, data_inici=:data_inici, data_fi=:data_fi, hora=:hora, adreca=:adreca, preu=:preu, imatge=:imatge, latitud=:latitud, longitud=:longitud WHERE id=:id";
                $esdeveniment_data['id'] = $id;
            } else {
                $sql = "INSERT INTO esdeveniments (nom, descripcio, data_inici, data_fi, hora, adreca, preu, imatge, latitud, longitud) VALUES (:nom, :descripcio, :data_inici, :data_fi, :hora, :adreca, :preu, :imatge, :latitud, :longitud)";
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($esdeveniment_data);
            $id_esdeveniment = $id ?: $pdo->lastInsertId();

            function actualitzarPivote($pdo, $id_esdeveniment, $taula_pivote, $columna_pivote, $valors_nous) {
                $pdo->prepare("DELETE FROM $taula_pivote WHERE id_esdeveniment = ?")->execute([$id_esdeveniment]);
                if (!empty($valors_nous) && is_array($valors_nous)) {
                    $stmt_insert = $pdo->prepare("INSERT INTO $taula_pivote (id_esdeveniment, $columna_pivote) VALUES (?, ?)");
                    foreach ($valors_nous as $valor_id) {
                        $stmt_insert->execute([$id_esdeveniment, $valor_id]);
                    }
                }
            }
            
            actualitzarPivote($pdo, $id_esdeveniment, 'esdeveniment_municipis', 'id_municipi', $municipis_seleccionats);
            actualitzarPivote($pdo, $id_esdeveniment, 'esdeveniment_subcategories', 'id_subcategoria', $subcategories_seleccionades);
            actualitzarPivote($pdo, $id_esdeveniment, 'esdeveniment_organitzadors', 'id_organitzador', $organitzadors_seleccionats);
            actualitzarPivote($pdo, $id_esdeveniment, 'esdeveniment_perfils_edat', 'id_perfil_edat', $perfils_edat_seleccionats);

            $pdo->commit();
            afegirToast('Esdeveniment desat correctament.', 'exit');
            redireccionar(esAdmin() ? 'index.php?accio=panell_admin' : 'index.php?accio=panell_organitzadors');
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error en desar esdeveniment: " . $e->getMessage());
            $errors[] = "S'ha produït un error en desar les dades a la base de dades.";
        }
    }
    
    $esdeveniment = array_merge($esdeveniment, $_POST);

} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        if (!verificarPropietat($pdo, $id)) {
             afegirToast("No tens permisos.", "error");
             redireccionar('index.php?accio=panell_organitzadors');
        }
        $titol = 'Editar Esdeveniment';
        $stmt = $pdo->prepare("SELECT * FROM esdeveniments WHERE id = ?");
        $stmt->execute([$id]);
        $esdeveniment = $stmt->fetch();
        if (!$esdeveniment) {
            afegirToast("L'esdeveniment que intentes editar no existeix.", "error");
            redireccionar('index.php?accio=panell_admin');
        }

        $stmt_mun = $pdo->prepare("SELECT id_municipi FROM esdeveniment_municipis WHERE id_esdeveniment = ?");
        $stmt_mun->execute([$id]);
        $municipis_seleccionats = $stmt_mun->fetchAll(PDO::FETCH_COLUMN);

        $stmt_subcat = $pdo->prepare("SELECT id_subcategoria FROM esdeveniment_subcategories WHERE id_esdeveniment = ?");
        $stmt_subcat->execute([$id]);
        $subcategories_seleccionades = $stmt_subcat->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt_org = $pdo->prepare("SELECT id_organitzador FROM esdeveniment_organitzadors WHERE id_esdeveniment = ?");
        $stmt_org->execute([$id]);
        $organitzadors_seleccionats = $stmt_org->fetchAll(PDO::FETCH_COLUMN);

        $stmt_edat = $pdo->prepare("SELECT id_perfil_edat FROM esdeveniment_perfils_edat WHERE id_esdeveniment = ?");
        $stmt_edat->execute([$id]);
        $perfils_edat_seleccionats = $stmt_edat->fetchAll(PDO::FETCH_COLUMN);
    }
}

try {
    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom, id_categoria FROM subcategories ORDER BY id_categoria, nom ASC")->fetchAll();
    $perfils_edat = $pdo->query("SELECT id, nom FROM perfils_edat ORDER BY id")->fetchAll();
    $organitzadors = esAdmin() ? $pdo->query("SELECT id, nom FROM organitzadors ORDER BY nom ASC")->fetchAll() : [];
} catch (PDOException $e) {
    $municipis = []; $categories = []; $subcategories = []; $perfils_edat = []; $organitzadors = [];
    $errors[] = "Error en carregar les dades per al formulari.";
    error_log("Error carregant catàlegs per formulari esdeveniment: " . $e->getMessage());
}

include __DIR__ . '/../vistes/crear_esdeveniment.php';