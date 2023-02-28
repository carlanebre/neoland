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
    $detail = $row['detail'];
    $pfp_user = $row['user_image'];

    if (isset($pfp_user)){
      // Decodificar imagen de perfil
      $pfp_string=stream_get_contents($pfp_user); 
      $pfp_codificada=base64_encode($pfp_string); 
      $url_pfp='data:image/jpg;base64,'.$pfp_codificada;
    }
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
    <div class="container container-custom">

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $cuerpo = $_POST['cuerpo'];
    $imagen = $_FILES['imagen']['tmp_name'];
    $fecha = $_POST['fecha'];

    // Lista de extensiones permitidas
    $extensiones_permitidas = array('jpg', 'jpeg', 'png', 'gif');
    // Obtener la extensión del archivo
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

    // Validar la extensión del archivo
    if (!in_array($extension, $extensiones_permitidas)) {
      echo '<div class="alert alert-danger alert-blog" role="alert">
      Lo sentimos, el formato de archivo no es válido.</div>';

    } else {

      // Leer el contenido del archivo
    $imagen_decod = file_get_contents($imagen);

    $sql = 'CALL hito.sp_add_post(?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $titulo);
    $stmt->bindParam(2, $username);
    $stmt->bindParam(3, $cuerpo);
    $stmt->bindParam(4, $imagen_decod, PDO::PARAM_LOB);
    $stmt->bindParam(5, $_SESSION['token'], PDO::PARAM_INT);
    $stmt->execute();

    echo '<div class="alert alert-success alert-blog" role="alert">
          Entrada publicada. Míralo en el <a href="blog.php">blog</a>.</div>';

    }

    
  }

  if ($login == true) { // Comprueba si usuario login ?>
      <div class="flexbox flexbox--publicar margin-top-50">
        <div class="column--bar column--bar--profile">
          <div class="bar bar--profile">
            <h3 class=""><?php echo $username ?></h3>
            <div class="profile-picture" style="background-image: url('<?php if (isset($url_pfp)){ echo $url_pfp; } else { echo "img/default_pfp.png";};?>');">
            </div>
            <p class="profile-description"><?php echo $detail ?></p>
            <p class="profile-email"><?php echo $email ?></p>
            <a href="profile.php">
              <button type="button" class="btn btn-primary"
              style="--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;">Modificar perfil</button>
            </a>
          </div><!--end of bar-->
        </div><!--end of column--bar-->
          
        <div class="column--main column--main--publicar">
          <h1>Publicar nuevo post</h1>
          <h2>Participa en la comunidad y publica una entrada en el blog.</h2>

          <div class="box box--publicar margin-top-18">
            <form action="publicar.php" method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <input type="text" name="titulo" class="form-control input-12" placeholder="Escribe un título atrayente" maxlength="100" required>
              </div>
              <div class="mb-3">
                <textarea name="cuerpo" class="form-control input-12" rows="3" placeholder="Comparte tu conocimiento con la comunidad" required></textarea>
              </div>
              <div class="mb-3">
                <label for="file" class="form-label">Selecciona una imagen</label>
                <input name="imagen" class="form-control" type="file" id="file" required>
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" id="exampleInputPassword1">
                <p class="text-smaller">Si no seleccionas fecha, se usará la fecha de hoy</p>
              </div>
              <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
          </div><!--end of box margin-top-18-->
        </div><!--end of column--main-->
      </div><!--end of flexbox-->
    </div><!--end of container-->
  <?php } // Cierra condicional user logueado

  if ($login == false) { // Si usuario no login ?>
    <div class="container container-custom">
      <div class="p-5 mb-4 bg-light rounded-3 hero-cover" style="background-image:url('imgprueba/img12.jpg');">
        <div class="container-fluid py-5 hero-index hero-publicar">
        <h1 class="text-rare-2"><a href="login.php"><span>Inicia sesión</span></a><br> para publicar un post</h1>
        <a class="btn btn-outline-secondary btn-outline-white" href="blog.php">Ir al blog</a>
      </div><!--end of container-fluid-->
      <div class="hero-trasparent hero-transparent-white"></div>
    </div><!--end of p-5 mb-4-->

    <footer class="py-3 my-4">
      <div class="nav justify-content-center border-bottom pb-3 mb-3"></div>
      <p class="text-center text-muted">&copy; 2023 Neoland, Inc</p>
    </footer>
  <?php } ?>

<?php include('footer.php');?>