<?php session_start();

require_once('config/db.php');

if (isset($_GET['id'])){
      $delete_post = "CALL hito.sp_delete_post(?)";
      $stmt = $conn->prepare($delete_post);
      $stmt->bindParam(1, $_GET['id']);
      $stmt->execute();

      header('location:admin.php');
    }