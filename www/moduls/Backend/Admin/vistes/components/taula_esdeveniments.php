<?php
// moduls/Backend/Admin/vistes/components/taula_esdeveniments.php (Lògica de categories més robusta)
?>

<style>
    .truncate-cell { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .table th, .table td { vertical-align: middle; }
    .category-badge { color: white; padding: 0.35em 0.65em; font-size: .85em; font-weight: 600; border-radius: 0.375rem; margin-right: 4px; display: inline-block; margin-bottom: 2px; }
</style>

<?php if (!empty($esdeveniments)): ?>
    <?php foreach ($esdeveniments as $esdeveniment): ?>
        <tr>
            <td class="truncate-cell" title="<?php echo e($esdeveniment['nom']); ?>"><?php echo e($esdeveniment['nom']); ?></td>
            <td><?php echo e(date('d/m/Y', strtotime($esdeveniment['data_inici']))); ?></td>
            <td class="truncate-cell" title="<?php echo e($esdeveniment['municipis']); ?>"><?php echo e($esdeveniment['municipis']); ?></td>
            <td>
                <?php
                // CORRECCIÓ: Comprovem que 'categories_data' no estigui buit abans de processar
                if (!empty($esdeveniment['categories_data'])) {
                    $categories_array = explode(',', $esdeveniment['categories_data']);
                    foreach ($categories_array as $cat_data) {
                        // Comprovem que cada element tingui el format correcte (nom|color)
                        if (strpos($cat_data, '|') !== false) {
                            list($nom_cat, $color_cat) = explode('|', $cat_data);
                            echo '<span class="category-badge" style="background-color: ' . e($color_cat) . ';">' . e($nom_cat) . '</span>';
                        }
                    }
                }
                ?>
            </td>
            <td class="truncate-cell" title="<?php echo e($esdeveniment['organitzadors']); ?>"><?php echo e($esdeveniment['organitzadors']); ?></td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <a href="index.php?accio=veure_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-sm btn-outline-primary" title="Veure"><i class="fas fa-eye"></i></a>
                    <a href="index.php?accio=guardar_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-pencil-alt"></i></a>
                    <form method="POST" action="index.php?accio=guardar_esdeveniment" onsubmit="return confirm('Estàs segur?');" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo e($esdeveniment['id']); ?>">
                        <input type="hidden" name="accio_formulari" value="esborrar">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Esborrar"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center text-muted py-4">No s'han trobat esdeveniments amb els filtres seleccionats.</td></tr>
<?php endif; ?>