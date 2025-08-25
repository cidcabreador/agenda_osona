<?php
// moduls/Backend/Organitzador/controladors/panell_organitzadors.php (Versió amb Pivotes)

if (!estaAutenticat()) {
    redireccionar('index.php?accio=login');
}

$titol = 'Els Meus Esdeveniments';
$id_organitzador_actual = $_SESSION['organitzador_id'];

try {
    $sql = "
        SELECT 
            e.id, 
            e.nom, 
            e.data_inici, 
            GROUP_CONCAT(DISTINCT m.nom ORDER BY m.nom SEPARATOR ', ') AS municipis,
            GROUP_CONCAT(DISTINCT sc.nom ORDER BY sc.nom SEPARATOR ', ') AS subcategories
        FROM 
            esdeveniments e
        INNER JOIN
            esdeveniment_organitzadors eo ON e.id = eo.id_esdeveniment
        LEFT JOIN 
            esdeveniment_municipis em ON e.id = em.id_esdeveniment
        LEFT JOIN 
            municipis m ON em.id_municipi = m.id
        LEFT JOIN 
            esdeveniment_subcategories esc ON e.id = esc.id_esdeveniment
        LEFT JOIN 
            subcategories sc ON esc.id_subcategoria = sc.id
        WHERE
            eo.id_organitzador = :id_organitzador
        GROUP BY 
            e.id
        ORDER BY 
            e.data_inici DESC
    ";

    $stmt_esdeveniments = $pdo->prepare($sql);
    $stmt_esdeveniments->execute(['id_organitzador' => $id_organitzador_actual]);
    $esdeveniments = $stmt_esdeveniments->fetchAll();

    // Per als filtres
    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom FROM subcategories ORDER BY nom ASC")->fetchAll();

} catch (PDOException $e) {
    error_log("Error al carregar el panell d'organitzador: " . $e->getMessage());
    afegirToast("S'ha produït un error en carregar els teus esdeveniments.", "error");
    $esdeveniments = [];
    $municipis = [];
    $subcategories = [];
}

include __DIR__ . '/../vistes/panell_organitzadors.php';