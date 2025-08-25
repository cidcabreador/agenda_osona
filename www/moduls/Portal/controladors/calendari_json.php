<?php
// www/moduls/Portal/controladors/calendari_json.php

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Consulta optimitzada per a FullCalendar
    $sql = "
        SELECT 
            e.id,
            e.nom as title,
            e.data_inici as start,
            -- Si hi ha data de fi, l'afegim +1 dia perquè FullCalendar la mostri correctament
            CASE 
                WHEN e.data_fi IS NOT NULL AND e.data_fi > e.data_inici 
                THEN DATE_ADD(e.data_fi, INTERVAL 1 DAY) 
                ELSE NULL 
            END as end,
            -- Afegim el color de la categoria per als esdeveniments
            c.color as backgroundColor,
            -- Creem la URL per anar al detall de l'esdeveniment
            CONCAT('index.php?accio=veure_esdeveniment&id=', e.id) as url
        FROM 
            esdeveniments e
        LEFT JOIN 
            esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
        LEFT JOIN 
            subcategories sc ON esc.id_subcategoria = sc.id
        LEFT JOIN
            categories c ON sc.id_categoria = c.id
        -- Mostrem només esdeveniments futurs o que acaben avui o més tard
        WHERE
            IFNULL(e.data_fi, e.data_inici) >= CURDATE()
        GROUP BY
            e.id
    ";

    $stmt = $pdo->query($sql);
    $esdeveniments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($esdeveniments);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error a calendari_json.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error en obtenir les dades dels esdeveniments.']);
}

exit();