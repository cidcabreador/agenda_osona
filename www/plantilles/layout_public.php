<?php
// plantilles/layout_public.php (Versió amb botó d'accés al backend intel·ligent)
$titol_pagina = $titol ?? 'Agenda d\'Esdeveniments Osona';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titol_pagina); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
	
    
	
</head>
<body>
    
    <main>
        <section class="hero-section">
            <div class="hero-images">
                <div class="hero-img" style="background-image: url('img/1.jpg'); animation-delay: 0s;"></div>
                <div class="hero-img" style="background-image: url('img/2.jpg'); animation-delay: 12s;"></div>
                <div class="hero-img" style="background-image: url('img/3.jpg'); animation-delay: 24s;"></div>
                <div class="hero-img" style="background-image: url('img/4.jpg'); animation-delay: 36s;"></div>
                <div class="hero-img" style="background-image: url('img/5.jpg'); animation-delay: 48s;"></div>
                <div class="hero-img" style="background-image: url('img/6.jpg'); animation-delay: 60s;"></div>
            </div>

            <div class="hero-header">
                <div class="container d-flex justify-content-between align-items-center">
                    <a href="index.php" class="hero-brand-container text-decoration-none text-white">
                        <img src="img/agendaosona.gif" alt="Logo Agenda Osona" class="hero-brand-logo">
                        <div class="hero-brand-text">
                            <h1>Descobreix Osona</h1>
                            <p>Paisatges, cultura i gastronomia en un sol lloc.</p>
                        </div>
                    </a>

                    <nav class="hero-nav">
                        <ul class="nav">
                            <li class="nav-item"><a class="nav-link" href="index.php?accio=llistar_esdeveniments">Agenda</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?accio=llistar_mercats_public">Mercats</a></li>
                            <li class="nav-item"><a class="nav-link" href="index.php?accio=subscriure">Butlletí</a></li>
                            
                            <?php if (estaAutenticat()): ?>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" href="index.php?accio=escriptori_principal">
                                        <i class="fa-solid fa-user-shield me-1"></i> Panell
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?accio=login">Organitzadors</a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </nav>
                </div>
            </div>
            
        </section>

        <div class="container py-4">
            <?php echo $contingut; ?>
        </div>
    </main>

    <footer class="footer mt-auto py-4 bg-white border-top">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <span class="text-muted small">Amb el suport de:</span><br>
                <img src="img/consell apaisat.png" alt="Logo Consell Comarcal d'Osona" style="height: 40px;">
            </div>
            <p class="text-muted text-md-end mb-0">&copy; <?php echo date('Y'); ?> Agenda d'Esdeveniments Osona.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>