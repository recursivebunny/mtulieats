<?php
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
