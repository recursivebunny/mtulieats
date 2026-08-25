<?php
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
