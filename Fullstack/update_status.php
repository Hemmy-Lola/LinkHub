<?php
// Démarrage de la session
session_start();

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupération de l'ID de l'utilisateur
$id = $_SESSION['id'];

// Vérification si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mise à jour du statut de l'utilisateur
    $stmt = $pdo->prepare("UPDATE users SET status = 1 - status WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirection vers la page de profil
    header('Location: profil.php');
    exit();
}
?>
