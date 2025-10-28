<?php
include 'db.php';

// Filtros
$filtroSQL = "WHERE 1";
$hayFiltro = false;
if (isset($_GET['familia']) && $_GET['familia']!="") { 
    $filtroSQL .= " AND familia_id=".intval($_GET['familia']);
    $hayFiltro = true;
}
if (isset($_GET['periodo']) && $_GET['periodo']!="") { 
    $filtroSQL .= " AND periodo_id=".intval($_GET['periodo']);
    $hayFiltro = true;
}
if (isset($_GET['simbolo']) && $_GET['simbolo']!="") { 
    $filtroSQL .= " AND simbolo LIKE '%". $conexion->real_escape_string($_GET['simbolo']) ."%'";
    $hayFiltro = true;
}
$result = $conexion->query("SELECT * FROM elementos $filtroSQL ORDER BY numero_atomico");

$tabla = [];
$lantanidos = [];
$actinidos = [];

while($row = $result->fetch_assoc()){
    if ($row['periodo_id']==6 && $row['numero_atomico']>=58 && $row['numero_atomico']<=71) $lantanidos[] = $row;
    elseif ($row['periodo_id']==7 && $row['numero_atomico']>=90 && $row['numero_atomico']<=103) $actinidos[] = $row;
    else $tabla[$row['periodo_id']][$row['grupo']] = $row;
}

$familias = $conexion->query("SELECT * FROM familias");
$periodos = $conexion->query("SELECT * FROM periodos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Tabla Periódica</title>
<link rel="stylesheet" href="estilos.css">

<!-- Estilos cuadro flotante -->
<style>
#detalle-flotante {
    display: none;
    position: fixed;
    top: 20px;
    right: 10px;
    width: 320px;
    max-height: 90%;
    background: #ffffff;
    border: 1px solid #ccc;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease, opacity 0.3s ease;
    transform: translateX(100%);
    opacity: 0;
}
#detalle-flotante.mostrar {
    transform: translateX(0);
    opacity: 1;
}
#cerrar-detalle {
    float: right;
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
}
#detalle-flotante ul { padding-left: 0; list-style: none; }
#detalle-flotante li { padding: 6px 0; border-bottom: 1px solid #eee; }
#detalle-flotante li:last-child { border-bottom: none; }

/* Resaltar elemento clickeado */
.elemento.seleccionado { outline: 3px solid #a53bd2ff; border-radius: 6px; }
</style>
</head>
<body>

<div class="filtros">
<form method="GET">
<select name="familia">
<option value="">Todas las familias</option>
<?php while($f=$familias->fetch_assoc()){
    $sel=(isset($_GET['familia']) && $_GET['familia']==$f['id']) ? "selected" : "";
    echo "<option value='{$f['id']}' $sel>{$f['nombre']}</option>";
} ?>
</select>

<select name="periodo">
<option value="">Todos los periodos</option>
<?php while($p=$periodos->fetch_assoc()){
    $sel=(isset($_GET['periodo']) && $_GET['periodo']==$p['id']) ? "selected" : "";
    echo "<option value='{$p['id']}' $sel>Periodo {$p['numero']}</option>";
} ?>
</select>
<input type="text" name="simbolo" placeholder="Símbolo" value="<?= isset($_GET['simbolo'])?htmlspecialchars($_GET['simbolo']):'' ?>">

<button type="submit">Filtrar</button>
</form>
</div>

<div class="container">
<div class="tabla-wrapper">
<div class="tabla">
<?php
// Tabla principal
for($p=1;$p<=7;$p++){
    for($g=1;$g<=18;$g++){
        // Verificamos si hay un elemento para esta celda
        if(isset($tabla[$p][$g])){
            $el = $tabla[$p][$g];

            // Mostramos La y Ac en la tabla principal
            if($el['numero_atomico']==57 || $el['numero_atomico']==89){
                echo '<div class="elemento familia-'.$el['familia_id'].'" data-id="'.$el['id'].'">
                        <span class="numero">'.$el['numero_atomico'].'</span>
                        <span class="simbolo">'.$el['simbolo'].'</span>
                        <span class="nombre">'.$el['nombre'].'</span>
                      </div>';
            } else {
                echo '<div class="elemento familia-'.$el['familia_id'].'" data-id="'.$el['id'].'">
                        <span class="numero">'.$el['numero_atomico'].'</span>
                        <span class="simbolo">'.$el['simbolo'].'</span>
                        <span class="nombre">'.$el['nombre'].'</span>
                      </div>';
            }
        } else {
            // Columna vacía, pero dejamos espacio para La y Ac en la columna 3
            if(($p==6||$p==7)&&$g==3){
                echo '<div class="vacio"></div>';
            } else {
                echo '<div class="vacio"></div>';
            }
        }
    }
}

// Lantánidos (excluyendo La 57)
$rowNum = 12; $colStart = 3;
foreach($lantanidos as $i => $el){
    if($el['numero_atomico']==57) continue; // ya está en tabla principal
    $col = $colStart + ($i % 14);
    echo '<div class="elemento familia-'.$el['familia_id'].'" data-id="'.$el['id'].'" style="grid-row:'.$rowNum.';grid-column:'.$col.';">
            <span class="numero">'.$el['numero_atomico'].'</span>
            <span class="simbolo">'.$el['simbolo'].'</span>
            <span class="nombre">'.$el['nombre'].'</span>
          </div>';
}

// Actínidos (excluyendo Ac 89)
$rowNum = 13;
foreach($actinidos as $i => $el){
    if($el['numero_atomico']==89) continue; // ya está en tabla principal
    $col = $colStart + ($i % 14);
    echo '<div class="elemento familia-'.$el['familia_id'].'" data-id="'.$el['id'].'" style="grid-row:'.$rowNum.';grid-column:'.$col.';">
            <span class="numero">'.$el['numero_atomico'].'</span>
            <span class="simbolo">'.$el['simbolo'].'</span>
            <span class="nombre">'.$el['nombre'].'</span>
          </div>';
}
?>

</div>
</div>
</div>

<!-- Cuadro flotante -->
<div id="detalle-flotante">
    <button id="cerrar-detalle">&times;</button>
    <div id="contenido-detalle"></div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.elemento').click(function(){
        var id = $(this).data('id');

        // Resaltar
        $('.elemento').removeClass('seleccionado');
        $(this).addClass('seleccionado');

        $.ajax({
            url: 'elemento.php',
            method: 'GET',
            data: { id: id, ajax: 1 },
            success: function(data){
                $('#contenido-detalle').html(data);
                $('#detalle-flotante').addClass('mostrar').fadeIn();
            }
        });
    });

    $('#cerrar-detalle').click(function(){
        $('#detalle-flotante').removeClass('mostrar').fadeOut();
        $('.elemento').removeClass('seleccionado');
    });
});
</script>

</body>
</html>
