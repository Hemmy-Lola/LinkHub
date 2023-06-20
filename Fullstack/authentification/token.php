<?php


function create_token(){
    
    session_start();
    
    $pdo = new PDO("mysql:host=localhost:3306;dbname=linkhub", "root", "");
    $requete = $pdo->prepare("
        SELECT * FROM users 
        WHERE mail = :login
    
    ");
    
    $requete->execute([
    
        ":login" => $_SESSION["login"],
    
    ]);
    

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    $random_token = bin2hex(random_bytes(64));

    $header = json_encode([

        'typ' => 'JWT',
        'alg' => 'HS256'

    ]);
    

    $payload = json_encode([
        
        'user_id' => $utilisateur["id"],
        'user_email' => $utilisateur["mail"],
        'user_first_name' => $utilisateur["first_name"],
        'user_last_name' =>$utilisateur["name"],
        'gender' => $utilisateur["gender"],
        'user_password' => $utilisateur["password"],
        'user_phone_numb' => $utilisateur["phone"],
        'token' => $random_token

    ]);


    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'S4lutc4v43251!', true);

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    
    $_SESSION["token"] = $jwt;

    if ($_SESSION["token"] == $jwt) {

        $_SESSION["validtoken"] = true;
    
    } else {
    
        $erreur = "";
    
    };
    
};

?>