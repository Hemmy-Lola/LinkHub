<?php
// Démarrage de la session
session_start();

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupération de l'ID de l'utilisateur
$id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "linkhub";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion à la base de données
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Suppression de l'utilisateur de la base de données
$sql = "DELETE FROM users WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    // Déconnexion de l'utilisateur
    session_unset();
    session_destroy();
    // Redirection
    header('Location: login.php');
    exit();
} else {
    echo "Erreur lors de la suppression de l'utilisateur : " . $conn->error;
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
