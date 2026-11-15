<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\postgresql\DBTabla;
use src\shared\infrastructure\persistence\DBConnection;

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
            $esquemaTxt = $esquema;
            if ($esquemaTxt === '') {
                continue;
            }
            $esquemaNewv = $esquemaTxt;
            $esquemaRefv = $esquemaTxt;

            /** @var array<string, array<string, mixed>> $aTablas */
            $aTablas = [$tabla => []];
            $oDBTabla = new DBTabla();
            $oDBTabla->setTablas($aTablas);
            $oDBTabla->setRef($esquemaRefv);
            $oDBTabla->setNew($esquemaNewv);

            if ($oDBTabla->mover($configRef, $configNew)) {
                // Quitar la tabla de la BD de origen, con su esquema y sin CASCADE. Antes se llamaba a
                // `DBTabla::eliminarTabla()`, que no tiene conexión (lanzaba una excepción con la tabla ya
                // copiada) y borra por el nombre sin esquema.
                if (preg_match('/^[a-z_][a-z0-9_]*$/', $tabla) !== 1) {
                    $msg .= '<br>' . sprintf(_("Nombre de tabla no válido: %s"), $tabla);
                    continue;
                }
                try {
                    $pdoRef = (new DBConnection($configRef))->getPDO();
                    $pdoRef->exec(sprintf('DROP TABLE IF EXISTS "%s".%s RESTRICT', str_replace('"', '', $esquemaRefv), $tabla));
                } catch (\Throwable $e) {
                    $msg .= '<br>' . sprintf(_("La tabla se ha copiado a sv-e, pero no se ha podido quitar de sv (%s): %s"), $esquema, $e->getMessage());
                }
            } else {
                $msg .= '<br>' . sprintf(_("Error para %s"), $esquema);
            }
        }
        if ($msg !== '') {
            return new MoverTablaResult($msg);
        }

        $lines = [];
        if (str_ends_with((string) ConfigGlobal::SERVIDOR, '.local')) {
            $a_rta = [];
            $command = "grep -r \"setNomTabla('$tabla');\" ";
            $command .= ServerConf::DIR . '/*';
            exec($command, $a_rta);

            foreach ($a_rta as $rta_txt) {
                $a_file = explode(':', $rta_txt);
                $filename = $a_file[0];

                $lines[] = "cambiando $filename<br>";

                $dump = file_get_contents($filename);
                if ($dump === false) {
                    $lines[] = "No se ha podido leer: $filename<br>";
                    continue;
                }
                $count = 0;
                $dump_nou = str_replace('$oDbl = $GLOBALS[\'oDB\'];', '$oDbl = $GLOBALS[\'oDBE\'];', $dump, $count);

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
