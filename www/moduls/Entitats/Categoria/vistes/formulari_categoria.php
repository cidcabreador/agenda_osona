<div class="card shadow-sm">
    <div class="card-header"><h4 class="mb-0"><?php echo e($titol); ?></h4></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0">
                <?php foreach ($errors as $error): echo '<li>' . e($error) . '</li>'; endforeach; ?>
            </ul></div>
        <?php endif; ?>
        <form action="index.php?accio=desar_categoria" method="POST">
            <?php if ($categoria['id']): ?><input type="hidden" name="id" value="<?php echo e($categoria['id']); ?>"><?php endif; ?>
            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom de la Categoria</label>
                <input type="text" name="nom" id="nom" value="<?php echo e($categoria['nom']); ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="color" class="form-label fw-bold">Color</label>
                <input type="color" name="color" id="color" value="<?php echo e($categoria['color']); ?>" required class="form-control form-control-color">
            </div>
            <div class="mb-3">
                <label for="icona_fa" class="form-label fw-bold">Icona (Font Awesome)</label>
                <input type="text" name="icona_fa" id="icona_fa" value="<?php echo e($categoria['icona_fa']); ?>" required class="form-control">
                <div class="form-text">Exemple: <code>fa-music</code>, <code>fa-utensils</code>, <code>fa-tree</code>. Pots trobar més icones a <a href="https://fontawesome.com/search?m=free&s=solid" target="_blank">Font Awesome</a>.</div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?accio=llistar_categories" class="btn btn-secondary">Cancel·lar</a>
                <button type="submit" class="btn btn-primary">Desar Canvis</button>
            </div>
        </form>
    </div>
</div>