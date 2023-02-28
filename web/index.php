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
}
else {
  $login=false;
}
// Almacenar cookie
// Obtener la dirección IP del equipo que está accediendo
$ip = $_SERVER['REMOTE_ADDR'];
// Obtener la fecha actual
$fecha = date('Y-m-d H:i:s');
// Crear la cookie con la información de la IP y la fecha
setcookie('ip', $ip);
setcookie('fecha_acceso', $fecha);
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
  <div class="p-5 mb-4 bg-light rounded-3 hero-cover" style="background-image: url('./img/blue.jpg');">
    <div class="container-fluid py-5 hero-index">
      <h1 class="display-5 fw-bold">Bienvenido a Neoland</h1>
      <p class="col-md-8 fs-4">Un blog de programación donde encontrarás todo lo que necesitas para mantenerte actualizado en el mundo de la tecnología y el desarrollo.</p>
      <a href="blog.php"><button class="btn btn-primary btn-lg" type="button">Entra al blog</button></a>
    </div><!--end of container-fluid-->
    <div class="hero-trasparent"></div>
  </div><!--end of p-5 mb-4-->

  <div class="progress" role="progressbar" aria-label="Example 1px high" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 10px">
    <div class="progress-bar" id="progress" style="width: 0%"></div>
  </div><!--end of progress-->

  <div class="flexbox margin-top-50">
    <div class="column--bar">
        <div class="bar">
            <h3 class="bar__title">Imperativa</h3>
            <ul class="bar__list">
                <li class="bar__list__item"><a href="#" class="bar__list__link">Programación estructurada</a></li>
                <li class="bar__list__item"><a href="#" class="bar__list__link">Programación procedimental</a></li>
                <li class="bar__list__item"><a href="#" class="bar__list__link">Programación modular</a></li>
                <li class="bar__list__item"><a href="#" class="bar__list__link">POO</a></li>
            </ul><!--end of bar__list-->

            <h3 class="bar__title">Declarativa</h3>
            <ul class="bar__list">
                <li class="bar__list__item"><a href="#" class="bar__list__link">Programación Lógica</a></li>
                <li class="bar__list__item"><a href="#" class="bar__list__link">Programación Funcional</a></li>
                <li class="bar__list__item"><a href="#" class="bar__list__link">Reactiva</a></li>
            </ul><!--end of bar__list-->
        </div><!--end of bar-->
      </div><!--end of column--bar-->
      
      <div class="column--main index-blog">
        <h1 class="title--big">Paradigmas de programación</h1>
        <h1>Diferencias entre diferentes enfoques</h1>
        <br>
        <p>Se denominan paradigmas de programación a las formas de clasificar los <span class="text-bold">lenguajes de programación</span> en función de sus características.</p>

        <h2>Lenguajes de programación orientada a objetos</h2>
        <p>La programación orientada a objetos (Object Oriented Programming, OOP) es un modelo de programación informática que organiza el diseño de software en torno a datos u objetos, en lugar de funciones y lógica. Un <span class="text-bold">objeto</span> se puede definir como un campo de datos que tiene atributos y comportamiento únicos.</p>

        <p>La programación orientada a objetos se centra en los objetos que los desarrolladores quieren manipular en lugar de enfocarse en la lógica necesaria para manipularlos. Este enfoque de programación es adecuado para programas que son grandes, complejos y se actualizan o mantienen activamente.</p>

        <p>Los beneficios adicionales de la programación orientada a objetos incluyen la reutilización, la escalabilidad y la eficiencia del código.</p>

        <h3>Principios de OOP</h3>

        <ul>
          <li><u>Encapsulamiento</u>: La implementación y el estado de cada objeto se mantienen de forma privada dentro de un límite definido o clase. Otros objetos no tienen acceso a esta clase o la autoridad para realizar cambios, pero pueden llamar a una lista de funciones o métodos públicos. Esta característica de ocultación de datos proporciona una mayor seguridad al programa y evita la corrupción de datos no intencionada.</li>
          <li><u>Abstracción</u>: Los objetos solo revelan mecanismos internos que son relevantes para el uso de otros objetos, ocultando cualquier código de implementación innecesario. Este concepto ayuda a los desarrolladores a realizar cambios y adiciones más fácilmente a lo largo del tiempo.</li>
          <li><u>Herencia</u>: Se pueden asignar relaciones y subclases entre objetos, lo que permite a los desarrolladores reutilizar una lógica común sin dejar de mantener una jerarquía única. Esta propiedad de OOP obliga a un análisis de datos más completo, reduce el tiempo de desarrollo y asegura un mayor nivel de precisión.</li>
        </ul>

        <h2>Lenguajes de programación orientada a eventos</h2>

        <p>La programación orientada a eventos es un paradigma de programación en el que tanto la estructura como la ejecución de los programas van determinados por los sucesos que ocurran en el sistema, definidos por el usuario o que ellos mismos provoquen. Es un paradigma de programación que se basa en la emisión y recepción de eventos.</p>

        <p>La programación orientada a eventos es muy fácil de usar y es adecuada para aquellas personas que tienen poco conocimiento en programación. Con los lenguajes orientados a eventos se pueden realizar en poco tiempo aplicaciones sencillas y muy funcionales, utilizando interfaces gráficas en las que se insertan componentes o controles a los que se le programan eventos. Dichos eventos permiten al usuario realizar una serie de acciones lógicas para un determinado programa.</p>

        <h2>Lenguajes procedimentales</h2>

        <p>El paradigma de programación procedimental, también llamado programación modular o programación funcional, es un modelo de programación que utiliza el modelo de programación estructurada y se basa en dividir las tareas que debe hacer un programa en partes más pequeñas. Estas partes en las que se divide el trabajo a realizar se llaman procedimientos aunque también se les conocen como métodos, funciones, subrutinas, etc.</p>

        <p>Un procedimiento (un método en lenguaje Java) contiene una serie de instrucciones que realizan una tarea muy concreta.</p>
      </div><!--end of column--main-->
  </div><!--end of flexbox-->

  <footer class="py-3 my-4">
    <div class="nav justify-content-center border-bottom pb-3 mb-3"></div>
    <p class="text-center text-muted">&copy; 2023 Neoland, Inc</p>
  </footer>
</div><!--end of container-->

<script src="js/jquery-1.12.4.min.js"></script>
<?php include('footer.php');?>