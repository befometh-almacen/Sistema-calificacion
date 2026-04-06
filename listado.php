<?php
/**
 * @author Cristyan Fernando Morales Acevedo
 * Página principal de inicio de sesión
 */
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/vendor/autoload.php";

use Clases\Productos;
use Jaxon\Jaxon;
use Jaxon\Response\Response;


$productos = new Productos();
$lista = $productos->listar();
$jaxon = jaxon();
$jaxon->setOption('core.debug.on', false);
$jaxon->setOption('core.prefix.function', 'jax_');              //Se cambia el prefijo de registro de funciones, por defecto jaxon_ a jax_
$jaxon->register(Jaxon::CALLABLE_FUNCTION, 'realizarVoto');     //Se registra la función que va a permitir la asincronía

if ($jaxon->canProcessRequest()) {
    $jaxon->processRequest();
}
/** Función que crea la tabla de productos y la botonera de votación por item
 * @return void
 */
function crearLista()
{
    print("Aqui".$_SESSION["id_usuario"]);
    global $productos;
    $lista = $productos->listar();
    $tabla = "";
    if (sizeof($lista) > 0) {
        foreach ($lista as $fila) {
            $tabla .= "<tr>";
            $tabla .= "<td>" . $fila["id"] . "</td>";
            $tabla .= "<td>" . $fila["nombre"] . "</td>";
            $tabla .= "<td>" . $fila["familia"] . "</td>";
            $tabla .= "<td>" . $fila["pvp"] . "</td>";
            $tabla .= "<td id='valoracion" . $fila["id"] . "'>" . pintarEstrellas($fila["id"]) . "</td>";
            $tabla .= "<td><select class='select' id='select" . $fila["id"] . "' name='select" . $fila["id"] . "'>";
            for ($i = 1; $i <= 5; $i++) {
                $tabla .= "<option value='" . $i . "'>" . $i . "</option>";
            }
            $tabla .= "</select></td><td>";
            //Se crea el botón que va a dar lugar a la solicitud asíncrona, (función con jax_realizarVoto())
            $tabla .= "<button type='button' class='button is-primary' onclick='this.disabled=true; jax_realizarVoto(" . $fila["id"] . ", document.getElementById(\"select" . $fila["id"] . "\").value); return false; '>Votar</button></td></tr>";
        }
    }
    echo $tabla;
}

/** Función que genera las estrellas dentro del HTML
 * @param $id int ID del producto
 * @return string código HTML con las estrellas ya pintadas.
 */
function pintarEstrellas($id)
{
    global $productos;
    $elem = $productos->buscarVotos($id);
    $media = $elem["valor"];
    $base = floor($media);
    $html = '';
    //Pinta las estrellas votadas
    for ($i = 0; $i < $base; $i++) {
        $html .= '<span class="icon has-text-warning">
                    <i class="fa-solid fa-star"></i>
                  </span>';
    }
    //Pinta las medias estrellas si hay un flotante igual o superior a 0.5
    if ($media - $base >= 0.5) {
        $html .= '<span class="icon has-text-warning">
                    <i class="fa-solid fa-star-half"></i>
                  </span>';
        $base += 1; //Como la media estrella ocupa un lugar, se aumenta la base en 1
    }

    //Pinta las estrellas no votadas para rellenar los 5 espacios
    for ($i = $base; $i < 5; $i++) {
        $html .= '<span class="icon has-text-info">
                    <i class="fa-solid fa-star"></i>
                  </span>';
    }
    $html .= "<span class='has-text-grey-light is-size-7'> (" . $elem["cantidad"] . " valoraciones)</span>";
    return $html;
}

/**
 * @param $idProducto int ID de referencia del producto
 * @param $puntuacion int Valor votado por el usuario de 1 a 5
 * @return Response respuesta al código Jaxon para permitir la asincronía
 */
function realizarVoto($idProducto, $puntuacion)
{
    global $productos;
    $respuesta = jaxon()->newResponse();
    try {
        //Si la votación es posible porque el usuario no ha votado, (ver Clases/Usuarios), actualiza la información en tabla y envía una alerta de éxito
        if (is_array($productos->votar($idProducto, $puntuacion,$_SESSION["id_usuario"]))) {
            $html = pintarEstrellas($idProducto);
            $respuesta->assign("valoracion" . $idProducto, "innerHTML", $html);
            $respuesta->alert("¡Voto guardado!");
        } else {
            //Error que sale si ya se ha realizado el voto
            $respuesta->alert("Ya has votado este producto.");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
    return $respuesta;
}

include __DIR__ . '/esquema/cabecera.php';
?>
<header class="hero is-info is-bold">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">Catálogo de Productos</h1>
            <h2 class="subtitle">Valoraciones en tiempo real</h2>
            <h2 class="link"><a href="logout.php">Volver al inicio</a></h2>
        </div>
    </div>
</header>
<main class="section">
    <div class="container">
        <table class="table">
            <thead>
            <th>Cod.</th>
            <th>Nombre</th>
            <th>Familia</th>
            <th>PvP</th>
            <th>Valoración</th>
            <th colspan="2">Acciones</th>
            </thead>
            <tbody>
                <!--Se llama a la función que pinta la tabla-->
                <?php crearLista(); ?>
            </tbody>
        </table>
    </div>
</main>
<?php
echo $jaxon->getScript();
include __DIR__ . '/esquema/pie.php';
?>


