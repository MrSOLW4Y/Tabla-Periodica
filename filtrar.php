<?php
include "db.php";

$filtroSQL = "WHERE 1 ";
if (!empty($_GET['familia'])) $filtroSQL .= " AND familia_id = ".intval($_GET['familia']);
if (!empty($_GET['periodo'])) $filtroSQL .= " AND periodo_id = ".intval($_GET['periodo']);
if (!empty($_GET['simbolo'])) {
    $simbolo = $conexion->real_escape_string($_GET['simbolo']);
    $filtroSQL .= " AND simbolo LIKE '%$simbolo%'";
}

$result = $conexion->query("SELECT * FROM elementos $filtroSQL ORDER BY numero_atomico");

$tabla = []; $lantanidos = []; $actinidos = [];
while($row = $result->fetch_assoc()){
    if ($row['periodo_id']==6 && $row['numero_atomico']>=57 && $row['numero_atomico']<=71) $lantanidos[] = $row;
    elseif ($row['periodo_id']==7 && $row['numero_atomico']>=89 && $row['numero_atomico']<=103) $actinidos[] = $row;
    else $tabla[$row['periodo_id']][$row['grupo']] = $row;
}

if ($result->num_rows == 0) exit; // Para que AJAX detecte "sin resultados"
?>

<div class="tabla bg-white p-3 rounded shadow">
<?php
for($p=1;$p<=7;$p++){
    for($g=1;$g<=18;$g++){
        if(($p==6||$p==7)&&$g==3){ echo '<div class="vacio"></div>'; continue; }
        if(isset($tabla[$p][$g])){
            $el=$tabla[$p][$g];
            echo '<div class="elemento familia-'.$el['familia_id'].'">
                    <span class="fw-bold">'.$el['numero_atomico'].'</span><br>
                    <span class="fs-4">'.$el['simbolo'].'</span><br>
                    <span>'.$el['nombre'].'</span>
                  </div>';
        } else echo '<div class="vacio"></div>';
    }
}

// Lantánidos
$colStart = 3;
foreach($lantanidos as $i => $el){
    $col = $colStart + $i;
    echo '<div class="elemento familia-'.$el['familia_id'].'" style="grid-row:9;grid-column:'.$col.';">
            <span class="fw-bold">'.$el['numero_atomico'].'</span><br>
            <span class="fs-4">'.$el['simbolo'].'</span><br>
            <span>'.$el['nombre'].'</span>
          </div>';
}

// Actínidos
foreach($actinidos as $i => $el){
    $col = $colStart + $i;
    echo '<div class="elemento familia-'.$el['familia_id'].'" style="grid-row:10;grid-column:'.$col.';">
            <span class="fw-bold">'.$el['numero_atomico'].'</span><br>
            <span class="fs-4">'.$el['simbolo'].'</span><br>
            <span>'.$el['nombre'].'</span>
          </div>';
}
?>
</div>
