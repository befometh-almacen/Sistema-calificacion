<?php
require_once __DIR__ . "/vendor/autoload.php";
use Clases\Usuarios;

if(session_status()== PHP_SESSION_NONE)session_start();
// Si ya está logueado, lo mandamos al listado
if (isset($_SESSION['id_usuario'])) {
    header("Location: listado.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = new Usuarios();
    $registro = $usuario -> logIn($_POST["usuario"], $_POST["password"]);
    //Debug
    //print_r($registro?"si":"no");

    if ($registro) {
        $_SESSION['id_usuario'] = $registro["id"];
        header("Location: listado.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
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