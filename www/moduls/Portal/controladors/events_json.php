<?php
// moduls/Portal/controladors/events_json.php (Versió Final amb Coordenades del Municipi)

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Consulta SQL definitiva: Agafa les coordenades del municipi, no de l'esdeveniment.
    $sql = "
        SELECT DISTINCT
            e.id, 
            e.nom, 
            e.data_inici, 
            e.imatge, 
            m.latitud,   -- Canvi clau aquí
            m.longitud,  -- Canvi clau aquí
            m.nom AS municipi,
            cat.color AS tipologia_color,
            cat.icona_fa AS tipologia_icona
        FROM 
            esdeveniments e
        -- Fem INNER JOIN perquè només ens interessen esdeveniments amb municipi
        INNER JOIN 
            esdeveniment_municipis em ON e.id = em.id_esdeveniment
        INNER JOIN 
            municipis m ON em.id_municipi = m.id
        LEFT JOIN 
            esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
        LEFT JOIN 
            subcategories sc ON esc.id_subcategoria = sc.id
        LEFT JOIN
            categories cat ON sc.id_categoria = cat.id
        WHERE 
            e.data_inici >= CURDATE()
            -- La condició ara és que el municipi tingui coordenades
            AND m.latitud IS NOT NULL
            AND m.longitud IS NOT NULL
    ";

    $stmt = $pdo->query($sql);
    $esdeveniments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($esdeveniments);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error a events_json.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error en obtenir les dades dels esdeveniments.']);
}

exit();