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
    $username = $row['username'];
    $email = $row['email'];
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

<div class="container px-4 py-5" id="custom-cards">
  <div class="pb-2 border-bottom blog-main-title">
    <h2>Entradas del blog</h2>
    <a href="publicar.php" class="btn-link"><button type="button" class="btn btn-outline-primary me-2 btn-blog">Publicar nueva entrada</button></a>
  </div>

  <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-5">
   
  <?php
    $display_posts='SELECT HITO.POSTS.*, HITO.USERS.USER_IMAGE
                    FROM HITO.POSTS
                    INNER JOIN HITO.USERS ON HITO.POSTS.ID_USER = HITO.USERS.ID_USER
                    ORDER BY FECHA DESC';
    $stmt = $conn->prepare($display_posts);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
      // El usuario no ha publicado ningún post
      echo '<div class="text-rare-3">Todavía no has publicado nada, ¿a qué esperas?</div>';
    } else {
      // El usuario ha publicado al menos un post
      // Mostrar los posts del usuario 
    

    while($row_post=$stmt->fetch()){

    // Formateo de la fecha
    $fecha = date("m-d-Y", strtotime($row_post['fecha']));

    // Recoger imágenes de post
      $imagen=$row_post['imagen']; 
      $imagen_string=stream_get_contents($imagen); 
      $imagen_codificada=base64_encode($imagen_string); 
      $url_imagen='data:image/jpg;base64,'.$imagen_codificada;

    // Recoger imágenes de perfil
    if (!empty($row_post['user_image'])) {
      $p_pfp=$row_post['user_image'];
      $p_pfp_string=stream_get_contents($p_pfp); 
      $p_pfp_codificada=base64_encode($p_pfp_string); 
      $p_url_pfp='data:image/jpg;base64,'.$p_pfp_codificada;
    } else {
      $p_url_pfp='img/default_pfp.png';
    }
    
    // Pintar los posts desde la base de datos en bucle
    ?>
    <div class='col col-custom'>
      <a href='#0' class='card-link'>
        <div class='card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg' style='background-image:url("<?php echo $url_imagen;?>");'>
          <div class='d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1 card-index'>
            <h3 class='pt-5 mt-5 mb-4 display-6 lh-1 fw-bold'><?php echo $row_post['titulo'];?></h3>
            <ul class='d-flex list-unstyled mt-auto'>
              <li class='me-auto'>
                <div style='background-image:url("<?php echo $p_url_pfp; ?>");' class='rounded-circle border border-white user_picture'></div>
              </li>
              <li class='d-flex align-items-center me-3'>
                <svg class='bi me-2' width='1em' height='1em'><use xlink:href='#geo-fill'/></svg>
                <small><?php echo $fecha;?></small>
              </li>
              <li class='d-flex align-items-center'>
                <svg class='bi me-2' width='1em' height='1em'><use xlink:href='#calendar3'/></svg>
                <small><?php echo $row_post['username'];?></small>
              </li>
            </ul>
          </div>
          <div class='cover-trasparent'></div>
        </div><!--end of card-->
    </a><!--end of card-link-->
    </div><!--end of col-->
  <?php
  }}
  ?>
  </div><!--end of row row-cols-1-->
  <footer class="py-3 my-4">
    <div class="nav justify-content-center border-bottom pb-3 mb-3"></div>
    <p class="text-center text-muted">&copy; 2023 Neoland, Inc</p>
  </footer>
</div><!--end of container-->

<script>
  $(function() {
	$('.card-text').matchHeight();
  });
</script>

<?php include('footer.php');?>