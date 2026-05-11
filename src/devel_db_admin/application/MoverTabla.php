<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\postgresql\DBTabla;

/**
 * Mueve una tabla de sv a sv-e por esquema y, en desarrollo local, ajusta referencias a oDBE en PHP.
 */
final class MoverTabla
{
    /**
     * @param list<string>|array<int|string, string> $aEsquemas
     */
    public function ejecutar(string $tabla, array $aEsquemas): MoverTablaResult
    {
        $oConfigDB = new ConfigDB('importar');
        $configRef = $oConfigDB->getEsquema('publicv');
        $configNew = $oConfigDB->getEsquema('publicv-e');

        $msg = '';
        foreach ($aEsquemas as $esquema) {
            $esquemaNewv = $esquema;
            $esquemaRefv = $esquema;

            $aTablas = [$tabla => $tabla];
            $oDBTabla = new DBTabla();
            $oDBTabla->setTablas($aTablas);
            $oDBTabla->setRef($esquemaRefv);
            $oDBTabla->setNew($esquemaNewv);

            if ($oDBTabla->mover($configRef, $configNew)) {
                $oDBTabla->eliminarTabla($tabla);
            } else {
                $msg .= '<br>' . sprintf(_("Error para %s"), $esquema);
            }
        }
        if ($msg !== '') {
            return new MoverTablaResult($msg);
        }

        $lines = [];
        if (ConfigGlobal::SERVIDOR === 'orbix.local') {
            $a_rta = [];
            $command = "grep -r \"setNomTabla('$tabla');\" ";
            $command .= ServerConf::DIR . '/*';
            exec($command, $a_rta);

            foreach ($a_rta as $rta_txt) {
                $a_file = explode(':', $rta_txt);
                $filename = $a_file[0];

                $lines[] = "cambiando $filename<br>";

                $dump = file_get_contents($filename);
                $count = 0;
                $dump_nou = str_replace('$oDbl = $GLOBALS[\'oDB\'];', '$oDbl = $GLOBALS[\'oDBE\'];', $dump ?? '', $count);

                if ($count < 1) {
                    $lines[] = "No se ha modificado: $filename<br>";
                } else {
                    $d = file_put_contents($filename, $dump_nou);
                    if ($d === false) {
                        return new MoverTablaResult(_("error al escribir el fichero"));
                    }
                }
            }
        }

        $lines[] = "<br>FALTA aclararse con la sincronización: bucardo<br>";
        $lines[] = "<br>Hay que revisar los programas que generan la tabla: instalar módulo<br>";

        return new MoverTablaResult('', $lines);
    }
}
