<?php

function getDbConnection() {
    try{
        $conn = new PDO("sqlite:" . ROOT . "/database/database.db");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        die("Connection failed: " . $e->getMessage());
    }catch(Exception $e){
        die("Error: " . $e->getMessage());
    }
    return $conn;
}

?>