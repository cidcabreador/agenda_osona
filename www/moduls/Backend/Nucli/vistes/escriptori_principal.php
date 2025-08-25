<?php
// moduls/Backend/Nucli/vistes/escriptori_principal.php (Disseny Definitivament Corregit)
?>
<div class="alert alert-primary" role="alert">
    <h4 class="alert-heading">Benvingut/da de nou, <?php echo e($_SESSION['organitzador_nom']); ?>!</h4>
    <p>Aquí tens un resum de l'activitat recent a l'agenda. Fes servir la barra lateral per navegar per les diferents seccions.</p>
</div>

<?php if (esAdmin() && !empty($estadistiques)): ?>
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-primary shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-1 fw-bold"><?php echo e($estadistiques['total_esdeveniments']); ?></div>
                    <div class="fs-5">Esdeveniments</div>
                </div>
                <i class="fa-solid fa-calendar-alt fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-1 fw-bold"><?php echo e($estadistiques['total_organitzadors']); ?></div>
                    <div class="fs-5">Organitzadors</div>
                </div>
                <i class="fa-solid fa-users fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-info shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-1 fw-bold"><?php echo e($estadistiques['total_municipis']); ?></div>
                    <div class="fs-5">Municipis</div>
                </div>
                <i class="fa-solid fa-map-marked-alt fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-warning shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="fs-1 fw-bold"><?php echo e($estadistiques['total_subscripcions']); ?></div>
                    <div class="fs-5">Subscriptors</div>
                </div>
                <i class="fa-solid fa-envelope-open-text fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <?php
    // ===== LÒGICA DE VISUALITZACIÓ CORREGIDA =====
    // Definim si la columna lateral s'ha de mostrar
    $mostrar_columna_lateral = esAdmin() && !empty($ultims_esdeveniments_creat);
    // Definim la classe de la columna principal en funció de si es mostra la lateral
    $classe_columna_principal = $mostrar_columna_lateral ? 'col-lg-8' : 'col-lg-12';
    ?>

    <div class="<?php echo $classe_columna_principal; ?>">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-star me-2"></i>Propers Esdeveniments</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($propers_esdeveniments)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($propers_esdeveniments as $esdeveniment): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold"><?php echo e($esdeveniment['nom']); ?></div>
                                    <small class="text-muted">
                                        <i class="fa-solid fa-location-dot me-1"></i><?php echo e($esdeveniment['municipi']); ?> - 
                                        <i class="fa-solid fa-clock ms-2 me-1"></i><?php echo e(date('d/m/Y', strtotime($esdeveniment['data_inici']))); ?>
                                    </small>
                                </div>
                                <a href="index.php?accio=veure_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-outline-primary btn-sm">
                                    Veure <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center">Actualment no hi ha esdeveniments programats.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if ($mostrar_columna_lateral): ?>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-plus me-2"></i>Darrers Esdeveniments Creats</h5>
            </div>
            <div class="card-body">
                 <?php if (!empty($ultims_esdeveniments_creat)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ultims_esdeveniments_creat as $esdeveniment): ?>
                             <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo e($esdeveniment['nom']); ?>
                                <a href="index.php?accio=guardar_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center">No s'han creat esdeveniments recentment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>