<?php
// moduls/Backend/Organitzador/vistes/components/taula_esdeveniments.php
?>
<?php if (!empty($esdeveniments)): ?>
    <?php foreach ($esdeveniments as $esdeveniment): ?>
        <tr class="border-b border-gray-200 hover:bg-gray-100">
            <td class="py-3 px-4 font-medium text-gray-900"><?php echo e($esdeveniment['nom']); ?></td>
            <td class="py-3 px-4"><?php echo e(date('d/m/Y', strtotime($esdeveniment['data_inici']))); ?></td>
            <td class="py-3 px-4"><?php echo e($esdeveniment['municipi']); ?></td>
            <td class="py-3 px-4">
			<span class="badge bg-success"><?php echo e($esdeveniment['subcategories']); ?></span>
            </td>
            <td class="py-3 px-4 text-center">
                <div class="btn-group">
                    <a href="index.php?accio=veure_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-sm btn-outline-primary" title="Veure">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="index.php?accio=guardar_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form method="POST" action="index.php?accio=guardar_esdeveniment" onsubmit="return confirm('Estàs segur que vols esborrar aquest esdeveniment?');" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo e($esdeveniment['id']); ?>">
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
        <td colspan="5" class="text-center py-6 text-muted">
            Encara no has creat cap esdeveniment. Anima't a crear el primer!
        </td>
    </tr>
<?php endif; ?>