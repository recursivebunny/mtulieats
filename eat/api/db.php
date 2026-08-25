<?php
    // Set up database if it doesn't exist
    if (!file_exists("writable/db.sqlite3")) {
        $db = new SQLite3("writable/db.sqlite3");

        // Sessions table
        $db->query("CREATE TABLE sessions (
            token varchar(32) PRIMARY KEY,
            username varchar(64),
            verify_code varchar(8),
            verified bit)");

        // Orders table
        $db->query("CREATE TABLE orders (
            id varchar(32) PRIMARY KEY,
            username varchar(64),
            assigned_to varchar(64),
            content varchar(2047),
            status varchar(16),
            expires int,
            code varchar(16),
            comment varchar(1023))");
    }
    else {
        $db = new SQLite3("writable/db.sqlite3");
    }

    // Returns the username of the current user if valid; otherwise throws 401 Unauthorized
    function validateToken($verified = 1) {
        global $db;

        $smt = $db->prepare("SELECT username FROM sessions WHERE token = :token AND verified = :verified");
        $smt->bindValue(":token", $_COOKIE["token"]);
        $smt->bindValue(":verified", $verified);
        $result = $smt->execute();
        $row = $result->fetchArray();

        if ($row === false) {
            http_response_code(401);
            die();
        }

        return $row["username"];
    }
?>
