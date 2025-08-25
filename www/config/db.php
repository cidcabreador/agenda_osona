<?php
// config/db.php

/**
 * Fitxer de configuració de la base de dades.
 * Estableix una connexió PDO i la retorna.
 */

// Paràmetres de connexió a la base de dades
define('DB_HOST', 'localhost');
define('DB_NAME', 'agenda_osona');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sense contrasenya per a l'usuari root
define('DB_CHARSET', 'utf8mb4');

/**
 * Funció per connectar a la base de dades.
 *
 * @return PDO|null Retorna un objecte PDO en cas d'èxit, o null si hi ha un error.
 */
function connectar(): ?PDO
{
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $opcions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opcions);
        return $pdo;
    } catch (PDOException $e) {
        // En un entorn de producció, no mostraríem detalls de l'error.
        // Podríem registrar l'error en un fitxer de logs.
        error_log('Error de connexió a la BD: ' . $e->getMessage());
        // Mostrem un missatge genèric a l'usuari.
        die('Error: No s\'ha pogut connectar a la base de dades. Intenta-ho més tard.');
    }
}