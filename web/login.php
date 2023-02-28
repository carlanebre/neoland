<?php session_start(); ?>

<?php
require_once('config/db.php');
if (isset($_SESSION['token'])){
  $login=true;
  $consulta="select * from hito.users where id_user = ?";
  $stmt = $conn->prepare($consulta);
  $stmt->bindParam(1, $_SESSION['token']);
  $stmt->execute();

  // Almacenar los datos en variables
  if ($row=$stmt->fetch()) {
    $username = $row[1];
    $email = $row[2];
    $pfp_user = $row['user_image'];

    if (isset($pfp_user)){
      // Decodificar imagen de perfil
      $pfp_string=stream_get_contents($pfp_user); 
      $pfp_codificada=base64_encode($pfp_string); 
      $url_pfp='data:image/jpg;base64,'.$pfp_codificada;
    }
  }
  if(isset($_SESSION['login_before'])) {
    // El usuario ha iniciado sesión anteriormente, ejecutar el header
    header('location:index.php');
    exit;
  } else {
    // El usuario ha iniciado sesión por primera vez, marcar variable de sesión y no ejecutar el header
    $_SESSION['login_before'] = true;
  }
}
else {
  $login=false;
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

          <?php if ($login == true) { ?>
            <div class="nav-group">

            <?php if (!isset($pfp_user)) { // Si no tiene foto de perfil ?>
              <a href="profile.php" class="btn-round">
                <span></span>
                <ion-icon class="nav-user" name="person"></ion-icon>
              </a>

              <?php } else { // Si tiene foto de perfil?>
              <a href="profile.php" class="btn-round btn-round-grey">
                <span class="nav-pfp" style="background-image: url('<?php echo $url_pfp;?>');"></span>
              </a>

            <?php } // Cierra condicional si tiene foto de perfil ?>
              <a href="logout.php" class="btn-round">
                <span></span>
                <ion-icon class="nav-user" name="log-out-outline"></ion-icon>
              </a>
            </div>
          <?php } // Cierra condicional user logueado ?>

          <?php if ($login == false) { ?>
            <div class="col-md-3 text-end btns-custom">
              <a href="login.php"><button type="button" class="btn btn-outline-primary me-2">Iniciar sesión</button></a>
              <a href="register.php"><button type="button" class="btn btn-primary">Regístrate</button></a>
            </div>
          <?php } ?>
        </header>
      </div>
    </div><!--end of header-custom-->

<div class="container wrapper-narrow px-4 py-5" id="custom-cards">
  <main class="form-signin w-100 m-auto">
  <form action="login.php" method="post">
    <h1 class="h3 mb-3 fw-normal">Accede con tu cuenta</h1>

    <div class="input-login-group">
      <div class="form-floating">
        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="Password">
        <label for="floatingInput">Email</label>
      </div>
      <div class="form-floating">
        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
        <label for="floatingPassword">Contraseña</label>
      </div>
    </div>

    <!--<div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="checkbox">
      <label class="form-check-label" for="checkbox">Recuérdame</label>
    </div>-->

    <button class="w-100 btn btn-lg btn-primary" type="submit">Iniciar sesión</button>
  </form>

  <?php
    if (isset($_SESSION['token'])){
      echo '<div class="alert alert-success alert-custom" role="alert">
      Bienvenido de vuelta, '.$username.' &#128018</div>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once('config/db.php');
      $consulta="select * from hito.users where email = '{$_POST['email']}'";
      $resultado=$conn->query($consulta);
      
      if($resultado->rowCount()<1){
        echo '<div class="alert alert-custom alert-warning" role="alert">
        Lo sentimos, no existe este correo en nuestra base de datos.
        </div>';
      }else{
        while($row=$resultado->fetch()){
          if($row['user_pass']==md5($_POST['password'])){
            $_SESSION['token']=$row['id_user'];
            header('location:login.php');
            break; //deja de comprobar
          }// cierra if evalua contraseña
          
          else {
            echo '<div class="alert alert-custom alert-danger" role="alert">
            La contraseña no es correcta.</div>';
          }//cierra else evaluar contraseña
        }
      }
    }   
    ?>
    <p class="text-small smaller">¿No tienes cuenta? <a href="register.php">Crea una nueva cuenta</a></p>
    <p class="mt-5 mb-3 text-muted">&copy; 2023 Neoland, Inc</p>
</main>
</div><!--end of container-->

<?php include('footer.php');?>