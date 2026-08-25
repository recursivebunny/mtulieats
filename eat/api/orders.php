<?php
    /*
    This file provides a list of orders to the client
    It should be queried by clients to display the order list
    Request: GET
    Responses:
        405 - Request was not a POST request
        401 - Session is invalid
        200 - Orders provided
    Response JSON:
        {
            "id": "<order ID>",
            "username": "<username of order placer>",
            "assigned_to": "[username of order fulfiller]",
            "content": <JSON data describing order>,
            "expires": <UNIX timestamp of order expiry>,
            "comment": "<order comment>"
        } */

    // Request isn't a GET request; reply with 405 Method Not Allowed
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(405);
        die();
    }

    // Load database
    include("db.php");

    validateToken();

    // Get list of orders
    $results = $db->query("SELECT id, username, assigned_to, content, status, expires, comment FROM orders");

    // Build and return orders JSON
    $data = [];
    while ($results !== false && $row = $results->fetchArray(SQLITE3_ASSOC)) {
        $row["content"] = json_decode($row["content"]);
        array_push($data, $row);
    }
    echo json_encode(array("orders" => $data));

    // Close db
    $db->close();

    die();
?>
