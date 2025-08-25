<?php
function render_page_link_org($pagina, $text = null, $disabled = false, $active = false) {
    $text = $text ?? $pagina;
    $class_li = 'page-item' . ($disabled ? ' disabled' : '') . ($active ? ' active' : '');
    echo "<li class=\"$class_li\"><a class=\"page-link\" href=\"#\" data-pagina=\"$pagina\">$text</a></li>";
}
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <span class="text-muted">Mostrant <?php echo count($organitzadors); ?> de <?php echo $total_organitzadors; ?> resultats</span>
    <?php if ($total_pagines > 1): ?>
        <nav><ul class="pagination justify-content-center mb-0">
            <?php
            render_page_link_org($pagina_actual - 1, 'Anterior', $pagina_actual <= 1);
            $rang = 2;
            for ($i = 1; $i <= $total_pagines; $i++) {
                if ($i == 1 || $i == $total_pagines || ($i >= $pagina_actual - $rang && $i <= $pagina_actual + $rang)) {
                    render_page_link_org($i, null, false, $i == $pagina_actual);
                } elseif ($i == $pagina_actual - $rang - 1 || $i == $pagina_actual + $rang + 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            render_page_link_org($pagina_actual + 1, 'Següent', $pagina_actual >= $total_pagines);
            ?>
        </ul></nav>
    <?php endif; ?>
</div>