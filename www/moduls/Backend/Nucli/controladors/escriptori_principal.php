<?php
// moduls/Backend/Nucli/controladors/escriptori_principal.php (Versió millorada amb més estadístiques)

if (!estaAutenticat()) {
    redireccionar('index.php?accio=login');
}

$titol = 'Escriptori Principal';
$estadistiques = [];
$propers_esdeveniments = [];

try {
    // Obtenim els propers 5 esdeveniments (visible per a tots)
    $sql_propers = "
        SELECT e.id, e.nom, e.data_inici, 
               GROUP_CONCAT(DISTINCT m.nom SEPARATOR ', ') as municipi
        FROM esdeveniments e
        LEFT JOIN esdeveniment_municipis em ON e.id = em.id_esdeveniment
        LEFT JOIN municipis m ON em.id_municipi = m.id
        WHERE e.data_inici >= CURDATE()
        GROUP BY e.id
        ORDER BY e.data_inici ASC
        LIMIT 5
    ";
    $propers_esdeveniments = $pdo->query($sql_propers)->fetchAll();

    // Obtenim estadístiques generals (només per a l'admin)
    if (esAdmin()) {
        $total_esdeveniments = $pdo->query("SELECT COUNT(*) FROM esdeveniments")->fetchColumn();
        $total_organitzadors = $pdo->query("SELECT COUNT(*) FROM organitzadors")->fetchColumn();
        $total_municipis = $pdo->query("SELECT COUNT(*) FROM municipis")->fetchColumn();
        $total_subscripcions = $pdo->query("SELECT COUNT(*) FROM suscripcions_newsletter")->fetchColumn();
        
        $estadistiques = [
            'total_esdeveniments' => $total_esdeveniments,
            'total_organitzadors' => $total_organitzadors,
            'total_municipis' => $total_municipis,
            'total_subscripcions' => $total_subscripcions,
        ];

        // Obtenim els últims 5 esdeveniments creats
        $ultims_esdeveniments_creat = $pdo->query("SELECT id, nom FROM esdeveniments ORDER BY id DESC LIMIT 5")->fetchAll();
    }

} catch (PDOException $e) {
    error_log("Error al carregar l'escriptori principal: " . $e->getMessage());
    afegirToast("Hi ha hagut un error al carregar les dades de l'escriptori.", "error");
}

include __DIR__ . '/../vistes/escriptori_principal.php';