<?php
// moduls/Backend/Admin/vistes/components/filtres_taula_esdeveniments.php (Versió AJAX)
?>
<form id="filtres-esdeveniments-form-admin">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control form-control-sm" name="nom" id="nom" value="<?php echo e($cerca_nom); ?>">
        </div>
        <div class="col-md-2">
            <label for="data_des_de" class="form-label">Des de</label>
            <input type="date" class="form-control form-control-sm" name="data_des_de" id="data_des_de">
        </div>
        <div class="col-md-2">
            <label for="data_fins_a" class="form-label">Fins a</label>
            <input type="date" class="form-control form-control-sm" name="data_fins_a" id="data_fins_a">
        </div>
        <div class="col-md-4">
            <label for="id_municipi" class="form-label">Municipi</label>
            <select name="id_municipi" id="id_municipi" class="form-select form-select-sm">
                <option value="">Tots</option>
                <?php foreach ($municipis as $municipi): ?>
                    <option value="<?php echo e($municipi['id']); ?>"><?php echo e($municipi['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="id_categoria_admin" class="form-label">Categoria</label>
            <select name="id_categoria" id="id_categoria_admin" class="form-select form-select-sm">
                <option value="">Totes</option>
                <?php foreach ($categories as $categoria): ?>
                    <option value="<?php echo e($categoria['id']); ?>"><?php echo e($categoria['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="id_subcategoria_admin" class="form-label">Subcategoria</label>
            <select name="id_subcategoria" id="id_subcategoria_admin" class="form-select form-select-sm">
                <option value="">Totes</option>
                 <?php foreach ($subcategories as $subcategoria): ?>
                    <option value="<?php echo e($subcategoria['id']); ?>" data-categoria-id="<?php echo e($subcategoria['id_categoria']); ?>"><?php echo e($subcategoria['nom']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <button type="reset" id="reset-filters-btn" class="btn btn-sm btn-outline-secondary">Netejar Filtres</button>
        </div>
    </div>
</form>