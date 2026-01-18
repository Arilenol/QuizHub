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

/**
 * Vérifie si l'utilisateur est connecté et est administrateur
 * 
 * @return bool True si l'utilisateur est connecté et admin, false sinon
 */
function isAdminUser() {
    session_start();
    
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['id'])) {
        return false;
    }
    
    // Récupérer les informations de l'utilisateur
    try {
        $db = getDbConnection();
        $stmt = $db->prepare("SELECT admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && (int)$user['admin'] === 1;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Redirige vers la page d'accueil si l'utilisateur n'a pas les droits admin
 * Affiche un message d'erreur 403
 * 
 * @return void
 */
function requireAdmin() {
    if (!isAdminUser()) {
        http_response_code(403);
        die("Accès refusé : Vous devez être connecté avec les droits administrateur pour accéder à cette page.");
    }
}

?>