<?php session_start(); ?>

<?php
require_once('config/db.php');
if (isset($_SESSION['token'])){
  $login=true;
  $consulta="select * from hito.users where id_user = ?";
  $stmt = $conn->prepare($consulta);
  $stmt->bindParam(1, $_SESSION['token']);
  $stmt->execute();

  // Almacenar los datos del usuario logueado en variables
  if ($row=$stmt->fetch()) {
    $username = $row['username'];
    $email = $row['email'];
    $pfp_user = $row['user_image'];
    $detail = $row['detail'];
    $id_user = $row['id_user'];

    if (isset($pfp_user)){
      // Decodificar imagen de perfil
      $pfp_string=stream_get_contents($pfp_user); 
      $pfp_codificada=base64_encode($pfp_string); 
      $url_pfp='data:image/jpg;base64,'.$pfp_codificada;
      //echo '<img src="'.$url_pfp.'" />'; // Test de muestra de imagen
    }
  }
}
else {
  $login=false;
  header('Location: login.php');
  exit();
}
?>

<?php
$flag=true;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $detail = $_POST['detail'];
      $pfp = $_FILES['pfp']['tmp_name'];
      $pfp_decod = file_get_contents($pfp);

      // Lista de extensiones permitidas
      $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif', 'JPG');
      // Obtener la extensión del archivo
      $extension = pathinfo($_FILES['pfp']['name'], PATHINFO_EXTENSION);

      // Validar la extensión del archivo
      if (!in_array($extension, $extensiones_permitidas)) {
      $flag=false;

    }  else {
      $sql = 'CALL hito.sp_update_user(?, ?, ?)';
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(1, $detail);
      $stmt->bindParam(2, $pfp_decod, PDO::PARAM_LOB);
      $stmt->bindParam(3, $_SESSION['token']);
      $stmt->execute();
      
      header('Location: profile.php?success=1');
      exit;
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

  <div class="container container-custom">
  <?php
  if ($flag==false) {
    echo '<div class="alert alert-danger alert-blog" role="alert">
    Lo sentimos, el formato de archivo no es válido.</div>';

  }
    if (isset($_GET['success']) && $_GET['success'] === '1') {
      echo '<div class="alert alert-success alert-blog" role="alert">
            Información de perfil actualizada.</div>';
    }
    ?>
    <div class="flexbox flexbox--publicar margin-top-50">
      <div class="column--bar column--bar--profile">
        <div class="bar bar--profile">
          <h3 class=""><?php echo $username ?></h3>
          <div class="profile-picture" style="background-image: url('<?php if (isset($url_pfp)){ echo $url_pfp; } else { echo "img/default_pfp.png";};?>');">
          </div>
          <p class="profile-description"><?php if (isset($detail)){ echo $detail; } ?></p>
          <p class="profile-email"><?php echo $email ?></p>
        </div><!--end of bar-->
      </div><!--end of column--bar-->
        
      <div class="column--main column--main--publicar">
        <div class="box box--publicar">
          <form action="profile.php" method="post" enctype="multipart/form-data">
            <div class="input-group mb-3">
              <span class="input-group-text">@</span>
              <input type="text" class="form-control input-8" placeholder="<?php echo $username ?>" disabled>
            </div>
            <p class="text-smaller">En este momento no puedes cambiar tu nombre de usuario.</p>
            <div class="mb-3">
              <label for="detail" class="form-label">Añade más información a tu perfil</label>
              <input type="text" name="detail" class="form-control" placeholder="Introduce una descripcion corta">
            </div>

            <label for="file" class="form-label">Selecciona imagen de perfil</label>
            <div class="input-group margin-bottom-16">
              <input name="pfp" class="form-control" type="file" id="file" required>
              <a href="delete-pfp.php?id=<?php echo $id_user ?>" class="btn btn-outline-danger" type="button">Eliminar imagen actual</a>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
          </form>
        </div><!--end of box-->
      </div><!--end of column--main-->
    </div><!--end of flexbox-->
  </div><!--end of container-->

<?php include('footer.php');?>