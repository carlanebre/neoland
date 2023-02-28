<?php session_start();

require_once('config/db.php');

if (isset($_GET['id'])){
      $delete_pfp = "CALL hito.sp_delete_pfp(?)";
      $stmt = $conn->prepare($delete_pfp);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();

      header('location:profile.php');
    }