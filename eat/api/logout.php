<?php
  // Request isn't a POST request; reply with 405 Method Not Allowed
  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    die();
  }

  // Load database
  include("db.php");

  validateToken();

  // Delete session
  $smt = $db->prepare("DELETE FROM sessions WHERE token = :token");
  $smt->bindValue(":token", $_COOKIE["token"]);
  $smt->execute();

  // Close db
  $db->close();

  http_response_code(204);
  die();
?>
