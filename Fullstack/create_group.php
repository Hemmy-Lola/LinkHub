<?php
require_once './include/verify_connection.php';

$pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
$methode = filter_input(INPUT_SERVER, "REQUEST_METHOD");
$Message = null;
$errorMessage = null;

require_once('function.php');

if ($methode == "POST") {
  $group_name = filter_input(INPUT_POST, "group_name");
  $event = filter_input(INPUT_POST, "event_event");
  $status = filter_input(INPUT_POST, "status");
  $description = filter_input(INPUT_POST, "description");

  if (isset($_POST['confirm_group'])) {
    $nameEvent = $_POST['group_name'];
    if (!empty($nameEvent)) {
      if (isBlacklisted($nameEvent)) {
        $errorMessage = "Le nom du groupe ne peut pas contenir de caractère interdit.";
        echo $errorMessage;
      } else {
        $Message = "Le groupe a été créé avec succès.";
        $requete = $pdo->prepare("
              INSERT INTO `group` (group_name, event, status, description) 
              VALUES (:group_name, :event, :status, :description)
            ");

        // Ajoute toutes les colonnes de la table "group"
        $requete->execute([
          ":group_name" => $group_name,
          ":event" => $event,
          ":status" => $status,
          ":description" => $description,
        ]);

        $group_id = $pdo->lastInsertId();

        // Ajoute les colonnes de la table "users"
        $requete2 = $pdo->prepare("
              SELECT name from users
              WHERE id = :id
            ");

        $requete2->execute([
          ":id" => $_SESSION['id']

        ]);

        $user_name = $requete2->fetch(PDO::FETCH_ASSOC);

        $name = $user_name['name'];

        if (isset($_SESSION['id'])) {
          // Ajoute les colonnes de la table "members_group"
          $requete_other_table = $pdo->prepare("
                INSERT INTO `members_group` (`name`, `role`, `user_id`, `group_id`) 
                VALUES (:name, :role, :user_id, :group_id)
              ");

          $requete_other_table->execute([
            ":name" => $name,
            ":role" => 'Admin',
            ":user_id" => $_SESSION['id'],
            ":group_id" => $group_id,
          ]);
        }
      }
      // Redirection vers une autre page
      // urlencode s'assure que les valeurs des paramètres sont correctement encodées
      header("Location: ./group_page.php?group_name=" . urlencode($group_name) . "&group_id=" . urlencode($group_id));
      exit;

    } else {
      $Message = "Veuillez saisir un nom pour le groupe.";

    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Groupe</title>
  <link rel="stylesheet" href="./css/header.css">
  <link rel="stylesheet" href="./css/footer.css">
  <link rel="stylesheet" href="./css/create_group.css">

</head>

<body>

  <?php require_once './pages/header.php' ?>

  <section>
    <div class="form_create_group">
    <form method="post">
      <fieldset>
        <legend>Créer ton propre groupe !</legend>
        <div>
          <label for="group_name">Nom du groupe :</label>
          <input type="text" id="group_name" name="group_name" required>
        </div>
        <div>
          <label for="description">Description :</label>
          <textarea id="description" name="description" required></textarea>
        </div>

        <div>
          <label for="event">Thème :</label>
          <label for="event_event_1">
            <input type="radio" id="event_event_1" name="event_event" value="meet-up" required>
            <p>Rencontre</p>
          </label>
          <label for="event_event_2">
            <input type="radio" id="event_event_2" name="event_event" value="concert">
            <p>Concert</p>
          </label>
          <label for="event_event_3">
            <input type="radio" id="event_event_3" name="event_event" value="convention">
            <p>Convention</p>
          </label>
        </div>
        <div>
          <label for="statut">Statut :</label>
          <label for="statut_1">
            <input type="radio" id="statut_1" name="status" value="public" required>
            <p>Public</p>
          </label>
          <label for="statut_2">
            <input type="radio" id="statut_2" name="status" value="private">
            <p>Privée</p>
          </label>
        </div>

        <input type="submit" name="confirm_group" value="Confirmer">

      </fieldset>
    </form>
</div>
  </section>

  <?= $Message; ?>
  <?= $errorMessage ?>

  <?php require_once './pages/footer.php' ?>
</body>

</html>