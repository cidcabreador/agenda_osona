<?php
// moduls/Entitats/Tipologia/vistes/llistar_tipologies.php (Amb botó d'esborrar)
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
        <a href="index.php?accio=desar_tipologies" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Crear Nova Subcategoria
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Nom de la Subcategoria</th>
                        <th scope="col">Categoria Principal</th>
                        <th scope="col" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subcategories)): ?>
                        <?php foreach ($subcategories as $subcategoria): ?>
                            <tr>
                                <td><?php echo e($subcategoria['nom']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo e($subcategoria['nom_categoria'] ?? 'Sense assignar'); ?></span></td>
                                <td class="text-center">
                                     <div class="btn-group">
                                        <a href="index.php?accio=desar_tipologies&id=<?php echo e($subcategoria['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="POST" action="index.php?accio=desar_tipologies" onsubmit="return confirm('Estàs segur que vols esborrar aquesta subcategoria?');" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo e($subcategoria['id']); ?>">
                                            <input type="hidden" name="accio_formulari" value="esborrar">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Esborrar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No s'han trobat subcategories.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>