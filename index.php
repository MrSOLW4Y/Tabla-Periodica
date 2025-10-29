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

            <style>
            #detalle-flotante {
                font-family: Arial, Helvetica, sans-serif;
                position: fixed;
                top: 20px;
                right: 0px;
                width: 20%  ;
                height: 100%;
                background: #ffffffff;
                border-radius: 1%;
                opacity: 50;
                text-align: center;

            }   
            .img-elemento {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }

        .familia-off {
            filter: grayscale(15%) brightness(0.6);
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .familia-on {   
              filter: none;
  opacity: 1;
  transform: scale(1.05);
  transition: all 0.3s ease;
  z-index: 2;   
  border-color: #0056b3;
        }
        .img-elemento {
            width: 80%;
            height: 80%;
            object-fit: contain;
            display: block;
            margin: 0 auto 4px auto;
        }
        

            #detalle-flotante ul { padding-left: 0; list-style: none; }
            #detalle-flotante li { padding: 6px 0; border-bottom: 1px solid #eee; }
            #detalle-flotante li:last-child { border-bottom: none; }
            .elemento.seleccionado { outline: 3px solid #727272ff; border-radius: 6px; }
            .filtros {
  background: #f9fafc;
  border-radius: 10px;
  padding: 12px 18px;
  max-width: 750px;
  margin: 10px auto 20px auto;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.filtros form {
  display: flex ;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: left      ;
  align-items: center;
}

.filtros select,
.filtros input[type="text"] {
  padding: 8px 10px;
  font-size: 0.9rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  min-width: 150px;
  transition: 0.2s ease;
}

.filtros select:focus,
.filtros input[type="text"]:focus {
  border-color: #007bff;
  box-shadow: 0 0 4px #007bff50;
  outline: none;
}

.filtros button {
  padding: 9px 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.1s ease;
}

.filtros button:hover {
  background: #0056b3;
  transform: translateY(-1px);
}
.elemento {
  position: relative;
  cursor: pointer;
}

/* Tooltip general */
.elemento::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 110%;
  left: 50%;
  transform: translateX(-50%) translateY(5px);
  background: rgba(0, 0, 0, 0.8);
  color: #fff;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 0.8rem;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease, transform 0.2s ease;
  z-index: 999;
}

/* Flechita del tooltip */
.elemento::before {
  content: "";
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  border-width: 5px;
  border-style: solid;
  border-color: rgba(0, 0, 0, 0.8) transparent transparent transparent;
  opacity: 0;
  transition: opacity 0.2s ease;
}

/* Mostrar al pasar el mouse */
.elemento:hover::after,
.elemento:hover::before {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
    .familia-1::after { background: #390c87ff; }
.familia-2::after { background: #74b9ff; }
.familia-3::after { background: #eeee03ff; }


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

    <input type="text" name="simbolo" placeholder="Símbolo" 
           value="<?= isset($_GET['simbolo'])?htmlspecialchars($_GET['simbolo']):'' ?>">

    <button type="submit">🔍 Filtrar</button>
  </form>
</div>


    <div class="container">
    <div class="tabla-wrapper">
    <div class="tabla">
    <?php
    for($p=1;$p<=7;$p++){
        for($g=1;$g<=18;$g++){
            if(isset($tabla[$p][$g])){
                $el = $tabla[$p][$g];

                if($el['numero_atomico']==57 || $el['numero_atomico']==89){
                    echo '<div class="elemento familia-'.$el['familia_id'].'" 
           data-id="'.$el['id'].'"
           data-periodo="'.$el['periodo_id'].'"
           data-tooltip="N° '.$el['numero_atomico'].' | '.$el['simbolo'].' - '.$el['nombre'].'">
        <span class="numero">'.$el['numero_atomico'].'</span>
        <span class="simbolo">'.$el['simbolo'].'</span>
        <span class="nombre">'.$el['nombre'].'</span>
      </div>';

                } else {
                          echo '<div class="elemento familia-'.$el['familia_id'].'" 
           data-id="'.$el['id'].'"
           data-periodo="'.$el['periodo_id'].'"
           data-tooltip="N° '.$el['numero_atomico'].' | '.$el['simbolo'].' - '.$el['nombre'].'">
        <span class="numero">'.$el['numero_atomico'].'</span>
        <span class="simbolo">'.$el['simbolo'].'</span>
        <span class="nombre">'.$el['nombre'].'</span>
      </div>';  
                }
            } else {
                if(($p==6||$p==7)&&$g==3){
                    echo '<div class="vacio"></div>';
                } else {
                    echo '<div class="vacio"></div>';
                }
            }
        }
    }

    $rowNum = 12; $colStart = 3;
    foreach($lantanidos as $i => $el){
        if($el['numero_atomico']==57) continue;
        $col = $colStart + ($i % 14);
       echo '<div class="elemento familia-'.$el['familia_id'].'" 
           data-id="'.$el['id'].'"
           data-periodo="'.$el['periodo_id'].'"
           data-tooltip="N° '.$el['numero_atomico'].' | '.$el['simbolo'].' - '.$el['nombre'].'" style="grid-row:'.$rowNum.';grid-column:'.$col.';"  >
        <span class="numero">'.$el['numero_atomico'].'</span>
        <span class="simbolo">'.$el['simbolo'].'</span>
        <span class="nombre">'.$el['nombre'].'</span>
      </div>';  
    }

    $rowNum = 13;
    foreach($actinidos as $i => $el){
        if($el['numero_atomico']==89) continue; 
        $col = $colStart + ($i % 14);
        
       echo '<div class="elemento familia-'.$el['familia_id'].'" 
           data-id="'.$el['id'].'"
           data-periodo="'.$el['periodo_id'].'"
           data-tooltip="N° '.$el['numero_atomico'].' | '.$el['simbolo'].' - '.$el['nombre'].'" style="grid-row:'.$rowNum.';grid-column:'.$col.';"  >
        
                <span class="numero">'.$el['numero_atomico'].'</span>
                <span class="simbolo">'.$el['simbolo'].'</span>
                <span class="nombre">'.$el['nombre'].'</span>
            </div>';
    }
    ?>

    </div>
    </div>
    </div>
    <div id="detalle-flotante">
                
        <div id="contenido-detalle"></div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        $('.elemento').click(function(){
            var id = $(this).data('id');

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
    let familiaSeleccionada = null;

document.querySelectorAll('.elemento').forEach(el => {
    el.addEventListener('click', () => {

        const familia = [...el.classList].find(c => c.startsWith('familia-'));

        if (familiaSeleccionada === familia) {
            resetFamilias();
            familiaSeleccionada = null;
            return;
        }

        familiaSeleccionada = familia;

        actualizarFamilias(familiaSeleccionada);
    });
});

function actualizarFamilias(familia) {
    document.querySelectorAll('.elemento').forEach(e => {
        e.classList.remove('familia-on', 'familia-off');
    });

    document.querySelectorAll('.' + familia).forEach(e =>
        e.classList.add('familia-on')
    );

    document.querySelectorAll('.elemento:not(.' + familia + ')').forEach(e =>
        e.classList.add('familia-off')
    );
}

function resetFamilias() {
    document.querySelectorAll('.elemento').forEach(e => {
        e.classList.remove('familia-on', 'familia-off');
    });
}
    </script>
    <script>
$(document).ready(function() {
  const familiaSelect = document.querySelector('select[name="familia"]');
  const periodoSelect = document.querySelector('select[name="periodo"]');
  const simboloInput = document.querySelector('input[name="simbolo"]');
  const elementos = document.querySelectorAll('.elemento');

  // Extraemos la familia y periodo de cada elemento desde las clases o atributos
  elementos.forEach(el => {
    if (!el.dataset.familia) {
      const familia = [...el.classList].find(c => c.startsWith('familia-'))?.split('-')[1];
      el.dataset.familia = familia;
    }
  });

  function aplicarFiltroVisual() {
    const familiaSel = familiaSelect.value;
    const periodoSel = periodoSelect.value;
    const simboloSel = simboloInput.value.trim().toLowerCase();

    elementos.forEach(el => {
      const elFamilia = el.dataset.familia;
      const elPeriodo = el.getAttribute('style')?.match(/grid-row:(\d+)/)?.[1] || el.dataset.periodo || '';
      const elSimbolo = el.querySelector('.simbolo').textContent.toLowerCase();

      const coincideFamilia = !familiaSel || elFamilia === familiaSel;
      const coincidePeriodo = !periodoSel || elPeriodo === periodoSel;
      const coincideSimbolo = !simboloSel || elSimbolo.includes(simboloSel);

      if (coincideFamilia && coincidePeriodo && coincideSimbolo) {
        el.classList.remove('familia-off');
        el.classList.add('familia-on');
      } else {
        el.classList.remove('familia-on');
        el.classList.add('familia-off');
      }
    });

    // Si no hay filtros → todo normal
    if (!familiaSel && !periodoSel && !simboloSel) {
      elementos.forEach(e => e.classList.remove('familia-on', 'familia-off'));
    }
  }

  // Ejecutar cuando cambien los filtros
  familiaSelect.addEventListener('change', aplicarFiltroVisual);
  periodoSelect.addEventListener('change', aplicarFiltroVisual);
  simboloInput.addEventListener('input', aplicarFiltroVisual);

  // Ejecutar una vez al cargar
  aplicarFiltroVisual();
});
</script>


    </body>
    </html>
