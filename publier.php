<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $token = bin2hex(random_bytes(30));
    $_SESSION["token"] = $token;
} else {
    $token = $_SESSION["token"] ?? null;

    // Vérification obligatoire
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $token) {
        die("Les tokens sont différents. Formulaire invalide !");
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    if (empty($_POST["title"])) {
        echo "Veuillez entrer le titre.<br>";
        exit; 
    }
    else {
        $title = htmlspecialchars($_POST["title"]);   }


    if (empty($_FILES["picture"]["name"])) {
        echo "Veuillez entrer un fichier.<br>";
        exit; 
    }
    else {
        $filename = $_FILES["picture"]["name"];}

    if (
        $_FILES["picture"]["type"] != "image/jpeg" &&
        $_FILES["picture"]["type"] != "image/png"
    ) {
        echo "Le fichier doit être une image .<br>";
        exit; 
    }
    if(
        isset($_FILES["picture"])  && 
        !empty($_FILES["picture"]["name"]) 
        ){
  move_uploaded_file(
               $_FILES["picture"]["tmp_name"], 
               "uploads/".$_FILES["picture"]["name"] 
            );

        }


    $date = new DateTime("now", new DateTimeZone("Europe/Paris"));
    $datetime = $date->format("Y-m-d H:i");
    $description =$_POST["description"] ?? null;

require_once 'conf.php';
$dsn = $Confdsn;
 $login = $Conflogin;

    try{
        $db = new PDO($dsn,$login,);
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $res = $db->prepare(
        "INSERT INTO publication(title, description, picture, datetime, is_published)
         VALUES (:title, :description, :image, :datetime, :is_published)"
    );

    $res->execute([
        ":title" => $title,
        ":description" => $description,
        ":image" => $filename,
        ":datetime" => $datetime,
        ":is_published" => true
    ]);
    }catch (Exception $e){
        echo"<pre>";
        var_dump($e);die;
    
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TP FINAL</title>
</head>
<body>
   <form action="#" method="POST" enctype="multipart/form-data">

    <div>
        <label for="picture">Image</label>
        <input type="file" name="picture" id="picture">
    </div>
<div>
            <label for="title">Titre</label>
            <input type="text" name="title" id="title">
        </div>

    <div>
            <label for="description">Description </label>
            <textarea name="description" id="description" cols="40" rows="10"></textarea>
        </div>
        <div>
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
        </div>
        <div>
            <button>Publier</button>
        </div>
</form> 
</body>
</html>

 