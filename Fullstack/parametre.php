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

// Vérification si le formulaire a été soumis
if (isset($_POST['submit'])) {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des valeurs du formulaire
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $mail = isset($_POST['mail']) ? $_POST['mail'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';

    // Mise à jour des informations de l'utilisateur dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET name = :name, first_name = :first_name, mail = :mail, phone = :phone, gender = :gender WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirection vers la page de profil
    header('Location: profil.php');
    exit();
}

// Récupération des informations de l'utilisateur à partir de la base de données
try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost:3306; dbname=linkhub", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparation de la requête
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Récupération des informations de l'utilisateur
    $row = $stmt->fetch();
    $name = $row['name'];
    $first_name = $row['first_name'];
    $mail = $row['mail'];
    $phone = $row['phone'];
    $gender = $row['gender'];
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Fermeture de la connexion à la base de données
$pdo = null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/footer_shortpage.css"> 
    <link rel="stylesheet" href="./css/button.css">
    <script src="Boutton.js"></script>
    <title>Modifier le profil</title>
    <?php
        require_once './pages/header.php';
        require_once 'update_status.php';
    ?>
        <br>
    </head>
    <body>
        <h1>Modifier le profil</h1>
        <form action="profil_edit.php" method="post">
            <label for="name">Nom :</label>
            <input type="text" name="name" value="<?php echo $name; ?>"><br>
            <br>
            <label for="first_name">Prénom :</label>
            <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br>
            <br>
            <label for="mail">Email :</label>
            <input type="email" name="mail" value="<?php echo $mail; ?>"><br>
            <br>
            <label for="phone">Téléphone :</label>
            <input type="tel" name="phone" value="<?php echo $phone; ?>"><br>
            <br>
            <label for="gender">Genre :</label>
            <select name="gender">
                <option value="F" <?php if ($gender == 'F') echo 'selected'; ?>>Femme</option>
                <option value="M" <?php if ($gender == 'M') echo 'selected'; ?>>Homme</option>
            </select>
            <br>
            <input type="submit" name="submit" value="Enregistrer">
        </form>
        <br> 
        <label class="switch">
            <input type="checkbox" id="toggleButton">
            <span class="slider"></span>
        </label>
        <br>
        <a href="update_status.php" onclick="return confirm('Etes-vous bien sûr de vouloir faire cela ?')">Activer / Désactiver mon compte</a>
        <br>
        <a href="delete_account.php" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">Supprimer mon compte</a>
        <br>
        <a href="profil.php">Retourner au profil</a>
        <br>
        <a href="logout.php">Déconnexion</a>
        <?php require_once './pages/footer.php'; ?>
    </body>
    </html>
    
