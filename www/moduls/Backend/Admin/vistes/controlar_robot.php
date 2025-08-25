<div class="card shadow-sm">
    <div class="card-header"><h4 class="mb-0"><?php echo e($titol); ?></h4></div>
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h5 class="alert-heading"><i class="fa-solid fa-circle-info me-2"></i>Com funciona?</h5>
            <p>Selecciona un municipi de la llista per executar el seu procés d'extracció automatitzat. El robot visitarà la web associada a aquest municipi per buscar nous esdeveniments.</p>
        </div>
        
        <div class="row align-items-end g-3 my-3">
            <div class="col-md-8">
                <label for="selector-scraper" class="form-label fw-bold">Selecciona el Scraper a Executar:</label>
                <select id="selector-scraper" class="form-select form-select-lg">
                    <option value="">-- Tria un municipi --</option>
                    <?php foreach ($municipis_amb_scraper as $municipi): ?>
                        <option value="<?php echo e($municipi['nom_fitxer']); ?>"><?php echo e($municipi['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="button" class="btn btn-primary btn-lg" id="iniciar-robot-btn" disabled>
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="fa-solid fa-play me-2"></i>
                    <span class="btn-text">Iniciar el Robot</span>
                </button>
            </div>
        </div>

        <hr>
        <div>
            <label for="resultats-robot" class="form-label fw-bold">Resultats de l'execució:</label>
            <textarea id="resultats-robot" class="form-control bg-dark text-white" rows="10" readonly>Selecciona un scraper per començar...</textarea>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Esdeveniments Pendents de Validació</h5></div>
    <div class="card-body">
        <?php if (!empty($esdeveniments_pendents)): ?>
            <div class="list-group">
                <?php foreach ($esdeveniments_pendents as $event): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo e($event['nom']); ?></h5>
                            <small>Recollit el: <?php echo date('d/m/Y', strtotime($event['data_recollida'])); ?></small>
                        </div>
                        <p class="mb-1"><strong>Data:</strong> <?php echo e($event['data_inici'] ? date('d/m/Y', strtotime($event['data_inici'])) : 'N/D'); ?> | <strong>Lloc:</strong> <?php echo e($event['lloc']); ?></p>
                        <p class="mb-1"><strong>Organitzador(s) proposat(s):</strong> <?php echo e($event['organitzadors']); ?></p>
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php?accio=aprovar_esdeveniment&id_temporal=<?php echo $event['id']; ?>" class="btn btn-success"><i class="fa-solid fa-check me-2"></i> Validar i Editar</a>
                            <form action="index.php?accio=controlar_robot" method="POST" onsubmit="return confirm('Segur?');">
                                <input type="hidden" name="accio_robot" value="descartar">
                                <input type="hidden" name="id_temporal" value="<?php echo $event['id']; ?>">
                                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-times me-2"></i> Descartar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">No hi ha esdeveniments pendents de validació.</p>
        <?php endif; ?>
    </div>
</div>