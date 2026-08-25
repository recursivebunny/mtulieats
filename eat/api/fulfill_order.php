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
  if (!isset($data["id"]) || !isset($data["code"])) {
    http_response_code(400);
    die();
  }

  // Fetch order
  $smt = $db->prepare("SELECT status, assigned_to FROM orders WHERE id = :id");
  $smt->bindValue(":id", $data["id"]);
  $result = $smt->execute();
  $row = $result->fetchArray();

  // Order not found; reply with 404 Not Found
  if ($row === false) {
    http_response_code(404);
    die();
  }

  // Order not assigned or user is not assignee; reply with 401 Unauthorized
  if ($row["status"] != "assigned" || $row["assigned_to"] != $username) {
    http_response_code(401);
    die();
  }

  // Update order in db
  $smt = $db->prepare("UPDATE orders SET status = 'fulfilled', code = :code WHERE id = :id");
  $smt->bindValue(":id", $data["id"]);
  $smt->bindValue(":code", $data["code"]);
  $smt->execute();

  // Close db
  $db->close();

  http_response_code(204);
  die();
?>
