<?php
// moduls/Entitats/Mercat/vistes/gestionar_mercats.php
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
        <a href="index.php?accio=desar_mercat" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Afegir Nou Mercat
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Dia de la Setmana</th>
                        <th scope="col">Població</th>
                        <th scope="col">Notes</th>
                        <th scope="col" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($mercats)): ?>
                        <?php foreach ($mercats as $mercat): ?>
                            <tr>
                                <td><span class="badge badge-dia badge-dia-<?php echo e($mercat['dia_setmana']); ?>"><?php echo e($mercat['dia_setmana']); ?></span></td>
                                <td><?php echo e($mercat['poblacio']); ?></td>
                                <td><?php echo e($mercat['notes']); ?></td>
                                <td class="text-center">
                                     <div class="btn-group">
                                        <a href="index.php?accio=desar_mercat&id=<?php echo e($mercat['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="POST" action="index.php?accio=desar_mercat" onsubmit="return confirm('Estàs segur que vols esborrar aquest mercat?');" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo e($mercat['id']); ?>">
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
                            <td colspan="4" class="text-center text-muted py-4">No s'han trobat mercats.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>