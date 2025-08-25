<?php
// moduls/Entitats/Organitzador/vistes/formulari_organitzador.php (Camp 'cognoms' eliminat)
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
        <form action="index.php?accio=desar_organitzadors" method="POST">
            <?php if (isset($organitzador['id']) && $organitzador['id']): ?>
                <input type="hidden" name="id" value="<?php echo e($organitzador['id']); ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom de l'Organitzador o Entitat</label>
                <input type="text" name="nom" id="nom" value="<?php echo e($organitzador['nom'] ?? ''); ?>" required class="form-control">
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Correu Electrònic</label>
                <input type="email" name="email" id="email" value="<?php echo e($organitzador['email'] ?? ''); ?>" required class="form-control">
            </div>
            
            <div class="mb-3">
                <label for="rol" class="form-label fw-bold">Rol</label>
                <select name="rol" id="rol" class="form-select">
                    <option value="organitzador" <?php echo (isset($organitzador['rol']) && $organitzador['rol'] === 'organitzador') ? 'selected' : ''; ?>>Organitzador</option>
                    <option value="admin" <?php echo (isset($organitzador['rol']) && $organitzador['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            
            <fieldset class="border p-3 rounded mb-3">
                <legend class="float-none w-auto px-2 h6">Contrasenya</legend>
                <p class="form-text mt-0 mb-3">Omple els camps de contrasenya només si vols establir-ne una de nova.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nova Contrasenya</label>
                        <input type="password" name="password" id="password" <?php echo (!isset($organitzador['id']) || !$organitzador['id']) ? 'required' : ''; ?> class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label">Confirmar Contrasenya</label>
                        <input type="password" name="password_confirm" id="password_confirm" <?php echo (!isset($organitzador['id']) || !$organitzador['id']) ? 'required' : ''; ?> class="form-control">
                    </div>
                </div>
            </fieldset>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?accio=llistar_organitzadors" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-2"></i>Cancel·lar
                </a>
                <button type="submit" name="accio_formulari" value="guardar" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Desar Canvis
                </button>
            </div>
        </form>
    </div>
</div>