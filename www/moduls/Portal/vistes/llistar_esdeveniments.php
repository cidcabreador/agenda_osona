<?php
// moduls/Portal/vistes/llistar_esdeveniments.php (Versió Final i Correctament Estructurada)
?>
<div class="row">
    <aside class="col-lg-3">
        <div class="sidebar-filters p-3 rounded">
            <h4 class="mb-3"><i class="fa-solid fa-filter me-2"></i>Filtra els Esdeveniments</h4>
            <form id="public-filters-form" action="index.php" method="GET">
                <input type="hidden" name="accio" value="llistar_esdeveniments">
                <div class="mb-3">
                    <label for="nom" class="form-label fw-bold">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Festa Major" value="<?php echo e($cerca_nom ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="id_municipi" class="form-label fw-bold">Municipi</label>
                    <select id="id_municipi" name="id_municipi" class="form-select">
                        <option value="">Tots</option>
                        <?php foreach ($municipis as $municipi): ?>
                            <option value="<?php echo e($municipi['id']); ?>" <?php echo (($cerca_municipi ?? '') == $municipi['id']) ? 'selected' : ''; ?>><?php echo e($municipi['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_categoria" class="form-label fw-bold">Categoria</label>
                    <select id="id_categoria" name="id_categoria" class="form-select">
                        <option value="">Totes</option>
                        <?php foreach ($categories as $categoria): ?>
                            <option value="<?php echo e($categoria['id']); ?>" <?php echo (($cerca_categoria ?? '') == $categoria['id']) ? 'selected' : ''; ?>><?php echo e($categoria['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_subcategoria" class="form-label fw-bold">Subcategoria</label>
                    <select id="id_subcategoria" name="id_subcategoria" class="form-select">
                        <option value="">Totes</option>
                        <?php foreach ($subcategories as $subcategoria): ?>
                            <option value="<?php echo e($subcategoria['id']); ?>" data-categoria-id="<?php echo e($subcategoria['id_categoria']); ?>" <?php echo (($cerca_subcategoria ?? '') == $subcategoria['id']) ? 'selected' : ''; ?>>
                                <?php echo e($subcategoria['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search me-2"></i>Cerca</button>
                </div>
            </form>
             
            <div class="sidebar-controls">
                <label class="control-label">Canviar de vista</label>
                <div class="btn-group" role="group" aria-label="Canvi de vista">
                    <button type="button" class="btn btn-outline-primary active" id="timeline-view-btn" title="Vista de llista">
                        <i class="fa-solid fa-bars-staggered me-1"></i> Llista
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="calendar-view-btn" title="Vista de calendari">
                        <i class="fa-solid fa-calendar-days me-1"></i> Calendari
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="map-view-btn" title="Vista de mapa">
                        <i class="fa-solid fa-map-location-dot me-1"></i> Mapa
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <div class="col-lg-9">
        
        <div class="stats-panel">
            <div class="stat-item">
                <div class="stat-value"><?php echo e($estadistiques['total_esdeveniments']); ?></div>
                <div class="stat-label">Esdeveniments Trobats</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo e($estadistiques['total_municipis']); ?></div>
                <div class="stat-label">Municipis Actius</div>
            </div>
            <div id="find-me-btn" class="stat-item action-item">
                <div class="stat-value-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
                <div class="stat-label">Busca a prop teu</div>
            </div>
            <div class="stat-item">
                <div class="stat-value small"><?php echo e($estadistiques['proxim_esdeveniment_data']); ?></div>
                <div class="stat-label">Proper Esdeveniment</div>
            </div>
        </div>

        <div id="views-container">
            <div id="map-view" class="d-none">
                <div id="map" style="height: 600px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></div>
            </div>

            <div id="calendar-view" class="d-none">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>

            <div id="timeline-view">
                <div class="timeline-container">
                    <?php if (!empty($esdeveniments_per_setmana)): ?>
                        <?php $is_first_week = true; ?>
                        <?php foreach ($esdeveniments_per_setmana as $setmana): ?>
                            <div class="timeline-item timeline-week-header">
                                <div class="timeline-icon <?php if ($is_first_week) echo 'timeline-icon-start'; ?>">
                                    <?php if ($is_first_week): ?><i class="fa-solid fa-calendar-day special-start-icon"></i><?php else: ?><i class="fa-solid fa-calendar-days red-week-icon"></i><?php endif; ?>
                                </div>
                                <h2><?php echo e($setmana['titol']); ?></h2>
                            </div>
                            <div class="timeline-item">
                                <div class="weekly-events-list">
                                    <?php $formatter_dia = new IntlDateFormatter('ca_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'E'); ?>
                                    <?php foreach ($setmana['esdeveniments'] as $esdeveniment): ?>
                                        <?php
                                            $ruta_imatge = $esdeveniment['es_imatge_de_recurs']
                                                ? 'img/' . e($esdeveniment['imatge'])
                                                : 'uploads/' . e($esdeveniment['imatge']);
                                        ?>
                                        <a href="index.php?accio=veure_esdeveniment&id=<?php echo e($esdeveniment['id']); ?>" class="weekly-event-item-link">
                                            <div class="weekly-event-item">
                                                <div class="event-image"><img src="<?php echo $ruta_imatge; ?>" alt="Imatge de <?php echo e($esdeveniment['nom']); ?>"></div>
                                                <div class="event-date">
                                                    <span class="day-number"><?php echo e(date('d', strtotime($esdeveniment['data_inici']))); ?></span>
                                                    <span class="day-name"><?php echo e(ucfirst($formatter_dia->format(new DateTime($esdeveniment['data_inici'])))); ?></span>
                                                </div>
                                                <div class="event-details">
                                                    <span class="event-title"><?php echo e($esdeveniment['nom']); ?></span>
                                                    <span class="event-location"><i class="fa-solid fa-location-dot"></i> <?php echo e($esdeveniment['municipi']); ?></span>
                                                </div>
                                                <div class="event-arrow"><i class="fa-solid fa-chevron-right"></i></div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php $is_first_week = false; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card shadow-sm"><div class="card-body text-center p-5"><h4>No s'han trobat pròxims esdeveniments</h4><p class="text-muted">Prova a canviar els filtres de cerca o a netejar-los.</p></div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>