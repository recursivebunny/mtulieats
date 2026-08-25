<?php
    /*
    This file handles setting up and interacting with the database
    It should be included at the beginning of any file which uses the database
    It should not be queried directly by the client */

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
    // Open existing database if it does exist
    else {
        $db = new SQLite3("writable/db.sqlite3");
    }

    // Validates the token stored in the current request's cookie exists and matches the provided verified state
    // Replies with 401 Unauthorized and exits if the token is not valid
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
