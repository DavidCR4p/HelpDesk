<?php
$host = 'localhost'; // Ou o IP do servidor, como 192.168.0.14
$dbname = 'helpdesk_db'; // Substitua pelo nome correto do seu banco de dados
$user = 'root';
$pass = 'root'; // Coloque 'root' como senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>