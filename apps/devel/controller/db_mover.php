<?php
use core\ConfigGlobal;
use core\DBPropiedades;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtabla = (string) \filter_input(INPUT_POST, 'tabla');

// copiar definicion y datos de sv
// las definiciones de tablas padre ya las tengo: todas las global y public de sv las pongo en vs-e.
// Para todos los esquemas

//Mirar que esquemas tienen la tabla (si pertenece a un módulo no instalado, pueden no tenerla).
$oDBPropiedades = new DBPropiedades();
$a_esquemas = $oDBPropiedades->array_esquemas_con_tabla($Qtabla);

print_r($a_esquemas);

$oConfigDB = new core\ConfigDB('importar');
$configRef = $oConfigDB->getEsquema('publicv');
$configNew = $oConfigDB->getEsquema('publicv-e');
    
$msg = '';
foreach ($a_esquemas as $esquema) {
    // crear tabla en sv-e y pasar datos
    $esquemaNewv = $esquema;
    $esquemaRefv = $esquema;

    $aTablas = [ $Qtabla => $Qtabla ];
    $oDBTabla = new core\DBTabla();
    $oDBTabla->setTablas($aTablas);
    $oDBTabla->setRef($esquemaRefv);
    $oDBTabla->setNew($esquemaNewv);

    if ( $oDBTabla->mover($configRef,$configNew) ) {
        // Eliminar la original
        $oDBTabla->eliminarTabla($Qtabla);
    } else {
        // Si falla no la elimino.
        $msg .= '<br>'.sprintf(_("Error para %s"),$esquema);
    }
}
if (!empty($msg)) {
    exit ($msg);
}

// Cambiar definicion de classes.
// Buscar ficheros en que se usa la tabla:
// Esto sólo será útil en desarrollo. El resto ya lo cogeran modificado del git.
if (ConfigGlobal::SERVIDOR == 'orbix.local') {
    $a_rta = [];
    $command = "grep -r \"setNomTabla('$Qtabla');\" ";
    $command .= ConfigGlobal::DIR ."/*";
    exec($command, $a_rta);

    foreach ($a_rta as $rta_txt) {
        $a_file = explode(':', $rta_txt);
        $filename = $a_file[0];
        
        echo "cambiando $filename<br>";
        
        $dump = file_get_contents($filename);
        $count = 0;
        $dump_nou = str_replace('$oDbl = $GLOBALS[\'oDB\'];','$oDbl = $GLOBALS[\'oDBE\'];',$dump,$count);
        
        if ($count < 1) {
            echo "No se ha modificado: $filename<br>";
        } else {
            $d = file_put_contents($filename, $dump_nou);
            if ($d === false) exit (_("error al escribir el fichero"));
        }
    }
}

// tema sync: bucardo.
echo "<br>FALTA aclararse con la syncronización: bucardo<br>";
// Avisar que hay que cambiar la gestión al instalar el módulo.

echo "<br>Hay que revisar los programas que generan la tabla: instalar módulo<br>";
