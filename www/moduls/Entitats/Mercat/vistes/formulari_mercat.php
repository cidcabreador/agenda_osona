<?php
// moduls/Entitats/Mercat/vistes/formulari_mercat.php
?>
<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">S'han trobat errors!</h5>
                <hr>
                <ul class="mb-0 list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="fa-solid fa-exclamation-triangle me-2"></i><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="index.php?accio=desar_mercat" method="POST">
            <?php if ($mercat['id']): ?>
                <input type="hidden" name="id" value="<?php echo e($mercat['id']); ?>">
            <?php endif; ?>
            <input type="hidden" name="accio_formulari" value="guardar">
            
            <div class="mb-3">
                <label for="dia_setmana" class="form-label fw-bold">Dia de la Setmana</label>
                <select name="dia_setmana" id="dia_setmana" class="form-select" required>
                    <option value="">Selecciona un dia</option>
                    <?php foreach ($dies_setmana as $dia): ?>
                        <option value="<?php echo e($dia); ?>" <?php echo ($mercat['dia_setmana'] == $dia) ? 'selected' : ''; ?>>
                            <?php echo e($dia); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="poblacio" class="form-label fw-bold">Població</label>
                <input type="text" name="poblacio" id="poblacio" value="<?php echo e($mercat['poblacio']); ?>" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label fw-bold">Notes (opcional)</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"><?php echo e($mercat['notes']); ?></textarea>
                <div class="form-text">Afegeix informació addicional, com l'horari o la ubicació exacta.</div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?accio=gestionar_mercats" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-2"></i>Cancel·lar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Desar Canvis
                </button>
            </div>
        </form>
    </div>
</div>