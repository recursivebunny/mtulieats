<?php
  // Request isn't a POST request; reply with 405 Method Not Allowed
  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die();
  }

  // Load database
  include("db.php");

  $username = validateToken();

  // Parse order info
  $data = json_decode(file_get_contents('php://input'), true);

  // Request JSON is invalid; reply with 400 Bad Request
  if (!isset($data["id"])) {
    http_response_code(400);
    die();
  }

  // Fetch order
  $smt = $db->prepare("SELECT status FROM orders WHERE id = :id");
  $smt->bindValue(":id", $data["id"]);
  $result = $smt->execute();
  $order = $result->fetchArray();

  // Order not found; reply with 404 Not Found
  if ($row === false) {
    http_response_code(404);
    die();
  }

  // Order not open; reply with 401 Unauthorized
  if ($order["status"] != "open") {
    http_response_code(401);
    die();
  }

  // Update order in db
  $smt = $db->prepare("UPDATE orders SET status = 'assigned', assigned_to = :username WHERE id = :id");
  $smt->bindValue(":username", $username);
  $smt->bindValue(":id", $data["id"]);
  $smt->execute();

  // Close db
  $db->close();

  http_response_code(204);
  die();
?>
