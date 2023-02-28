<?php session_start(); ?>

<?php
require_once('config/db.php');
if (isset($_SESSION['token'])){
  header('Location: index.php');
  exit();
}
else {
  $consulta="select * from hito.users where id_user = ?";
  $stmt = $conn->prepare($consulta);
  $stmt->bindParam(1, $_SESSION['token']);
  $stmt->execute();

  // Almacenar los datos en variables
  if ($row=$stmt->fetch()) {
    $username = $row[1];
    $email = $row[2];
  }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NEOLAND</title>
    <link rel="shortcut icon" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Playfair&#43;Display:700,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
  </head>

  <body id="custom">
    <div class="header-custom">
      <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4">
          <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
            <li><a href="index.php"><img src="img/logo.svg" class="header-logo"/></a></li>
            <li><a href="blog.php" class="nav-link px-2 link-dark">Blog</a></li>
            <li><a href="publicar.php" class="nav-link px-2 link-dark">Publicar</a></li>
            <li><a href="admin.php" class="nav-link px-2 link-dark">Administrar</a></li>
          </ul>

          <?php
          if (isset($_SESSION['token'])){
            echo '<div class="col-md-3 text-end btns-custom">
            <a href="profile.php"><button type="button" class="btn btn-outline-primary me-2">Perfil de '.$username.'</button></a>
            <a href="logout.php"><button type="button" class="btn btn-primary">Cerrar sesión</button></a>
          </div>';
          }
          else {
            echo '<div class="col-md-3 text-end btns-custom">
            <a href="login.php"><button type="button" class="btn btn-outline-primary me-2">Iniciar sesión</button></a>
            <a href="register.php"><button type="button" class="btn btn-primary">Regístrate</button></a>
          </div>';
          }
          ?>
        </header>
      </div>
    </div><!--end of header-custom-->

<div class="container wrapper-narrow px-4 py-5" id="custom-cards">
  <div class="form-register w-100 m-auto">
  <form action="register.php" method="post">
    <h1 class="h3 mb-3 fw-normal">Crea una nueva cuenta</h1>

    <div class="mb-3">
      <input type="text" name="user" class="form-control input-16" placeholder="Usuario" required>
    </div>

    <div class="mb-3">
      <input type="email" name="email" class="form-control input-16" placeholder="Email" required>
    </div>

    <div class="mb-3">
      <input type="password" name="password" class="form-control input-16" placeholder="Contraseña" required>
    </div>

    <div class="mb-3">
      <input type="password" name="password2" class="form-control input-16" placeholder="Repita la contraseña" required>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Regístrate</button>
  </form>

  <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = $_POST['user'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $password2 = $_POST['password2'];

  function emailExiste($email, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM HITO.users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    return $count > 0;
  }
  if ($password==$password2) {
    {
      if (emailExiste($email, $conn)) {
        // El email ya existe, mostrar mensaje de error
        echo '<div class="alert alert-custom alert-warning" role="alert">
        Este email ya existe, por favor, inténtalo con otro.
        </div>';
      }
      else {
        $sql = "CALL hito.sp_add_user(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $user);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $password);
        $stmt->execute();
      
        echo '<div class="alert alert-custom alert-success" role="alert">
          Usuario registrado con éxito.
        </div>';
        }
      } 
  }
  else {
    echo '<div class="alert alert-custom alert-warning" role="alert">
    Las contraseñas no coinciden, inténtalo de nuevo.
    </div>';
  }
}
?>
<p class="text-small">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
<p class="mt-5 mb-3 text-muted">&copy; 2023 Neoland, Inc</p>
</div>
</div><!--end of container-->

<?php include('footer.php');?>