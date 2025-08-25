<?php
// src/ajudants.php

function dd($data): void {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function redireccionar(string $url): void {
    header("Location: {$url}");
    exit();
}

function estaAutenticat(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['organitzador_id']);
}

function esAdmin(): bool {
    return estaAutenticat() && isset($_SESSION['organitzador_rol']) && $_SESSION['organitzador_rol'] === 'admin';
}

function e(?string $text): string {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function afegirToast(string $missatge, string $tipus = 'exit'): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['toast'] = [
        'missatge' => $missatge,
        'tipus' => $tipus,
    ];
}