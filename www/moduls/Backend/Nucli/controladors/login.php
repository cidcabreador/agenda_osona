<?php
// moduls/Backend/Nucli/controladors/login.php

if (estaAutenticat()) {
    redireccionar('index.php?accio=escriptori_principal');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        $error = 'El correu electrònic i la contrasenya són obligatoris.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, email, password, rol FROM organitzadors WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $organitzador = $stmt->fetch();

        if ($organitzador && password_verify($password, $organitzador['password'])) {
            session_regenerate_id(true);
            $_SESSION['organitzador_id'] = $organitzador['id'];
            $_SESSION['organitzador_nom'] = $organitzador['nom'];
            $_SESSION['organitzador_rol'] = $organitzador['rol'];

            afegirToast('Sessió iniciada correctament. Benvingut/da!', 'exit');
            redireccionar('index.php?accio=escriptori_principal');
        } else {
            $error = 'El correu electrònic o la contrasenya no són correctes.';
        }
    }
}

// Mostrem la plantilla de login. Aquesta és especial i no utilitza el layout.
include __DIR__ . '/../../../../plantilles/login.php';
exit();