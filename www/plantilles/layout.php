<?php
// plantilles/layout.php (Amb el logo de Osona Turisme)
$titol_pagina = $titol ?? 'Panell de Gestió - Agenda Osona';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titol_pagina); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        #sidebar-logo {
            max-width: 180px; /* Ajusta la mida del logo com vulguis */
            transition: transform 0.2s ease-in-out; /* Defineix l'animació */
        }
        #sidebar-logo:hover {
            transform: scale(1.08); /* Fa el logo una mica més gran al passar el ratolí per sobre */
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-3">
            <a href="index.php?accio=escriptori_principal">
                <img src="img/logoturisme.jpg" alt="Logo Osona Turisme" id="sidebar-logo">
            </a>
        </div>
        <div class="list-group list-group-flush">
            <a href="index.php?accio=escriptori_principal" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-home fa-fw me-2"></i>Inici</a>
            
            <?php if (esAdmin()): ?>
                <a href="index.php?accio=panell_admin" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-calendar-week fa-fw me-2"></i>Esdeveniments</a>
                <hr class="bg-secondary mx-3 my-1">
                <a href="index.php?accio=llistar_categories" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-palette fa-fw me-2"></i>Categories</a>
                <a href="index.php?accio=llistar_subcategories" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-tags fa-fw me-2"></i>Subcategories</a>
                <a href="index.php?accio=llistar_municipis" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-map-marked-alt fa-fw me-2"></i>Municipis</a>
                <a href="index.php?accio=gestionar_mercats" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-store fa-fw me-2"></i>Mercats</a>
                <hr class="bg-secondary mx-3 my-1">
                <a href="index.php?accio=llistar_organitzadors" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-users-cog fa-fw me-2"></i>Organitzadors</a>
                <a href="index.php?accio=controlar_robot" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-robot fa-fw me-2"></i>Robot</a>
            <?php else: ?>
                <a href="index.php?accio=panell_organitzadors" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-calendar-day fa-fw me-2"></i>Esdeveniments</a>
            <?php endif; ?>
            
            <hr class="bg-secondary mx-3 my-1">
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white" target="_blank"><i class="fa-solid fa-globe fa-fw me-2"></i>Web Públic</a>
        </div>
    </div>
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-primary btn-sm" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-user me-2"></i><?php echo e($_SESSION['organitzador_nom'] ?? 'Organitzador'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="index.php?accio=logout"><i class="fa-solid fa-right-from-bracket fa-fw me-2"></i>Tancar Sessió</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php echo $contingut; ?>
        </div>
    </div>
</div>
<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php include __DIR__ . '/components/toast.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js?v=1.1"></script>
<script src="js/admin.js?v=1.1"></script>
<script src="js/organitzadors.js?v=1.1"></script>


</body>
</html>