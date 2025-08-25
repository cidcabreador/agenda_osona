<?php
// moduls/Portal/vistes/formulari_subscripcio.php (Amplada Màxima Definitiva)
?>
<div class="row">
    <div class="col-12"> 
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light p-3 d-flex justify-content-between align-items-center">
                 <a href="javascript:history.back()" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Tornar</a>
                <h1 class="h3 fw-bold text-center mb-0 mx-auto">Butlletí Setmanal</h1>
                 <div style="width: 100px;"></div>
            </div>
            <div class="card-body p-4 p-md-5">
                <p class="text-center text-muted mt-n3 mb-4">Vols rebre un email amb els actes que es fan a la comarca? Apunta't!</p>

                <?php if ($success): ?>
                    <div class="alert alert-success text-center" role="alert">
                        <h4 class="alert-heading">Gràcies per subscriure't!</h4>
                        <p>Has estat afegit correctament a la nostra llista de distribució.</p>
                        <hr>
                        <a href="index.php" class="btn btn-success">Tornar a l'inici</a>
                    </div>
                <?php else: ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <h5 class="alert-heading">Hi ha hagut errors:</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?accio=subscriure" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">El teu correu electrònic:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo e($email_enviat); ?>" required>
                        </div>

                        <h5 class="mt-4 mb-3 fw-bold">Tria les teves preferències...</h5>
                        
                        <div class="form-check form-switch form-check-reverse text-end mb-3">
                            <input class="form-check-input select-all" type="checkbox" id="selectAllMaster" data-target="all-preferences">
                            <label class="form-check-label fw-bold text-primary" for="selectAllMaster">Apuntar-se a TOT!</label>
                        </div>
                        
                        <div class="all-preferences">
                            <div class="accordion" id="accordionSubscripcio">
                                <div class="accordion-item">
                                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories"><i class="fa-solid fa-palette me-2"></i> ... per categories d'interès</button></h2>
                                    <div id="collapseCategories" class="accordion-collapse collapse" data-bs-parent="#accordionSubscripcio">
                                        <div class="accordion-body">
                                            <?php foreach($categories as $categoria): ?>
                                                <div class="mb-3">
                                                    <div class="form-check bg-light p-2 rounded-top border-bottom">
                                                        <input class="form-check-input select-all" type="checkbox" data-target="categoria-<?php echo e($categoria['id']); ?>-checks">
                                                        <label class="form-check-label fw-bold"><?php echo e($categoria['nom']); ?></label>
                                                    </div>
                                                    <div class="categoria-<?php echo e($categoria['id']); ?>-checks row p-2">
                                                        <?php foreach($subcategories as $subcategoria): ?>
                                                            <?php if($subcategoria['id_categoria'] == $categoria['id']): ?>
                                                                <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="subcategories[]" value="<?php echo e($subcategoria['id']); ?>"> <label class="form-check-label"><?php echo e($subcategoria['nom']); ?></label></div></div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="accordion-item">
                                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrganitzadors"><i class="fa-solid fa-user-pen me-2"></i> ... per organitzadors</button></h2>
                                    <div id="collapseOrganitzadors" class="accordion-collapse collapse" data-bs-parent="#accordionSubscripcio">
                                        <div class="accordion-body">
                                            <div class="form-check"><input class="form-check-input select-all" type="checkbox" data-target="organitzadors-checks"><label class="form-check-label fw-bold">Seleccionar-los tots</label></div><hr>
                                            <div class="organitzadors-checks row">
                                                <?php foreach ($organitzadors as $organitzador): ?>
                                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="organitzadors[]" value="<?php echo e($organitzador['id']); ?>"> <label class="form-check-label"><?php echo e($organitzador['nom']); ?></label></div></div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMunicipis"><i class="fa-solid fa-map-location-dot me-2"></i> ... per municipis</button></h2>
                                    <div id="collapseMunicipis" class="accordion-collapse collapse" data-bs-parent="#accordionSubscripcio">
                                        <div class="accordion-body">
                                            <div class="form-check"><input class="form-check-input select-all" type="checkbox" data-target="municipis-checks"><label class="form-check-label fw-bold">Seleccionar-los tots</label></div><hr>
                                            <div class="municipis-checks row">
                                                <?php foreach ($municipis as $municipi): ?>
                                                <div class="col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="municipis[]" value="<?php echo e($municipi['id']); ?>"> <label class="form-check-label"><?php echo e($municipi['nom']); ?></label></div></div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 mt-4 p-3 bg-light rounded">
                            <label for="captcha" class="form-label fw-bold">Verificació (per evitar robots):</label>
                            <div class="d-flex align-items-center">
                                <span class="me-2"><?php echo e($pregunta_captcha); ?></span>
                                <input type="text" class="form-control" id="captcha" name="captcha" style="width: 120px;" required autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="politica" name="politica" required>
                            <label class="form-check-label" for="politica">Accepto la política de privacitat</label>
                        </div>
                        <small class="form-text text-muted">
                            De conformitat amb el Reglament General de Protecció de Dades de Caràcter Personal, l'informem que les seves dades personals seran incorporades en un fitxer automatitzat de titularitat de Consell Comarcal d'Osona (NIF: CIF P5800015-I), amb la finalitat de poder respondre a les seves consultes i poder oferir-li els serveis que ens sol·liciti. L'informem que les seves dades no seran objecte de cessió a cap altra entitat i que es conservaran mentre es mantinguin les converses entre les parts o durant els anys necessàries per complir amb les obligacions legals i tributaries vigents en cada moment o fins que vostè exerceixi algun dels següents drets: Accés, rectificació o supressió, cancel·lació, limitació i oposició o portabilitat de les dades enviant un email a: info@agendaosona.cat
                        </small>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Subscriu-m'hi</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>