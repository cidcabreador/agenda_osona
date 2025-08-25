<div class="card shadow-sm">
    <div class="card-header"><h4 class="mb-0"><?php echo e($titol); ?></h4></div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert"><h5 class="alert-heading">Errors!</h5><hr>
                <ul class="mb-0 list-unstyled">
                    <?php foreach ($errors as $error): ?><li><i class="fa-solid fa-exclamation-triangle me-2"></i><?php echo $error; ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($esdeveniment_temporal): ?>
        <form action="index.php?accio=aprovar_esdeveniment" method="POST">
            <input type="hidden" name="id_temporal" value="<?php echo e($esdeveniment_temporal['id']); ?>">
            
            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 h6">Dades de l'Esdeveniment</legend>
                <div class="mb-3"><label for="nom" class="form-label fw-bold">Nom</label><input type="text" name="nom" id="nom" value="<?php echo e($esdeveniment_temporal['nom'] ?? ''); ?>" required class="form-control"></div>
                
                <?php if (!empty($esdeveniment_temporal['imatge'])): ?>
                    <?php
                        $rutaImatge = $esdeveniment_temporal['imatge'];
                        if (!filter_var($rutaImatge, FILTER_VALIDATE_URL)) {
                            $rutaImatge = 'uploads/' . $rutaImatge;
                        }
                    ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imatge Extreta</label>
                        <div>
                            <img src="<?php echo e($rutaImatge); ?>" class="img-thumbnail" style="max-height: 200px;" alt="Imatge de l'esdeveniment extret">
                            <input type="hidden" name="imatge_original_url" value="<?php echo e($esdeveniment_temporal['imatge']); ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-3"><label for="descripcio" class="form-label fw-bold">Descripció</label><textarea name="descripcio" id="descripcio" rows="5" class="form-control"><?php echo e($esdeveniment_temporal['descripcio'] ?? ''); ?></textarea></div>
                <div class="row g-3">
                    <div class="col-md-6"><label for="data_inici" class="form-label fw-bold">Data Inici</label><input type="date" name="data_inici" id="data_inici" value="<?php echo e($esdeveniment_temporal['data_inici'] ?? ''); ?>" required class="form-control"></div>
                    <div class="col-md-6"><label for="data_fi" class="form-label fw-bold">Data Fi</label><input type="date" name="data_fi" id="data_fi" value="<?php echo e($esdeveniment_temporal['data_fi'] ?? ''); ?>" class="form-control"></div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-4"><label for="hora" class="form-label fw-bold">Hora</label><input type="text" name="hora" id="hora" value="<?php echo e($esdeveniment_temporal['hora'] ?? ''); ?>" class="form-control"></div>
                    <div class="col-md-4"><label for="adreca" class="form-label fw-bold">Adreça</label><input type="text" name="adreca" id="adreca" value="<?php echo e($esdeveniment_temporal['lloc'] ?? ''); ?>" class="form-control"></div>
                    <div class="col-md-4"><label for="preu" class="form-label fw-bold">Preu</label><input type="text" name="preu" id="preu" value="<?php echo e($esdeveniment_temporal['preu'] ?? ''); ?>" class="form-control"></div>
                </div>
                <div class="row g-3 mt-1">
                     <div class="col-md-6"><label for="municipis" class="form-label fw-bold">Municipi(s)</label><select name="municipis[]" id="municipis" required class="form-select" multiple size="8"><?php foreach ($municipis as $m): ?><option value="<?php echo e($m['id']); ?>" <?php echo (($esdeveniment_temporal['municipi'] ?? '') == $m['id']) ? 'selected' : ''; ?>><?php echo e($m['nom']); ?></option><?php endforeach; ?></select></div>
                     <div class="col-md-6"><label for="subcategories" class="form-label fw-bold">Subcategoria(es)</label><select name="subcategories[]" id="subcategories" required class="form-select" multiple size="8"><?php foreach ($categories as $c): ?><optgroup label="<?php echo e($c['nom']); ?>"><?php foreach ($subcategories as $sc): if ($sc['id_categoria'] == $c['id']): ?><option value="<?php echo e($sc['id']); ?>" <?php echo in_array($sc['id'], $subcategories_seleccionades) ? 'selected' : ''; ?>><?php echo e($sc['nom']); ?></option><?php endif; endforeach; ?></optgroup><?php endforeach; ?></select></div>
                </div>
            </fieldset>

            <fieldset class="border p-3 rounded mb-4">
                <legend class="float-none w-auto px-2 h6">Assistent d'Organitzadors</legend>
                <div class="alert alert-secondary small"><i class="fas fa-magic-wand-sparkles me-2"></i>Revisa les targetes. El sistema ha netejat el text i busca coincidències per paraules clau.</div>

                <?php foreach ($suggeriments_organitzadors as $index => $sug): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">Organitzador extret: <strong><?php echo e($sug['nom_original']); ?></strong></div>
                        <div class="card-body">
                            <input type="hidden" name="org_name[<?php echo $index; ?>]" value="<?php echo e($sug['nom_original']); ?>">
                            
                            <?php if (!empty($sug['suggeriments'])): ?>
                                <p class="small text-muted">Hem trobat aquests possibles organitzadors. Tria'n un:</p>
                                
                                <?php $loop_iterator = 0; ?>
                                <?php foreach ($sug['suggeriments'] as $s): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="org_decision[<?php echo $index; ?>]" id="link-<?php echo $index; ?>-<?php echo $s['id']; ?>" value="link" <?php if($loop_iterator === 0) echo 'checked'; ?>>
                                    <label class="form-check-label" for="link-<?php echo $index; ?>-<?php echo $s['id']; ?>">
                                        Associar a: <strong><?php echo e($s['nom']); ?></strong>
                                        
                                        <?php
                                            $percentatge = $s['percentatge'];
                                            $classe_badge = 'bg-warning text-dark'; // Per defecte (groc)
                                            if ($percentatge >= 95) {
                                                $classe_badge = 'bg-success'; // Molt alta (verd)
                                            } elseif ($percentatge >= 85) {
                                                $classe_badge = 'bg-primary'; // Alta (blau)
                                            }
                                        ?>
                                        <span class="badge <?php echo $classe_badge; ?>">Semblança: <?php echo e($percentatge); ?>%</span>
                                        </label>
                                    <input type="hidden" name="org_link_id[<?php echo $index; ?>]" value="<?php echo e($s['id']); ?>">
                                </div>
                                <?php $loop_iterator++; ?>
                                <?php endforeach; ?>

                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="org_decision[<?php echo $index; ?>]" id="create-<?php echo $index; ?>" value="create">
                                    <label class="form-check-label" for="create-<?php echo $index; ?>">
                                        No, cap d'aquests. Crear un nou organitzador anomenat "<?php echo e($sug['nom_original']); ?>"
                                    </label>
                                </div>
                            <?php else: ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="org_decision[<?php echo $index; ?>]" id="create-<?php echo $index; ?>" value="create" checked>
                                    <label class="form-check-label" for="create-<?php echo $index; ?>">
                                        Crear nou organitzador: "<?php echo e($sug['nom_original']); ?>"
                                        <span class="badge bg-secondary">No s'han trobat suggeriments clars</span>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                <div class="mb-3">
                    <label for="organitzadors_addicionals" class="form-label fw-bold">Afegir Manualment Altres Organitzadors Existents</label>
                    <input type="text" id="filtre-organitzadors-manual" class="form-control form-control-sm mb-2" placeholder="Escriu aquí per filtrar la llista...">
                    <select name="organitzadors_addicionals[]" id="organitzadors_addicionals" class="form-select" multiple size="5">
                        <?php foreach ($tots_organitzadors as $org): ?>
                            <option value="<?php echo e($org['id']); ?>"><?php echo e($org['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?accio=controlar_robot" class="btn btn-secondary"><i class="fa-solid fa-xmark me-2"></i>Cancel·lar</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Aprovar i Publicar Esdeveniment</button>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">No s'ha trobat l'esdeveniment temporal per validar. Torna al panell del robot.</div>
        <?php endif; ?>
    </div>
</div>