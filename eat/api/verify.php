<?php
    /*
    This file verifies an existing session using a verification code
    It should be queried by clients after /login with the emailed code
    Request: POST with JSON {"code": "<verification code>"}
    Responses:
        405 - Request was not a POST request
        400 - Invalid request data
        401 - Session not successfully verified
        204 - Verification successful */

            // Request isn't a POST request; reply with 405 Method Not Allowed
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        die();
    }

    // Request JSON is invalid; reply with 400 Bad Request
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data["code"])) {
        http_response_code(400);
        die();
    }

    // Load database
    include("db.php");

    validateToken(0);

    // Update row in db
    $smt = $db->prepare("UPDATE sessions SET verified = 1 WHERE token = :token AND verify_code = :verify_code AND verified = 0");
    $smt->bindValue(":token", $_COOKIE["token"]);
    $smt->bindValue(":verify_code", $data["code"]);
    $smt->execute();

    // Close db
    $db->close();

    // Set session token as cookie
    setcookie("verified", "true", array("samesite" => "None", "secure" => true));
    http_response_code(204);
    die();
?>
