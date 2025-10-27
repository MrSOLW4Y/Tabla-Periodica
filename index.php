<?php
include "db.php";

// Filtros
$filtroSQL = "WHERE 1";
if (isset($_GET['familia']) && $_GET['familia']!="") $filtroSQL .= " AND familia_id=".intval($_GET['familia']);
if (isset($_GET['periodo']) && $_GET['periodo']!="") $filtroSQL .= " AND periodo_id=".intval($_GET['periodo']);
if (isset($_GET['grupo']) && $_GET['grupo']!="") $filtroSQL .= " AND grupo=".intval($_GET['grupo']);
if (isset($_GET['simbolo']) && $_GET['simbolo']!="") $filtroSQL .= " AND simbolo LIKE '%". $conexion->real_escape_string($_GET['simbolo']) ."%'" ;

$result = $conexion->query("SELECT * FROM elementos $filtroSQL ORDER BY numero_atomico");

$tabla = [];
$lantanidos = [];
$actinidos = [];

while($row = $result->fetch_assoc()){
    if ($row['periodo_id']==6 && $row['numero_atomico']>=57 && $row['numero_atomico']<=71) $lantanidos[] = $row;
    elseif ($row['periodo_id']==7 && $row['numero_atomico']>=89 && $row['numero_atomico']<=103) $actinidos[] = $row;
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
        if(($p==6||$p==7)&&$g==3){ echo '<div class="vacio"></div>'; continue; }
        if(isset($tabla[$p][$g])){
            $el=$tabla[$p][$g];
            echo '<div class="elemento familia-'.$el['familia_id'].'">
                    <span class="numero">'.$el['numero_atomico'].'</span>
                    <span class="simbolo">'.$el['simbolo'].'</span>
                    <span class="nombre">'.$el['nombre'].'</span>
                  </div>';
        } else echo '<div class="vacio"></div>';
    }
}

// Lantánidos
$rowNum = 9;
$colStart = 3;
foreach($lantanidos as $i => $el){
    $col = $colStart + ($i % 15);
    $fila = ($i < 15) ? $rowNum : 6;
    echo '<div class="elemento familia-'.$el['familia_id'].'" style="grid-row:'.$fila.';grid-column:'.$col.';">
            <span class="numero">'.$el['numero_atomico'].'</span>
            <span class="simbolo">'.$el['simbolo'].'</span>
            <span class="nombre">'.$el['nombre'].'</span>
          </div>';
}

// Actínidos
$rowNum = 10;
foreach($actinidos as $i => $el){
    $col = $colStart + ($i % 15);
    $fila = ($i < 15) ? $rowNum : 7;
    echo '<div class="elemento familia-'.$el['familia_id'].'" style="grid-row:'.$fila.';grid-column:'.$col.';">
            <span class="numero">'.$el['numero_atomico'].'</span>
            <span class="simbolo">'.$el['simbolo'].'</span>
            <span class="nombre">'.$el['nombre'].'</span>
          </div>';
}
?>
</div>
</div>
</div>
</body>
</html>
