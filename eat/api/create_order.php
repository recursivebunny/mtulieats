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
  if (!isset($data["content"]) || !isset($data["expires"]) || !isset($data["comment"])) {
    http_response_code(400);
    die();
  }

  // Input validation
  $content = json_encode($data["content"]);
  if (gettype($data["content"]) != "array" ||
      strlen($content) > 2047 ||
      strlen($data["comment"]) > 1023 ||
      gettype($data["expires"]) != "integer" ||
      $data["expires"] < time()
  ) {
    http_response_code(400);
    die();
  }

  // Make order ID
  $id = bin2hex(random_bytes(16));

  // Add order to db
  $smt = $db->prepare("INSERT INTO orders VALUES (:id, :username, null, :content, :status, :expires, null, :comment)");
  $smt->bindValue(":id", $id);
  $smt->bindValue(":username", $username);
  $smt->bindValue(":content", $content);
  $smt->bindValue(":status", "open");
  $smt->bindValue(":expires", $data["expires"]);
  $smt->bindValue(":comment", $data["comment"]);
  $smt->execute();

  // Close db
  $db->close();

  // Reply with order ID
  echo json_encode(array("id" => $id));
  die();
?>
