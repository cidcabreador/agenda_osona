<?php
/**
 * SCRIPT DE MIGRACIÓ DE DADES (VERSIÓ 12.0 - LA FINAL)
 *
 * Finalitat: Aquest script es dedica EXCLUSIVAMENT a migrar les dades.
 *
 * CORRECCIÓ: S'ha afegit una comprovació per ignorar "dades òrfenes"
 * i evitar l'error de 'foreign key constraint' amb els organitzadors.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(1800); // 30 minuts

// ===================================================================
// ===== FUNCIONS D'AJUDA =====
// ===================================================================
function formatarTelefon($telefon) {
    if (empty($telefon)) return null;
    $net = preg_replace('/[^0-9]/', '', $telefon);
    if (strlen($net) === 11 && substr($net, 0, 2) === '34') $net = substr($net, 2);
    if (strlen($net) === 9) return substr($net, 0, 3) . ' ' . substr($net, 3, 3) . ' ' . substr($net, 6, 3);
    return null;
}

function netejarHtml($text) {
    if ($text === null) return null;
    $text_net = strip_tags($text);
    $text_net = html_entity_decode($text_net, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim($text_net);
}

// --- 1. CONFIGURACIÓ DE LES CONNEXIONS ---
$db_antiga_host = 'localhost';
$db_antiga_user = 'root';
$db_antiga_pass = ''; // La teva contrasenya
$db_antiga_name = 'agendaosona';

$db_nova_host = 'localhost';
$db_nova_user = 'root';
$db_nova_pass = ''; // La teva contrasenya
$db_nova_name = 'agenda_osona';

echo "<h1>Iniciant migració de dades (versió 12.0)...</h1>";

// --- INICI DEL PROCÉS ---
$conn_antiga = new mysqli($db_antiga_host, $db_antiga_user, $db_antiga_pass, $db_antiga_name);
if ($conn_antiga->connect_error) die("Error de connexió amb la BBDD antiga: " . $conn_antiga->connect_error);
$conn_antiga->set_charset('utf8mb4');

$conn_nova = new mysqli($db_nova_host, $db_nova_user, $db_nova_pass, $db_nova_name);
if ($conn_nova->connect_error) die("Error de connexió amb la BBDD nova: " . $conn_nova->connect_error);
$conn_nova->set_charset('utf8mb4');

// --- MAPES DE COLUMNES ---
$mapa_columnes_subcategories = ['c1'=>1,'c2'=>2,'c3'=>3,'c4'=>4,'c5'=>5,'c6'=>6,'c7'=>7,'c8'=>8,'c9'=>9,'c10'=>10,'c11'=>11,'c12'=>12,'n1'=>13,'n2'=>14,'n3'=>15,'n4'=>16,'n5'=>17,'n6'=>18,'n7'=>19,'n8'=>20,'g1'=>21,'g2'=>22,'g3'=>23,'g4'=>24,'g5'=>25];
$mapa_columnes_perfils_edat = ['e1'=>1,'e2'=>2,'e3'=>3];

try {
    // Esborrem només les dades, no les taules
    echo "<h2>Buidant les dades existents a la base de dades nova...</h2>";
    $conn_nova->query("SET FOREIGN_KEY_CHECKS=0");
    $conn_nova->query("TRUNCATE TABLE `esdeveniment_perfils_edat`");
    $conn_nova->query("TRUNCATE TABLE `esdeveniment_subcategories`");
    $conn_nova->query("TRUNCATE TABLE `esdeveniment_municipis`");
    $conn_nova->query("TRUNCATE TABLE `esdeveniment_organitzadors`");
    $conn_nova->query("TRUNCATE TABLE `suscripcions_newsletter`");
    $conn_nova->query("TRUNCATE TABLE `esdeveniments`");
    $conn_nova->query("TRUNCATE TABLE `municipis`");
    $conn_nova->query("TRUNCATE TABLE `organitzadors`");

    echo "<p>OK: Dades anteriors esborrades.</p>";

    $conn_nova->begin_transaction();

    // --- Migrar Municipis ---
    echo "<h2>Migrant Municipis...</h2>";
    $result_mun = $conn_antiga->query("SELECT id, municipi, cp FROM pobles");
    $stmt_mun = $conn_nova->prepare("INSERT INTO municipis (id, nom, codiPostal) VALUES (?, ?, ?)");
    $mapa_municipios = [];
    while ($fila = $result_mun->fetch_assoc()) {
        $stmt_mun->bind_param('iss', $fila['id'], $fila['municipi'], $fila['cp']);
        $stmt_mun->execute();
        $mapa_municipios[$fila['id']] = $fila['id'];
    }
    echo "<p>OK: Municipis migrats.</p>";
    $stmt_mun->close();

    // --- Migrar Organitzadors ---
    echo "<h2>Migrant Organitzadors...</h2>";
    $result_org = $conn_antiga->query("SELECT id, nom, email, telefon, web, logo FROM organitzadors");
    $stmt_org = $conn_nova->prepare("INSERT INTO organitzadors (id, nom, email, telefon, web, logo, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    $rol = 'organitzador';
    $mapa_organizadores = [];
    while ($fila = $result_org->fetch_assoc()) {
        $telefon_formatat = formatarTelefon($fila['telefon']);
        $stmt_org->bind_param('isssssss', $fila['id'], $fila['nom'], $fila['email'], $telefon_formatat, $fila['web'], $fila['logo'], $password_hash, $rol);
        $stmt_org->execute();
        $mapa_organizadores[$fila['id']] = $fila['id'];
    }
    echo "<p>OK: Organitzadors migrats.</p>";
    $stmt_org->close();
    
    // --- Migrar Esdeveniments i les seves Relacions ---
    echo "<h2>Migrant Esdeveniments i les seves relacions...</h2>";
    $stmt_evento = $conn_nova->prepare( "INSERT INTO esdeveniments (id, nom, descripcio, data_inici, data_fi, hora, adreca, preu, imatge, latitud, longitud) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_pivot_municipi = $conn_nova->prepare("INSERT INTO esdeveniment_municipis (id_esdeveniment, id_municipi) VALUES (?, ?)");
    $stmt_pivot_organitzador = $conn_nova->prepare("INSERT INTO esdeveniment_organitzadors (id_esdeveniment, id_organitzador) VALUES (?, ?)");
    $stmt_pivot_subcategoria = $conn_nova->prepare("INSERT INTO esdeveniment_subcategories (id_esdeveniment, id_subcategoria) VALUES (?, ?)");
    $stmt_pivot_perfil_edat = $conn_nova->prepare("INSERT INTO esdeveniment_perfils_edat (id_esdeveniment, id_perfil_edat) VALUES (?, ?)");
    
    $result_actes = $conn_antiga->query("SELECT * FROM actes");
    $eventos_migrados = 0;
    while ($acte = $result_actes->fetch_assoc()) {
        $descripcio_neta = netejarHtml($acte['text']);
        $data_final_valida = (empty($acte['data_fi']) || $acte['data_fi'] == '0000-00-00') ? null : $acte['data_fi'];
        $stmt_evento->bind_param('issssssssdd', $acte['id'], $acte['titol'], $descripcio_neta, $acte['data_inici'], $data_final_valida, $acte['horari'], $acte['adreca'], $acte['preu'], $acte['img1'], $acte['lat'], $acte['lng']);
        $stmt_evento->execute();
        
        $inserted_orgs = []; $inserted_mun = []; $inserted_subcat = []; $inserted_perfil = [];
        
        for ($i = 1; $i <= 52; $i++) { 
            if (isset($acte['p'.$i]) && $acte['p'.$i] == '1' && isset($mapa_municipios[$i]) && !in_array($i, $inserted_mun)) { 
                $stmt_pivot_municipi->bind_param('ii', $acte['id'], $i); 
                $stmt_pivot_municipi->execute(); 
                $inserted_mun[] = $i; 
            } 
        }
        
        // ===== AQUÍ ESTÀ LA CORRECCIÓ =====
        for ($i = 1; $i <= 4; $i++) { 
            $id_org_antic = $acte['org'.$i];
            // Abans d'insertar, comprovem que l'organitzador existeix al nostre mapa.
            if ($id_org_antic > 0 && isset($mapa_organizadores[$id_org_antic]) && !in_array($id_org_antic, $inserted_orgs)) { 
                $stmt_pivot_organitzador->bind_param('ii', $acte['id'], $id_org_antic); 
                $stmt_pivot_organitzador->execute(); 
                $inserted_orgs[] = $id_org_antic; 
            } 
        }
        
        foreach ($mapa_columnes_subcategories as $col => $id) { if (isset($acte[$col]) && $acte[$col] == '1' && !in_array($id, $inserted_subcat)) { $stmt_pivot_subcategoria->bind_param('ii', $acte['id'], $id); $stmt_pivot_subcategoria->execute(); $inserted_subcat[] = $id; } }
        foreach ($mapa_columnes_perfils_edat as $col => $id) { if (isset($acte[$col]) && $acte[$col] == '1' && !in_array($id, $inserted_perfil)) { $stmt_pivot_perfil_edat->bind_param('ii', $acte['id'], $id); $stmt_pivot_perfil_edat->execute(); $inserted_perfil[] = $id; } }
        $eventos_migrados++;
    }
    echo "<p>OK: S'han migrat $eventos_migrados esdeveniments.</p>";
    $stmt_evento->close(); $stmt_pivot_municipi->close(); $stmt_pivot_organitzador->close(); $stmt_pivot_subcategoria->close(); $stmt_pivot_perfil_edat->close();

    // --- Migrar Subscripcions a la Newsletter ---
    echo "<h2>Migrant Subscripcions a la Newsletter...</h2>";
    $result_newsletter = $conn_antiga->query("SELECT * FROM newsletter");
    $stmt_newsletter = $conn_nova->prepare("INSERT INTO suscripcions_newsletter (email, preferencies, data_suscripcio) VALUES (?, ?, ?)");
    $subs_migrades = 0;
    while ($sub = $result_newsletter->fetch_assoc()) {
        $preferencies = ['municipis' => [], 'subcategories' => [], 'perfils_edat' => []];
        for ($i = 1; $i <= 52; $i++) { if (!empty($sub['p'.$i])) $preferencies['municipis'][] = $i; }
        foreach ($mapa_columnes_subcategories as $col => $id) { if (!empty($sub[$col])) $preferencies['subcategories'][] = $id; }
        foreach ($mapa_columnes_perfils_edat as $col => $id) { if (!empty($sub[$col])) $preferencies['perfils_edat'][] = $id; }
        $json_preferencies = json_encode($preferencies);
        $stmt_newsletter->bind_param('sss', $sub['email'], $json_preferencies, $sub['data_alta']);
        $stmt_newsletter->execute();
        $subs_migrades++;
    }
    echo "<p>OK: S'han migrat $subs_migrades subscripcions.</p>";
    $stmt_newsletter->close();

    $conn_nova->commit();
    echo "<h1 style='color:green;'>¡MIGRACIÓ COMPLETADA AMB ÈXIT!</h1>";

} catch (Exception $e) {
    if ($conn_nova->connect_errno) {} else { $conn_nova->rollback(); }
    echo "<h1 style='color:red;'>ERROR DURANT LA MIGRACIÓ:</h1>";
    echo "<p>S'han desfet tots els canvis per mantenir la integritat de la base de dades.</p>";
    echo "<p><strong>Missatge de l'error:</strong> " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn_antiga) && $conn_antiga) $conn_antiga->close();
    if (isset($conn_nova) && $conn_nova) $conn_nova->close();
}
?>