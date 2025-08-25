<?php
// moduls/Backend/Nucli/controladors/logout.php

/**
 * Acció per tancar la sessió de l'usuari.
 */

// Assegurem que la sessió estigui iniciada abans de destruir-la
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esborrem totes les variables de sessió
$_SESSION = [];

// Si es fa servir una cookie de sessió, l'eliminem
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalment, destruïm la sessió
session_destroy();

// Afegim un missatge de comiat a la sessió (que es crearà de nou a la redirecció)
session_start(); // Necessari per poder desar el toast
afegirToast('Has tancat la sessió correctament. A reveure!', 'info');

// Redirigim a la pàgina de login
redireccionar('index.php?accio=login');