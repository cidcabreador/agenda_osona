<?php
// moduls/Backend/Admin/vistes/panell_admin.php (Columna de categoria no ordenable)
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo e($titol); ?></h5>
        <a href="index.php?accio=guardar_esdeveniment" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-2"></i>Crear Nou Esdeveniment
        </a>
    </div>
    <div class="card-body">
        <?php include __DIR__ . '/components/filtres_taula_esdeveniments.php'; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header">
        <div id="filter-indicator" class="alert alert-info d-none justify-content-between align-items-center p-2 mb-0" role="alert">
            <span><i class="fa-solid fa-filter me-2"></i> S'estan mostrant resultats filtrats.</span>
            <button id="clear-filters-btn" type="button" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-xmark me-1"></i> Netejar Filtres</button>
        </div>
        <h5 class="mb-0" id="table-title">Llistat d'Esdeveniments</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered table-fixed">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 25%;"><a href="#" class="sort-link text-white" data-sort-by="nom">Nom Esdeveniment <i class="fa-solid fa-sort"></i></a></th>
                        <th style="width: 12%;"><a href="#" class="sort-link text-white" data-sort-by="data_inici">Data Inici <i class="fa-solid fa-sort"></i></a></th>
                        <th style="width: 20%;"><a href="#" class="sort-link text-white" data-sort-by="municipis">Municipi(s) <i class="fa-solid fa-sort"></i></a></th>
                        <th style="width: 18%;">Categoria(es)</th>
                        <th style="width: 15%;"><a href="#" class="sort-link text-white" data-sort-by="organitzadors">Organitzador(s) <i class="fa-solid fa-sort"></i></a></th>
                        <th style="width: 10%;" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody id="taula-esdeveniments-body">
                    <?php include __DIR__ . '/components/taula_esdeveniments.php'; ?>
                </tbody>
            </table>
            <div id="loading-indicator" class="d-none text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
        <div id="paginacio-container" class="mt-3">
            <?php include __DIR__ . '/components/paginacio_esdeveniments.php'; ?>
        </div>
    </div>
</div>