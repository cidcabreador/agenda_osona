<?php
// moduls/Portal/controladors/veure_esdeveniment.php (Versió Corregida Final amb Pivotes)

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    afegirToast("No s'ha especificat cap esdeveniment.", "error");
    redireccionar('index.php');
}

try {
    // 1. Consulta principal que recull tota la informació amb JOINs i GROUP_CONCAT
    $sql = "
        SELECT 
            e.*, 
            GROUP_CONCAT(DISTINCT m.nom ORDER BY m.nom SEPARATOR ', ') AS municipis,
            GROUP_CONCAT(DISTINCT sc.nom ORDER BY sc.nom SEPARATOR ', ') AS subcategories,
            GROUP_CONCAT(DISTINCT pe.nom ORDER BY pe.id SEPARATOR ', ') AS perfils_edat,
            GROUP_CONCAT(DISTINCT o.nom ORDER BY o.nom SEPARATOR ', ') AS organitzadors
        FROM 
            esdeveniments e
        LEFT JOIN 
            esdeveniment_municipis em ON e.id = em.id_esdeveniment
        LEFT JOIN 
            municipis m ON em.id_municipi = m.id
        LEFT JOIN 
            esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
        LEFT JOIN 
            subcategories sc ON esc.id_subcategoria = sc.id
        LEFT JOIN
            esdeveniment_perfils_edat epe ON e.id = epe.id_esdeveniment
        LEFT JOIN
            perfils_edat pe ON epe.id_perfil_edat = pe.id
        LEFT JOIN 
            esdeveniment_organitzadors eo ON e.id = eo.id_esdeveniment
        LEFT JOIN 
            organitzadors o ON eo.id_organitzador = o.id
        WHERE
            e.id = :id
        GROUP BY 
            e.id
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $esdeveniment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$esdeveniment) {
        afegirToast("L'esdeveniment sol·licitat no existeix.", "error");
        redireccionar('index.php');
    }

    $titol = $esdeveniment['nom'];

} catch (PDOException $e) {
    error_log("Error en veure esdeveniment: " . $e->getMessage());
    afegirToast("S'ha produït un error en carregar l'esdeveniment.", "error");
    redireccionar('index.php');
}

// Ara la variable $esdeveniment conté tota la informació necessària per a la vista.
include __DIR__ . '/../vistes/veure_esdeveniment.php';