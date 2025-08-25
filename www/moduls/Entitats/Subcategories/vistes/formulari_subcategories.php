<?php
// moduls/Entitats/Tipologia/vistes/formulari_tipologia.php (Ara formulari_subcategoria)
?>
<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo e($titol); ?></h4>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">S'han trobat errors!</h5><hr>
                <ul class="mb-0 list-unstyled">
                    <?php foreach ($errors as $error): ?>
                        <li><i class="fa-solid fa-exclamation-triangle me-2"></i><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php?accio=desar_tipologies" method="POST">
            <?php if (isset($subcategoria['id']) && $subcategoria['id']): ?>
                <input type="hidden" name="id" value="<?php echo e($subcategoria['id']); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="id_categoria" class="form-label fw-bold">Categoria Principal</label>
                <select name="id_categoria" id="id_categoria" class="form-select" required>
                    <option value="">-- Selecciona una categoria --</option>
                    <?php foreach ($categories as $categoria): ?>
                        <option value="<?php echo e($categoria['id']); ?>" <?php echo (isset($subcategoria['id_categoria']) && $subcategoria['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
                            <?php echo e($categoria['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom de la Subcategoria</label>
                <input type="text" name="nom" id="nom" value="<?php echo e($subcategoria['nom']); ?>" required class="form-control">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?accio=llistar_tipologies" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-2"></i>Cancel·lar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Desar Canvis
                </button>
            </div>
        </form>
    </div>
</div>