<?php
require_once __DIR__ . "/vendor/autoload.php";
use Clases\Usuarios;

session_start();
// Si ya está logueado, lo mandamos al listado
if (isset($_SESSION['id_usuario'])) {
    header("Location: listado.php");
    exit();
}
//Contraseñas de prueba
    /* echo "admin ->".password_hash("admin", PASSWORD_DEFAULT)."<br>";
    *  echo "user1 ->".password_hash(1234, PASSWORD_DEFAULT)."<br>";
    *  echo "user2 ->".password_hash("abcd", PASSWORD_DEFAULT)."<br>";
    *  echo "user3 ->".password_hash(0000, PASSWORD_DEFAULT);
    */

//Si la tabla ingresa por "POST" comprueba la contraseña, si es correcta graba la sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = new Usuarios();
    $registro = $usuario -> logIn($_POST["usuario"], $_POST["password"]);

    //Si el registro arroja un código de usuario, anclar a la sesión y dirigirse a la tabla listado.php
    if ($registro) {
        $_SESSION['id_usuario'] = $registro;
        header("Location: listado.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos."; //Valor si contraseña es incorrecta
    }
}
include __DIR__ . "/esquema/cabecera.php";
?>
<section class="section">
    <div class="container" id="containerLogin">
        <h1 class="title">Iniciar Sesión</h1>
        <form method="POST">
            <div class="field">
                <label class="label">Usuario</label>
                <input class="input" type="text" name="usuario" required>
            </div>
            <div class="field">
                <label class="label">Contraseña</label>
                <input class="input" type="password" name="password" required>
            </div>
            <?php if(isset($error)): ?>
                <p class="help is-danger"><?php echo $error; ?></p>
            <?php endif; ?>
            <button class="button is-link is-fullwidth">Entrar</button>
        </form>
    </div>
</section>
<?php include __DIR__ . "/esquema/pie.php"; ?>