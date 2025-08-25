<?php
// moduls/Portal/vistes/veure_esdeveniment.php (Versió Corregida Final)
?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-light p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
            <div>
                <h1 class="h2 fw-bold mb-1"><?php echo e($esdeveniment['nom']); ?></h1>
                <?php if (!empty($esdeveniment['tipologia'])): ?>
                    <span class="badge text-bg-primary fs-6"><?php echo e($esdeveniment['tipologia']); ?></span>
                <?php endif; ?>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Tornar
                </a>
                <?php if (estaAutenticat()): ?>
                    <a href="index.php?accio=guardar_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-warning">
                        <i class="fas fa-pencil-alt me-2"></i>Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body p-3 p-md-4">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-7">
                <?php if (!empty($esdeveniment['imatge'])): ?>
                    <img src="uploads/<?php echo e($esdeveniment['imatge']); ?>" alt="Imatge de <?php echo e($esdeveniment['nom']); ?>" class="img-fluid rounded shadow-sm mb-4 event-detail-img">
                <?php else: ?>
                    <div class="w-100 h-100 bg-secondary-subtle rounded d-flex align-items-center justify-content-center mb-4" style="min-height: 300px;">
                        <i class="fa-solid fa-image fa-5x text-body-tertiary"></i>
                    </div>
                <?php endif; ?>
                
                <h3 class="h4 fw-bold">Descripció</h3>
                <p class="text-body-secondary" style="white-space: pre-wrap;"><?php echo nl2br(e($esdeveniment['descripcio'])); ?></p>
            </div>

            <div class="col-lg-5">
                <div class="p-3 bg-light rounded">
                    <h3 class="h4 fw-bold mb-4">Detalls de l'Esdeveniment</h3>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-3">
                            <i class="fa-solid fa-calendar-day fa-fw text-primary mt-1 me-3 fs-5"></i>
                            <div>
                                <strong class="d-block">Data</strong>
                                <?php echo e(date('d/m/Y', strtotime($esdeveniment['data_inici']))); ?>
                                <?php if ($esdeveniment['data_fi']): ?>
                                     - <?php echo e(date('d/m/Y', strtotime($esdeveniment['data_fi']))); ?>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php if ($esdeveniment['hora']): ?>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fa-solid fa-clock fa-fw text-primary mt-1 me-3 fs-5"></i>
                            <div>
                                <strong class="d-block">Hora</strong>
                                <?php echo e($esdeveniment['hora']); ?>
                            </div>
                        </li>
                        <?php endif; ?>
                        <li class="d-flex align-items-start mb-3">
                            <i class="fa-solid fa-location-dot fa-fw text-primary mt-1 me-3 fs-5"></i>
                            <div>
                                <strong class="d-block">Lloc</strong>
                                <?php echo e($esdeveniment['municipis']); ?><br>
                                <small class="text-muted"><?php echo e($esdeveniment['adreca']); ?></small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-user-pen fa-fw text-primary mt-1 me-3 fs-5"></i>
                            <div>
                                <strong class="d-block">Organitzat per</strong>
                                <?php echo e($esdeveniment['organitzadors']); ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>