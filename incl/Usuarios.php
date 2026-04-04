<?php
session_start();
// Si ya está logueado, lo mandamos al listado
if (isset($_SESSION['id_usuario'])) {
    header("Location: listado.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aquí deberías incluir tu conexión PDO
    // $pdo = new PDO(...);

    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE usuario = ?");
    $stmt->execute([$user]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($pass, $usuario['password'])) {
        $_SESSION['id_usuario'] = $usuario['id'];
        $_SESSION['nombre_usuario'] = $user;
        header("Location: listado.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login de Votaciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
<section class="section">
    <div class="container" style="max-width: 400px;">
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
</body>
</html>