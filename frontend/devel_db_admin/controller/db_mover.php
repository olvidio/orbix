<?php

use frontend\shared\PostRequest;
use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qtabla = (string)filter_input(INPUT_POST, 'tabla');

// copiar definición y datos de sv
// las definiciones de tablas padre ya las tengo: todas las global y public de sv las pongo en sv-e.
// Para todos los esquemas

//Mirar que esquemas tienen la tabla (si pertenece a un módulo no instalado, pueden no tenerla).
$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_mover_esquemas_con_tabla',
    'tabla' => $Qtabla,
]);
$dbProps = is_array($dbProps) ? $dbProps : [];
$a_esquemas = (array)($dbProps['a_esquemas_con_tabla'] ?? []);

print_r($a_esquemas);

$oConfigDB = new src\shared\infrastructure\persistence\ConfigDB('importar');
$configRef = $oConfigDB->getEsquema('publicv');
$configNew = $oConfigDB->getEsquema('publicv-e');

$msg = '';
foreach ($a_esquemas as $esquema) {
    // crear tabla en sv-e y pasar datos
    $esquemaNewv = $esquema;
    $esquemaRefv = $esquema;

    $aTablas = [$Qtabla => $Qtabla];
    $oDBTabla = new src\shared\infrastructure\persistence\postgresql\DBTabla();
    $oDBTabla->setTablas($aTablas);
    $oDBTabla->setRef($esquemaRefv);
    $oDBTabla->setNew($esquemaNewv);

    if ($oDBTabla->mover($configRef, $configNew)) {
        // Eliminar la original
        $oDBTabla->eliminarTabla($Qtabla);
    } else {
        // Si falla no la elimino.
        $msg .= '<br>' . sprintf(_("Error para %s"), $esquema);
    }
}
if (!empty($msg)) {
    exit ($msg);
}

// Cambiar definición de classes.
// Buscar ficheros en que se usa la tabla:
// Esto sólo será útil en desarrollo. El resto ya lo cogerán modificado del git.
if (ConfigGlobal::SERVIDOR === 'orbix.local') {
    $a_rta = [];
    $command = "grep -r \"setNomTabla('$Qtabla');\" ";
    $command .= ServerConf::DIR . "/*";
    exec($command, $a_rta);

    foreach ($a_rta as $rta_txt) {
        $a_file = explode(':', $rta_txt);
        $filename = $a_file[0];

        echo "cambiando $filename<br>";

        $dump = file_get_contents($filename);
        $count = 0;
        $dump_nou = str_replace('$oDbl = $GLOBALS[\'oDB\'];', '$oDbl = $GLOBALS[\'oDBE\'];', $dump, $count);

        if ($count < 1) {
            echo "No se ha modificado: $filename<br>";
        } else {
            $d = file_put_contents($filename, $dump_nou);
            if ($d === false) exit (_("error al escribir el fichero"));
        }
    }
}

// tema sync: bucardo.
echo "<br>FALTA aclararse con la sincronización: bucardo<br>";
// Avisar que hay que cambiar la gestión al instalar el módulo.

echo "<br>Hay que revisar los programas que generan la tabla: instalar módulo<br>";
