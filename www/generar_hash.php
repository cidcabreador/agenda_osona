<?php
// generar_hash.php
$contrasenya_en_text_pla = '1234';
$hash_generat = password_hash($contrasenya_en_text_pla, PASSWORD_DEFAULT);

echo "La teva contrasenya de prova és: <b>" . $contrasenya_en_text_pla . "</b><br>";
echo "El HASH que has de posar a la base de dades és:<br>";
echo "<textarea rows='3' cols='80' readonly>" . htmlspecialchars($hash_generat) . "</textarea>";
?>