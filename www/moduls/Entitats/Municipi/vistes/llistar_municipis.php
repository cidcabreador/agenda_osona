<?php
// moduls/Entitats/Municipi/vistes/llistar_municipis.php
?>
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
        <a href="index.php?accio=desar_municipis" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Crear Nou Municipi
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Nom del Municipi</th>
                        <th scope="col" class="text-center">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($municipis)): ?>
                        <?php foreach ($municipis as $municipi): ?>
                            <tr>
                                <td><?php echo e($municipi['nom']); ?></td>
                                <td class="text-center">
                                     <div class="btn-group">
                                        <a href="index.php?accio=desar_municipis&id=<?php echo e($municipi['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="POST" action="index.php?accio=desar_municipis" onsubmit="return confirm('Estàs segur que vols esborrar aquest municipi?');" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo e($municipi['id']); ?>">
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
                            <td colspan="2" class="text-center text-muted py-4">No s'han trobat municipis.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>