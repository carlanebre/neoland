<?php session_start(); ?>

<?php
require_once('config/db.php');
// Verificar si el usuario está logueado
if (isset($_SESSION['token'])){
  $login=true;
  $consulta="select * from hito.users where id_user = ?";
  $stmt = $conn->prepare($consulta);
  $stmt->bindParam(1, $_SESSION['token']);
  $stmt->execute();

  // Almacenar los datos del usuario logueado en variables
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
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/matchHeight.js" type="text/javascript"></script>
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

  <?php if ($login == true) { // Si usuario no login ?>
    <div class="container container-custom px-4 py-5" id="custom-cards">
      <div class="pb-2 border-bottom blog-main-title">
        <h2>Mis posts</h2>
        <a href="publicar.php" class="btn-link"><button type="button" class="btn btn-outline-primary me-2 btn-blog">Publicar post</button></a>
      </div>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 margin-top-32">
      
      <?php
        $display_user_posts='select * from hito.posts where id_user = ? ORDER BY fecha DESC';
        $stmt = $conn->prepare($display_user_posts);
        $stmt->bindParam(1, $_SESSION['token']);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
          // El usuario no ha publicado ningún post
          echo '<div class="text-rare">Todavía no has publicado nada, ¿a qué esperas?</div>';
        } else {
          // El usuario ha publicado al menos un post
          // Mostrar los posts del usuario
          while($row_post=$stmt->fetch()){

            // Formateo de la fecha
            $fecha = date("m-d-Y", strtotime($row_post['fecha']));

            // Recoger imágenes
            $imagen=$row_post['imagen']; 
            $imagen_string=stream_get_contents($imagen); 
            $imagen_codificada=base64_encode($imagen_string); 
            $url_imagen='data:image/jpg;base64,'.$imagen_codificada;

            ?>
            <div class="col col-admin">
              <div class="card shadow-sm">
                <div class="bg-cover card-img-top" width="100%" height="225" style="background-image: url(<?php echo $url_imagen; ?>); height:225px;">
                </div>
                <div class="card-body">
                  <p class="card-text card-text-title"><?php echo $row_post['titulo']; ?></p>

                  <div class="text-group">
                  <small class="text-muted">Autor: <?php echo $row_post['username']; ?></small>
                    <small class="text-muted">Fecha: <?php echo $fecha; ?></small>
                  </div>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-secondary">Ver</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary">Editar</button>
                      <a href="delete.php?id=<?php echo $row_post['id_post']; ?>" class="btn btn-sm btn-outline-danger">Eliminar</a>
                    </div>
                  </div>
                </div><!--end of card-body-->
              </div><!--end of card-->
            </div><!--end of col-->
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </div><!--end of container-->
  

  <?php if ($login == false) { // Si usuario no login ?>
    <div class="container container-custom">
      <div class="p-5 mb-4 bg-light rounded-3 hero-cover" style="background-image:url('imgprueba/img12.jpg');">
        <div class="container-fluid py-5 hero-index hero-publicar">
        <h1 class="text-rare-2"><a href="login.php"><span>Inicia sesión</span></a><br> para administrar tus publicaciones</h1>
        <a class="btn btn-outline-secondary btn-outline-white" href="blog.php">Ir al blog</a>
      </div><!--end of container-fluid-->
      <div class="hero-trasparent hero-transparent-white"></div>
    </div><!--end of p-5 mb-4-->
  <?php } ?>

<footer class="py-3 my-4">
  <div class="nav justify-content-center border-bottom pb-3 mb-3"></div>
  <p class="text-center text-muted">&copy; 2023 Neoland, Inc</p>
</footer>

<script>
  $(function() {
	$('.card-text').matchHeight();
  });
</script>

<?php include('footer.php');?>