<?php
    /*
    This file invalidates all sessions with the same username as the client's current session 
    It should be queried by clients to deauthenticate ALL sessions
    Request: POST
    Responses:
        405 - Request was not a POST request
        401 - Session is invalid
        204 - Successfully invalidated all sessions */

    // Request isn't a POST request; reply with 405 Method Not Allowed
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        die();
    }

    // Load database
    include("db.php");

    $username = validateToken();

    // Delete sessions with username
    $smt = $db->prepare("DELETE FROM sessions WHERE username = :username");
    $smt->bindValue(":username", $row["username"]);
    $smt->execute();

    // Close db
    $db->close();

    http_response_code(204);
    die();
?>
