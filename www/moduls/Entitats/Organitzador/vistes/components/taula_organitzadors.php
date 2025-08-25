<?php if (!empty($organitzadors)): foreach ($organitzadors as $organitzador): ?>
    <tr>
        <td><?php echo e($organitzador['nom']); ?></td>
        <td><?php echo e($organitzador['email']); ?></td>
        <td>
            <?php if ($organitzador['rol'] === 'admin'): ?>
                <span class="badge bg-danger">Administrador</span>
            <?php else: ?>
                <span class="badge bg-secondary">Organitzador</span>
            <?php endif; ?>
        </td>
        <td class="text-center">
            <div class="btn-group">
                <a href="index.php?accio=desar_organitzadors&id=<?php echo e($organitzador['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                <form method="POST" action="index.php?accio=desar_organitzadors" onsubmit="return confirm('Estàs segur?');" class="d-inline">
                    <input type="hidden" name="id" value="<?php echo e($organitzador['id']); ?>">
                    <input type="hidden" name="accio_formulari" value="esborrar">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Esborrar" <?php echo ($organitzador['id'] == ($_SESSION['organitzador_id'] ?? '')) ? 'disabled' : ''; ?>><i class="fas fa-trash-alt"></i></button>
                </form>
            </div>
        </td>
    </tr>
<?php endforeach; else: ?>
    <tr><td colspan="4" class="text-center text-muted py-4">No s'han trobat organitzadors amb els filtres seleccionats.</td></tr>
<?php endif; ?>