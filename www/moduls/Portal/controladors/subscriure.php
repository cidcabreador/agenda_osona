<?php
// moduls/Portal/controladors/subscriure.php (Amb selecció per organitzador)

$titol = 'Butlletí Setmanal';
$errors = [];
$success = false;
$email_enviat = '';

try {
    // Obtenim totes les dades necessàries de la BD per al formulari
    $municipis = $pdo->query("SELECT id, nom FROM municipis ORDER BY nom ASC")->fetchAll();
    $categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll();
    $subcategories = $pdo->query("SELECT id, nom, id_categoria FROM subcategories ORDER BY nom ASC")->fetchAll();
    // NOU: Obtenim també els organitzadors
    $organitzadors = $pdo->query("SELECT id, nom FROM organitzadors ORDER BY nom ASC")->fetchAll();

} catch (PDOException $e) {
    $municipis = []; $categories = []; $subcategories = []; $organitzadors = [];
    $errors[] = "No s'han pogut carregar les opcions de preferències.";
    error_log("Error al carregar dades per subscripció: " . $e->getMessage());
}

// CAPTCHA
$captchas = [
    ['pregunta' => 'De quin color és el cel?', 'resposta' => 'blau'],
    ['pregunta' => 'Quantes potes té un gat?', 'resposta' => '4'],
    ['pregunta' => 'Escriu la paraula "agenda" al revés:', 'resposta' => 'adnega'],
    ['pregunta' => 'Si ara és Agost, quin mes ve després?', 'resposta' => 'setembre'],
];
$captcha_aleatori = $captchas[array_rand($captchas)];
$pregunta_captcha = $captcha_aleatori['pregunta'];
$_SESSION['captcha_answer'] = $captcha_aleatori['resposta'];

// Processament del formulari
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $captcha_user = strtolower(trim($_POST['captcha'] ?? ''));
    $politica = isset($_POST['politica']);
    $email_enviat = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');

    if (!$email) $errors[] = "El format del correu electrònic no és vàlid.";
    if ($captcha_user !== $_SESSION['captcha_answer']) $errors[] = "La resposta a la pregunta de verificació no és correcta.";
    if (!$politica) $errors[] = "Has d'acceptar la política de privacitat.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM suscripcions_newsletter WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Aquest correu electrònic ja està subscrit al nostre butlletí.";
        } else {
            // NOU: Afegim els organitzadors a les preferències
            $preferencies = [
                'municipis' => $_POST['municipis'] ?? [],
                'subcategories' => $_POST['subcategories'] ?? [],
                'organitzadors' => $_POST['organitzadors'] ?? [], // Array d'IDs
            ];
            $preferencies_json = json_encode($preferencies);

            try {
                $sql = "INSERT INTO suscripcions_newsletter (email, preferencies) VALUES (:email, :preferencies)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email, 'preferencies' => $preferencies_json]);
                $success = true;
            } catch (PDOException $e) {
                error_log("Error en subscriure al newsletter: " . $e->getMessage());
                $errors[] = "S'ha produït un error inesperat. Intenta-ho de nou més tard.";
            }
        }
    }
}

include __DIR__ . '/../vistes/formulari_subscripcio.php';