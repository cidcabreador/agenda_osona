<?php
// moduls/Portal/controladors/llistar_esdeveniments.php (Versió amb consulta SQL corregida)

$titol = 'Propers Esdeveniments a Osona';
$imatges_de_recurs = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg'];

// Recollim paràmetres de cerca
$cerca_nom = $_GET['nom'] ?? '';
$cerca_municipi = filter_input(INPUT_GET, 'id_municipi', FILTER_VALIDATE_INT);
$cerca_categoria = filter_input(INPUT_GET, 'id_categoria', FILTER_VALIDATE_INT);
$cerca_subcategoria = filter_input(INPUT_GET, 'id_subcategoria', FILTER_VALIDATE_INT);

$estadistiques = [
    'total_esdeveniments' => 0,
    'total_municipis' => 0,
    'proxim_esdeveniment_data' => null
];

try {
    // Obtenim les llistes per als filtres
    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom, id_categoria FROM subcategories ORDER BY nom ASC")->fetchAll();

    // ===== CONSULTA SQL CORREGIDA I MILLORADA AMB LEFT JOIN =====
    $sql_base = "
        SELECT
            e.id, e.nom, e.data_inici, e.hora, e.imatge,
            GROUP_CONCAT(DISTINCT m.nom ORDER BY m.nom SEPARATOR ', ') as municipi
        FROM esdeveniments AS e
        LEFT JOIN esdeveniment_municipis AS em ON e.id = em.id_esdeveniment
        LEFT JOIN municipis AS m ON em.id_municipi = m.id
    ";
    
    $condicions = ["e.data_inici >= CURDATE()"];
    $parametres = [];
    $joins = ""; // Aquesta variable ara contindrà joins addicionals per als filtres

    if (!empty($cerca_nom)) {
        $condicions[] = "e.nom LIKE :nom";
        $parametres[':nom'] = '%' . $cerca_nom . '%';
    }
    if (!empty($cerca_municipi)) {
        // Com que ja tenim el JOIN a la consulta base, només cal afegir la condició
        $condicions[] = "m.id = :id_municipi";
        $parametres[':id_municipi'] = $cerca_municipi;
    }
    if (!empty($cerca_subcategoria)) {
        $joins .= " JOIN esdeveniment_subcategories esc_filter ON e.id = esc_filter.id_esdeveniment";
        $condicions[] = "esc_filter.id_subcategoria = :id_subcategoria";
        $parametres[':id_subcategoria'] = $cerca_subcategoria;
    } elseif (!empty($cerca_categoria)) {
        $joins .= " JOIN esdeveniment_subcategories esc_filter ON e.id = esc_filter.id_esdeveniment";
        $joins .= " JOIN subcategories sc_filter ON esc_filter.id_subcategoria = sc_filter.id";
        $condicions[] = "sc_filter.id_categoria = :id_categoria";
        $parametres[':id_categoria'] = $cerca_categoria;
    }


//     -- 1. Ordena per la data més propera a avui (la data d'inici per a esdeveniments futurs, o avui per als que ja estan en curs)
//     -- 2. Si hi ha un empat, prioritza els esdeveniments més curts
//     -- 3. Com a últim criteri de desempat, l'hora


$sql_final = $sql_base . $joins . " WHERE " . implode(' AND ', $condicions) . " GROUP BY e.id 
ORDER BY GREATEST(e.data_inici, CURDATE()) ASC, DATEDIFF(IFNULL(e.data_fi, e.data_inici), e.data_inici) ASC, e.hora ASC";


    $stmt = $pdo->prepare($sql_final);
    $stmt->execute($parametres);
    $tots_els_esdeveniments = $stmt->fetchAll();

    // La resta de la lògica d'estadístiques i agrupació es manté igual
    $estadistiques['total_esdeveniments'] = count($tots_els_esdeveniments);
    if (!empty($tots_els_esdeveniments)) {
        $ids_esdeveniments = array_column($tots_els_esdeveniments, 'id');
        if (count($ids_esdeveniments) > 0) {
            $placeholders = rtrim(str_repeat('?,', count($ids_esdeveniments)), ',');
            $stmt_mun_count = $pdo->prepare("SELECT COUNT(DISTINCT id_municipi) FROM esdeveniment_municipis WHERE id_esdeveniment IN ($placeholders)");
            $stmt_mun_count->execute($ids_esdeveniments);
            $estadistiques['total_municipis'] = $stmt_mun_count->fetchColumn();
        }
        $proper_esdeveniment = $tots_els_esdeveniments[0];
        $data_proper = new DateTime($proper_esdeveniment['data_inici']);
        $formatter_data = new IntlDateFormatter('ca_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'd MMMM');
        $estadistiques['proxim_esdeveniment_data'] = $formatter_data->format($data_proper);
    }
    
    foreach ($tots_els_esdeveniments as &$esdeveniment) {
        if (empty($esdeveniment['imatge'])) {
            $esdeveniment['imatge'] = $imatges_de_recurs[array_rand($imatges_de_recurs)];
            $esdeveniment['es_imatge_de_recurs'] = true;
        } else {
            $esdeveniment['es_imatge_de_recurs'] = false;
        }
    }
    unset($esdeveniment);

    $esdeveniments_per_setmana = [];
    $formatter_setmana = new IntlDateFormatter('ca_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, null, 'd MMMM');
    foreach ($tots_els_esdeveniments as $esdeveniment) {
        $data = new DateTime($esdeveniment['data_inici']);
        $any = $data->format("Y");
        $setmana_num = (int)$data->format("W");
        $clau_setmana = $any . '-' . str_pad($setmana_num, 2, '0', STR_PAD_LEFT);
        if (!isset($esdeveniments_per_setmana[$clau_setmana])) {
            $primer_dia = new DateTime();
            $primer_dia->setISODate($any, $setmana_num, 1);
            $ultim_dia = new DateTime();
            $ultim_dia->setISODate($any, $setmana_num, 7);
            $esdeveniments_per_setmana[$clau_setmana] = [
                'titol' => 'Setmana del ' . $formatter_setmana->format($primer_dia) . ' al ' . $formatter_setmana->format($ultim_dia),
                'esdeveniments' => []
            ];
        }
        $esdeveniments_per_setmana[$clau_setmana]['esdeveniments'][] = $esdeveniment;
    }

} catch (PDOException $e) {
    error_log("Error al llistar esdeveniments públics: " . $e->getMessage());
    $esdeveniments_per_setmana = [];
    $municipis = [];
    $categories = [];
    $subcategories = [];
}

include __DIR__ . '/../vistes/llistar_esdeveniments.php';