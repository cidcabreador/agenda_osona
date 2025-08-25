<?php
// moduls/Entitats/Municipi/vistes/formulari_municipi.php (Amb lat/lon)
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

        <form action="index.php?accio=desar_municipis" method="POST">
            <?php if ($municipi['id']): ?>
                <input type="hidden" name="id" value="<?php echo e($municipi['id']); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom del Municipi</label>
                <input type="text" name="nom" id="nom" value="<?php echo e($municipi['nom']); ?>" required class="form-control">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="latitud" class="form-label fw-bold">Latitud</label>
                    <input type="text" name="latitud" id="latitud" value="<?php echo e($municipi['latitud']); ?>" class="form-control" placeholder="Ex: 41.9304">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="longitud" class="form-label fw-bold">Longitud</label>
                    <input type="text" name="longitud" id="longitud" value="<?php echo e($municipi['longitud']); ?>" class="form-control" placeholder="Ex: 2.2546">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?accio=llistar_municipis" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-2"></i>Cancel·lar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Desar Canvis
                </button>
            </div>
        </form>
    </div>
</div>