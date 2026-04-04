<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/vendor/autoload.php";

use Clases\Productos;
use Jaxon\Jaxon;
use Jaxon\Response\Response;


$productos = new Productos();
$lista = $productos->listar();
$jaxon = jaxon();
$jaxon->setOption('core.debug.on', false);
$jaxon->setOption('core.prefix.function', 'jax_');
$jaxon->register(Jaxon::CALLABLE_FUNCTION, 'realizarVoto');

if ($jaxon->canProcessRequest()) {
    $jaxon->processRequest();
}

function crearLista()
{
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
            $tabla .= "<button type='button' class='button is-primary' onclick='this.disabled=true; jax_realizarVoto(" . $fila["id"] . ", document.getElementById(\"select" . $fila["id"] . "\").value); return false; '>Votar</button></td></tr>";
        }
    }
    echo $tabla;
}

function pintarEstrellas($id)
{
    global $productos;
    $elem = $productos->buscarVotos($id);
    $media = $elem["valor"];
    $base = floor($media);
    $html = '';
    for ($i = 0; $i < $base; $i++) {
        $html .= '<span class="icon has-text-warning">
                    <i class="fa-solid fa-star"></i>
                  </span>';
    }
    if ($media - $base >= 0.5)
        $html .= '<span class="icon has-text-warning">
                    <i class="fa-solid fa-star-half"></i>
                  </span>';
    for ($i = $base; $i < 5; $i++) {
        $html .= '<span class="icon has-text-info">
                    <i class="fa-solid fa-star"></i>
                  </span>';
    }
    $html .= "<span class='has-text-grey-light is-size-7'> (" . $elem["cantidad"] . " valoraciones)</span>";
    return $html;
}

function realizarVoto($idProducto, $puntuacion)
{
    global $productos;
    $respuesta = jaxon()->newResponse();
    try {
        if (is_array($productos->votar($idProducto, $puntuacion))) {
            $html = pintarEstrellas($idProducto);
            $respuesta->assign("valoracion" . $idProducto, "innerHTML", $html);
            $respuesta->alert("¡Voto guardado!");
        } else {
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
                <?php crearLista(); ?>
            </tbody>
        </table>
    </div>
</main>
<?php
echo $jaxon->getScript();
include __DIR__ . '/esquema/pie.php';
?>


