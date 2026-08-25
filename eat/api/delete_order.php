<?php
    /*
    This file deletes an existing open order
    It should be queried by clients to delete their own orders
    Request: POST with JSON
        {"id": "<order ID>"}
    Responses:
        405 - Request was not a POST request
        401 - Invalid session token or unauthorized order
        400 - Invalid request data
        404 - Order does not exist
        204 - Order deleted */

    // Request isn't a POST request; reply with 405 Method Not Allowed
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        die();
    }

    // Load database
    include("db.php");

    $username = validateToken();

    // Parse delete info
    $data = json_decode(file_get_contents('php://input'), true);

    // Request JSON is invalid; reply with 400 Bad Request
    if (!isset($data["id"])) {
        http_response_code(400);
        die();
    }

    // Fetch order
    $smt = $db->prepare("SELECT username, status FROM orders WHERE id = :id");
    $smt->bindValue(":id", $data["id"]);
    $result = $smt->execute();
    $row = $result->fetchArray();

    // Order not found; reply with 404 Not Found
    if ($row === false) {
        http_response_code(404);
        die();
    }

    // Order was not created by user or order is not open; reply with 401 Unauthorized
    if ($row["username"] != $username || $row["status"] != "open") {
        http_response_code(401);
        die();
    }

    // Delete order
    $smt = $db->prepare("DELETE FROM orders WHERE id = :id");
    $smt->bindValue(":id", $data["id"]);
    $smt->execute();

    // Reply with order ID
    http_response_code(204);
    die();
?>
