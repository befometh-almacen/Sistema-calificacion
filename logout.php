<?php
/**
 * @author Cristyan Fernando Morales Acevedo
 * Página de logout
 */
// 1. Iniciamos para poder destruir
session_start();

// 2. Limpiamos todas las variables de sesión
$_SESSION = array();

// 3. Borramos la cookie de sesión del navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruimos la sesión en el servidor
session_destroy();

// Nota: No redirigimos con header() porque queremos mostrar la interfaz de abajo
include __DIR__ . "/esquema/cabecera.php";
?>
<section class="hero is-info is-fullheight">
    <div class="hero-body">
        <div class="container has-text-centered">
            <div class="column is-4 is-offset-4">
                <div class="box">
                        <span class="icon is-large has-text-success">
                            <svg style="width:64px;height:64px" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L7.5,13L8.91,11.59L11,13.67L15.09,9.59L16.5,11L11,16.5Z" />
                            </svg>
                        </span>

                    <h1 class="title has-text-grey-darker mt-4">Sesión Finalizada</h1>
                    <p class="subtitle has-text-grey">Has salido del sistema de votación de forma segura.</p>

                    <hr>

                    <p class="is-size-7 mb-4">Serás redirigido automáticamente en unos segundos...</p>

                    <a href="index.php" class="button is-link is-large is-fullwidth">
                        Ir al Inicio ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . "/esquema/pie.php";?>