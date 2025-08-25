<?php
// moduls/Backend/Organitzador/vistes/panell_organitzadors.php
?>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
        <a href="index.php?accio=guardar_esdeveniment" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Crear Nou Esdeveniment
        </a>
    </div>
    <div class="card-body">
        <?php include __DIR__ . '/components/filtres_taula_esdeveniments.php'; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5 class="mb-0">Llistat dels Teus Esdeveniments</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Nom Esdeveniment</th>
                        <th scope="col">Data Inici</th>
                        <th scope="col">Municipi</th>
                        <th scope="col">Tipologia</th>
                        <th scope="col" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody id="taula-esdeveniments-body">
                    <?php include __DIR__ . '/components/taula_esdeveniments.php'; ?>
                </tbody>
            </table>
            <div id="loading-indicator" class="d-none text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregant...</span>
                </div>
            </div>
        </div>
    </div>
</div>