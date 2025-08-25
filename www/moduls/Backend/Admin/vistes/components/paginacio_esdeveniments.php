<?php
// moduls/Backend/Admin/vistes/components/paginacio_esdeveniments.php (Paginació Intel·ligent)

// Funció d'ajuda per generar un enllaç de pàgina
function render_page_link($pagina, $text = null, $disabled = false, $active = false) {
    $text = $text ?? $pagina;
    $class_li = 'page-item' . ($disabled ? ' disabled' : '') . ($active ? ' active' : '');
    $class_a = 'page-link';
    echo "<li class=\"$class_li\"><a class=\"$class_a\" href=\"#\" data-pagina=\"$pagina\">$text</a></li>";
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <span class="text-muted">
        Mostrant <?php echo count($esdeveniments); ?> de <?php echo $total_esdeveniments; ?> resultats (Pàgina <?php echo $pagina_actual; ?> de <?php echo $total_pagines; ?>)
    </span>

    <?php if ($total_pagines > 1): ?>
        <nav aria-label="Paginació d'esdeveniments">
            <ul class="pagination justify-content-center mb-0">
                <?php
                // Botó 'Anterior'
                render_page_link($pagina_actual - 1, 'Anterior', $pagina_actual <= 1);

                // Lògica per a la paginació intel·ligent
                $rang = 2; // Quants enllaços mostrar al voltant de la pàgina actual
                for ($i = 1; $i <= $total_pagines; $i++) {
                    // Mostrem sempre la primera, l'última i les pàgines dins del rang
                    if ($i == 1 || $i == $total_pagines || ($i >= $pagina_actual - $rang && $i <= $pagina_actual + $rang)) {
                        render_page_link($i, null, false, $i == $pagina_actual);
                    } 
                    // Afegim punts suspensius si hi ha un salt
                    elseif ($i == $pagina_actual - $rang - 1 || $i == $pagina_actual + $rang + 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                // Botó 'Següent'
                render_page_link($pagina_actual + 1, 'Següent', $pagina_actual >= $total_pagines);
                ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>