<?php
    /*
    This file invalidates the client's current session
    It should be queried by clients to deauthenticate
    Request: POST
    Responses:
        405 - Request was not a POST request
        401 - Session is invalid
        204 - Successfully invalidated session */

    // Request isn't a POST request; reply with 405 Method Not Allowed
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        die();
    }

    // Load database
    include("db.php");

    validateToken();

    // Delete session from db
    $smt = $db->prepare("DELETE FROM sessions WHERE token = :token");
    $smt->bindValue(":token", $_COOKIE["token"]);
    $smt->execute();

    // Close db
    $db->close();

    http_response_code(204);
    die();
?>
