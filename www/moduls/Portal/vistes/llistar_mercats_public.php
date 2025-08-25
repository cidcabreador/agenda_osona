<?php
// moduls/Portal/vistes/llistar_mercats_public.php (Amb botó de tornar)
?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Tornar
        </a>
        <h1 class="h2 fw-bold text-center mb-0 mx-auto"><?php echo e($titol); ?></h1>
        <div style="width: 100px;"></div> </div>
    <div class="card-body p-4">
        <p class="text-center text-muted mb-4">Descobreix els mercats que donen vida als pobles de la comarca cada dia de la setmana.</p>
        
        <?php if (!empty($mercats)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 20%;">Dia de la Setmana</th>
                            <th scope="col" style="width: 30%;">Població</th>
                            <th scope="col">Notes / Horari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mercats as $mercat): ?>
                            <tr>
                                <td><span class="badge badge-dia badge-dia-<?php echo e($mercat['dia_setmana']); ?>"><?php echo e($mercat['dia_setmana']); ?></span></td>
                                <td class="fw-bold"><?php echo e($mercat['poblacio']); ?></td>
                                <td><?php echo e($mercat['notes']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Actualment no hi ha informació de mercats disponible.</div>
        <?php endif; ?>
    </div>
</div>