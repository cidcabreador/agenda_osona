<?php
// moduls/Entitats/Esdeveniment/vistes/crear_esdeveniment.php (Versió Final)
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
                        <li><i class="fa-solid fa-exclamation-triangle me-2"></i><?php echo $error; // Permetem HTML per a l'error de depuració ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php?accio=guardar_esdeveniment" method="POST" enctype="multipart/form-data">
            <?php if (isset($esdeveniment['id']) && $esdeveniment['id']): ?>
                <input type="hidden" name="id" value="<?php echo e($esdeveniment['id']); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nom" class="form-label fw-bold">Nom de l'Esdeveniment</label>
                <input type="text" name="nom" id="nom" value="<?php echo e($esdeveniment['nom'] ?? ''); ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="descripcio" class="form-label fw-bold">Descripció</label>
                <textarea name="descripcio" id="descripcio" rows="5" class="form-control"><?php echo e($esdeveniment['descripcio'] ?? ''); ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4 mb-3">
                    <label for="data_inici" class="form-label fw-bold">Data d'Inici</label>
                    <input type="date" name="data_inici" id="data_inici" value="<?php echo e($esdeveniment['data_inici'] ?? ''); ?>" required class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="data_fi" class="form-label fw-bold">Data de Fi (opcional)</label>
                    <input type="date" name="data_fi" id="data_fi" value="<?php echo e($esdeveniment['data_fi'] ?? ''); ?>" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="hora" class="form-label fw-bold">Hora (opcional)</label>
                    <input type="text" name="hora" id="hora" value="<?php echo e($esdeveniment['hora'] ?? ''); ?>" class="form-control" placeholder="Ex: 19:00h, Tot el dia...">
                </div>
            </div>
            <div class="mb-3">
                <label for="adreca" class="form-label fw-bold">Adreça o Lloc Específic</label>
                <input type="text" name="adreca" id="adreca" value="<?php echo e($esdeveniment['adreca'] ?? ''); ?>" class="form-control" placeholder="Ex: Pavelló Municipal...">
            </div>
             <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="latitud" class="form-label fw-bold">Latitud (opcional)</label>
                    <input type="text" name="latitud" id="latitud" value="<?php echo e($esdeveniment['latitud'] ?? ''); ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="longitud" class="form-label fw-bold">Longitud (opcional)</label>
                    <input type="text" name="longitud" id="longitud" value="<?php echo e($esdeveniment['longitud'] ?? ''); ?>" class="form-control">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="municipis" class="form-label fw-bold">Municipi(s) on es fa</label>
                    <select name="municipis[]" id="municipis" required class="form-select" multiple size="8">
                        <?php foreach ($municipis as $municipi): ?>
                            <option value="<?php echo e($municipi['id']); ?>" <?php echo in_array($municipi['id'], $municipis_seleccionats) ? 'selected' : ''; ?>>
                                <?php echo e($municipi['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="subcategories" class="form-label fw-bold">Subcategoria(es)</label>
                    <select name="subcategories[]" id="subcategories" required class="form-select" multiple size="8">
                         <?php foreach ($categories as $categoria): ?>
                            <optgroup label="<?php echo e($categoria['nom']); ?>">
                                <?php foreach ($subcategories as $subcategoria): ?>
                                    <?php if ($subcategoria['id_categoria'] == $categoria['id']): ?>
                                        <option value="<?php echo e($subcategoria['id']); ?>" <?php echo in_array($subcategoria['id'], $subcategories_seleccionades) ? 'selected' : ''; ?>>
                                            <?php echo e($subcategoria['nom']); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
             <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="perfils_edat" class="form-label fw-bold">Perfil(s) d'Edat</label>
                    <select name="perfils_edat[]" id="perfils_edat" class="form-select" multiple size="4">
                        <?php foreach ($perfils_edat as $perfil): ?>
                            <option value="<?php echo e($perfil['id']); ?>" <?php echo in_array($perfil['id'], $perfils_edat_seleccionats) ? 'selected' : ''; ?>>
                                <?php echo e($perfil['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="col-md-6 mb-3">
                    <label for="preu" class="form-label fw-bold">Preu (opcional)</label>
                    <input type="text" name="preu" id="preu" value="<?php echo e($esdeveniment['preu'] ?? ''); ?>" class="form-control" placeholder="Ex: Gratuït, 10€...">
                </div>
            </div>
            
            <?php if (esAdmin()): ?>
            <div class="mb-3">
                <label for="organitzadors" class="form-label fw-bold">Assignat a l'Organitzador(s)</label>
                <select name="organitzadors[]" id="organitzadors" required class="form-select" multiple>
                    <?php foreach ($organitzadors as $organitzador): ?>
                        <option value="<?php echo e($organitzador['id']); ?>" <?php echo in_array($organitzador['id'], $organitzadors_seleccionats) ? 'selected' : ''; ?>>
                            <?php echo e($organitzador['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="imatge" class="form-label fw-bold">Imatge de l'Esdeveniment</label>
                <input type="file" name="imatge" id="imatge" class="form-control">
                <?php if (!empty($esdeveniment['imatge'])): ?>
                    <div class="mt-3">
                        <p class="mb-1 text-muted">Imatge actual:</p>
                        <img src="uploads/<?php echo e($esdeveniment['imatge']); ?>" alt="Imatge de l'esdeveniment" class="img-thumbnail" style="max-height: 150px;">
                        <input type="hidden" name="imatge_actual" value="<?php echo e($esdeveniment['imatge']); ?>">
                    </div>
                <?php endif; ?>
            </div>
            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?accio=<?php echo esAdmin() ? 'panell_admin' : 'panell_organitzadors'; ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark me-2"></i>Cancel·lar
                </a>
                <button type="submit" name="accio_formulari" value="guardar" class="btn btn-primary">
                    <i class="fa-solid fa-save me-2"></i>Desar Canvis
                </button>
            </div>
        </form>
    </div>
</div>