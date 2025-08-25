<?php
// moduls/Backend/Organitzador/vistes/components/filtres_taula_esdeveniments.php
?>
<form id="filtres-esdeveniments-form" data-action="filtrar_esdeveniments_ajax">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="nom" class="form-label">Nom de l'esdeveniment</label>
            <input type="text" class="form-control" name="nom" id="nom" placeholder="Buscar per nom...">
        </div>
        <div class="col-md-3">
            <label for="data_inici" class="form-label">A partir de la data</label>
            <input type="date" class="form-control" name="data_inici" id="data_inici">
        </div>
        <div class="col-md-3">
            <label for="id_municipi" class="form-label">Municipi</label>
            <select name="id_municipi" id="id_municipi" class="form-select">
                <option value="">Tots els municipis</option>
                <?php foreach ($municipis as $municipi): ?>
                    <option value="<?php echo e($municipi['id']); ?>"><?php echo e($municipi['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="id_tipologia" class="form-label">Tipologia</label>
            <select name="id_tipologia" id="id_tipologia" class="form-select">
                <option value="">Totes</option>
                <?php foreach ($tipologies as $tipologia): ?>
                    <option value="<?php echo e($tipologia['id']); ?>"><?php echo e($tipologia['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</form>