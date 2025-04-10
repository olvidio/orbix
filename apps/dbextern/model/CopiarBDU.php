<?php

namespace dbextern\model;

use core\ConfigGlobal;
use DateTimeInterface;
use web\DateTimeLocal;

class CopiarBDU
{
    private string $oDbU;
    private string $tabla_bdu;

    private string $oDbl;

    function __construct()
    {
        if (!empty($GLOBALS['oDBListas']) && $GLOBALS['oDBListas'] === 'error') {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $this->oDbU = $GLOBALS['oDBListas'];
        $this->oDbl = $GLOBALS['oDBC'];

        $this->tabla_bdu = 'dbo.q_Aux_Dl';
    }

    public function crearTablaTmp()
    {
        $dl = ConfigGlobal::mi_dele();
        $usuario = ConfigGlobal::mi_usuario();
        $tabla = 'tmp_bdu_' . $dl . '_' . $usuario;

        $sqlDelete = "TRUNCATE TABLE $tabla";
        $sqlCreate = "CREATE TABLE IF NOT EXISTS $tabla(
                    Identif int4 NOT NULL PRIMARY KEY,
                    Apenom              text,
                    dl                  text,
                    ctr               	text,
                    Lugar_Naci          text,
                    Fecha_Naci          text,
                    Email              	text,
                    Tfno_Movil          text,
                    Ce              	text,
                    Prof_Carg           text,
                    Titu_Estu           text,
                    Encargos            text,
                    INCORP              text,
                    pertenece_r         text,
                    camb_fic            text,
                    fecha_c_fic         text,
                    compartida_con_r    text
            )";

        $this->oDbl->query($sqlCreate);
        $this->oDbl->query("CREATE INDEX IF NOT EXISTS $tabla" . "_identif" . " ON $tabla (Identif)");
        $this->oDbl->query("CREATE INDEX IF NOT EXISTS $tabla" . "_dl" . " ON $tabla (Dl)");
        $this->oDbl->query($sqlDelete);

        $campos = "Identif, Apenom, dl, ctr, Lugar_Naci, Fecha_Naci, Email, Tfno_Movil, Ce, Prof_Carg, Titu_Estu, Encargos, INCORP, pertenece_r, camb_fic, fecha_c_fic, compartida_con_r";

        // llenar:
        $sQuery = "SELECT $campos FROM dbo.q_dl_Estudios_b";

        $sqlInsert = "INSERT INTO $tabla ($campos) VALUES";
        $i = 0;
        $values = '';
        $fecha = new DateTimeLocal();
        foreach ($this->oDbU->query($sQuery) as $aDades) {
            $i++;
            $values .= '("' . implode('","', $aDades) . '"),';
            if ($i > 1000) {
                // cambiar la ultima coma por punto y coma
                $values = rtrim($values, ',');
                // execute
                $sql = $sqlInsert . ' ' . $values . ';';
                $this->oDbl->query($sql);
                $i = 0;
                $values = '';
            }
        }
        // el último pack:
       if (!empty($values)) {
           // cambiar la ultima coma por punto y coma
           $values = rtrim($values, ',');
           $sql = $sqlInsert . ' ' . $values . ';';
           $this->oDbl->query($sql);
       }
       // añadir la fecha en que se ha realizado:
       $fecha_iso = $fecha->format(DateTimeInterface::ATOM);
       $sqlTime = "INSERT INTO $tabla (Identif, Apenom) VALUES (\"1111\" , \"$fecha_iso\");";
       $this->oDbl->query($sqlTime);
   }

}