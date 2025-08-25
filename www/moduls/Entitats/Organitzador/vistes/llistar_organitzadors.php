<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Filtres d'Organitzadors</h4>
        <a href="index.php?accio=desar_organitzadors" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Crear Nou Organitzador
        </a>
    </div>
    <div class="card-body">
        <form id="filtres-organitzadors-form">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="cerca-organitzador" class="form-label">Cerca per Nom o Email</label>
                    <input type="text" id="cerca-organitzador" name="cerca" class="form-control" placeholder="Escriu per buscar...">
                </div>
                <div class="col-md-4">
                    <label for="rol-organitzador" class="form-label">Filtra per Rol</label>
                    <select id="rol-organitzador" name="rol" class="form-select">
                        <option value="">Tots els rols</option>
                        <option value="admin">Administrador</option>
                        <option value="organitzador">Organitzador</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4" id="organitzadors-results-container">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Llistat d'Organitzadors</h4>
        <div id="filter-indicator-org" class="alert alert-info d-none p-2 mb-0" role="alert">
            <i class="fa-solid fa-filter me-2"></i> Filtres actius
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><a href="#" class="sort-link text-white" data-sort-by="nom">Nom <i class="fa-solid fa-sort"></i></a></th>
                        <th><a href="#" class="sort-link text-white" data-sort-by="email">Email <i class="fa-solid fa-sort"></i></a></th>
                        <th><a href="#" class="sort-link text-white" data-sort-by="rol">Rol <i class="fa-solid fa-sort"></i></a></th>
                        <th class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody id="taula-organitzadors-body">
                    <?php include __DIR__ . '/components/taula_organitzadors.php'; ?>
                </tbody>
            </table>
             <div id="loading-indicator-org" class="d-none text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
        <div id="paginacio-organitzadors-container" class="mt-3">
            <?php include __DIR__ . '/components/paginacio_organitzadors.php'; ?>
        </div>
    </div>
</div>