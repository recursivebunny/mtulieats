<?php
  // Request isn't a POST request; reply with 405 Method Not Allowed
  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die();
  }

  // Read JSON
  $data = json_decode(file_get_contents('php://input'), true);

  // Request JSON is invalid; reply with 400 Bad Request
  if (!isset($data["username"]) || !ctype_alnum($data["username"])) {
    http_response_code(400);
    die();
  }
  $username = strtolower($data["username"]);

  // Load database
  include("db.php");

  // Generate random session token and verification code
  $token = bin2hex(random_bytes(16));
  $verify_code = bin2hex(random_bytes(4));

  // TODO: REMOVE once email works
  $verify_code = "aabbccdd";

  // Add new row to sessions table
  $smt = $db->prepare("INSERT INTO sessions VALUES (:token, :username, :verify_code, 0)");
  $smt->bindValue(":token", $token);
  $smt->bindValue(":username", $username);
  $smt->bindValue(":verify_code", $verify_code);
  $smt->execute();

  // Close db
  $db->close();

  // Set session token and exit
  setcookie("token", $token, array("samesite" => "None", "secure" => true));
  setcookie("verified", "false", array("samesite" => "None", "secure" => true));
  http_response_code(204);

  // TODO: Send email with verify code

  die();
?>
