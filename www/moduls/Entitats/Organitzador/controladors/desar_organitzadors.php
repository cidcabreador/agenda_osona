<?php
// moduls/Entitats/Organitzador/controladors/desar_organitzadors.php (Versió Corregida)

if (!esAdmin()) {
    http_response_code(403);
    die("Accés denegat.");
}

$errors = [];
// CORREGIT: Eliminem 'cognoms' que no existeix a la BD
$organitzador = ['id' => null, 'nom' => '', 'email' => '', 'rol' => 'organitzador'];
$titol = 'Crear Nou Organitzador';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $accio_formulari = $_POST['accio_formulari'] ?? 'guardar';

    if ($accio_formulari === 'esborrar') {
        if ($id) {
            if ($id == ($_SESSION['organitzador_id'] ?? '')) {
                 afegirToast('No pots esborrar el teu propi compte.', 'error');
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM organitzadors WHERE id = :id");
                    $stmt->execute(['id' => $id]);
                    afegirToast('Organitzador esborrat correctament.', 'exit');
                } catch (PDOException $e) {
                    error_log("Error en esborrar organitzador: " . $e->getMessage());
                    afegirToast('Error en esborrar l\'organitzador. Pot ser que tingui esdeveniments associats.', 'error');
                }
            }
        }
        redireccionar('index.php?accio=llistar_organitzadors');

    } else {
        $organitzador['nom'] = htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8');
        $organitzador['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $organitzador['rol'] = in_array($_POST['rol'], ['admin', 'organitzador']) ? $_POST['rol'] : 'organitzador';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($organitzador['nom'])) $errors[] = "El nom és obligatori.";
        if (empty($organitzador['email'])) $errors[] = "El format del correu electrònic no és vàlid.";
        if ($id === null && empty($password)) $errors[] = "La contrasenya és obligatòria per a nous organitzadors.";
        if (!empty($password) && $password !== $password_confirm) $errors[] = "Les contrasenyes no coincideixen.";
        
        $stmt = $pdo->prepare("SELECT id FROM organitzadors WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $organitzador['email'], 'id' => $id ?? 0]);
        if ($stmt->fetch()) {
            $errors[] = "Aquest correu electrònic ja està en ús.";
        }

        if (empty($errors)) {
            try {
                if ($id) {
                    $sql = "UPDATE organitzadors SET nom = :nom, email = :email, rol = :rol";
                    // CORREGIT: Eliminem 'cognoms'
                    $params = ['nom' => $organitzador['nom'], 'email' => $organitzador['email'], 'rol' => $organitzador['rol'], 'id' => $id];
                    if (!empty($password)) {
                        $sql .= ", password = :password";
                        $params['password'] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    $sql .= " WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    afegirToast('Organitzador actualitzat correctament.', 'exit');
                } else {
                    $sql = "INSERT INTO organitzadors (nom, email, password, rol) VALUES (:nom, :email, :password, :rol)";
                    // CORREGIT: Eliminem 'cognoms'
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['nom' => $organitzador['nom'], 'email' => $organitzador['email'], 'password' => password_hash($password, PASSWORD_DEFAULT), 'rol' => $organitzador['rol']]);
                    afegirToast('Organitzador creat correctament.', 'exit');
                }
                redireccionar('index.php?accio=llistar_organitzadors');
            } catch (PDOException $e) {
                error_log("Error en desar organitzador: " . $e->getMessage());
                $errors[] = "S'ha produït un error en desar les dades.";
            }
        }
    }
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        // CORREGIT: Eliminem 'cognoms'
        $stmt = $pdo->prepare("SELECT id, nom, email, rol FROM organitzadors WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($data = $stmt->fetch()) {
            $organitzador = $data;
            $titol = 'Editar Organitzador';
        } else {
            afegirToast("L'organitzador sol·licitat no existeix.", "error");
            redireccionar('index.php?accio=llistar_organitzadors');
        }
    }
}
// La vista ja estava correcta, la incloem
include __DIR__ . '/../vistes/formulari_organitzador.php';