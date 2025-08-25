<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
        <a href="index.php?accio=desar_categoria" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Crear Nova Categoria
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Nom</th>
                        <th scope="col">Color</th>
                        <th scope="col">Icona</th>
                        <th scope="col" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): foreach ($categories as $categoria): ?>
                        <tr>
                            <td><?php echo e($categoria['nom']); ?></td>
                            <td><span style="background-color: <?php echo e($categoria['color']); ?>; padding: 5px 10px; border-radius: 5px; color: white;"><?php echo e($categoria['color']); ?></span></td>
                            <td><i class="fa-solid <?php echo e($categoria['icona_fa']); ?>"></i> (<?php echo e($categoria['icona_fa']); ?>)</td>
                            <td class="text-center">
                                 <div class="btn-group">
                                    <a href="index.php?accio=desar_categoria&id=<?php echo e($categoria['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                                    
                                    <form method="POST" action="index.php?accio=desar_categoria" onsubmit="return confirm('Estàs segur que vols esborrar aquesta categoria? Aquesta acció no es pot desfer.');" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo e($categoria['id']); ?>">
                                        <input type="hidden" name="accio_formulari" value="esborrar">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Esborrar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No s'han trobat categories.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>