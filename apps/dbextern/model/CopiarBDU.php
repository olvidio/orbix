<?php

namespace dbextern\model;

use core\ConfigGlobal;
use DateTimeInterface;
use PDO;
use web\DateTimeLocal;

class CopiarBDU
{
    private object $oDbU;
    private string $tabla_bdu;

    private object $oDbl;

    function __construct()
    {
        if (!empty($GLOBALS['oDBListas']) && $GLOBALS['oDBListas'] === 'error') {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $this->oDbU = $GLOBALS['oDBListas'];
        $this->oDbl = $GLOBALS['oDBP'];

        $this->tabla_bdu = 'dbo.q_Aux_Dl';
    }

    public function crearTablaTmp()
    {
        $dl = ConfigGlobal::mi_dele();
        $usuario = ConfigGlobal::mi_usuario();
        //$tabla = 'tmp_bdu_' . $dl . '_' . $usuario;
        $tabla = 'tmp_bdu';

        $sqlDelete = "TRUNCATE TABLE $tabla";
        $sqlCreate = "CREATE TABLE IF NOT EXISTS $tabla(
                    Identif bigint NOT NULL PRIMARY KEY,
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

        $campos = ['Identif', 'Apenom', 'dl', 'ctr', 'Lugar_Naci', 'Fecha_Naci', 'Email', 'Tfno_Movil', 'Ce', 'Prof_Carg', 'Titu_Estu', 'Encargos', 'INCORP', 'pertenece_r', 'camb_fic', 'fecha_c_fic', 'compartida_con_r'];

        $nom_campos_select = implode(",", $campos);
        $nom_campos = "(" . $nom_campos_select . ")";
        $valores = "(:" . implode(",:", $campos) . ")";
        if (($oDblSt = $this->oDbl->prepare("INSERT INTO $tabla $nom_campos VALUES $valores")) === false) {
            $sClauError = 'ActividadDl.insertar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($this->oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        // llenar:
        $fecha = new DateTimeLocal();
        $sQuery = "SELECT $nom_campos_select FROM dbo.q_dl_Estudios_b";
        $stmt = $this->oDbU->query($sQuery);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($stmt as $aDades) {
            array_walk($aDades, 'core\poner_null');
            $oDblSt->execute($aDades);
        }


        /*
        // llenar:
        $sQuery = "SELECT $campos FROM dbo.q_dl_Estudios_b";

        $sqlInsert = "INSERT INTO $tabla ($campos) VALUES";
        $i = 0;
        $values = '';
        $fecha = new DateTimeLocal();
        foreach ($this->oDbU->query($sQuery) as $aDades) {
            $i++;
            $aDades_slashed = str_replace("'", "\'", $aDades);
            $values .= "('" . implode("','", $aDades_slashed) . "'),";
            if ($i > 1000) {
                // remplazar vacíos por NULL
                $values = str_replace("''", 'NULL', $values);
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
            // remplazar vacíos por NULL
            $values = str_replace("''", 'NULL', $values);
            // cambiar la ultima coma por punto y coma
            $values = rtrim($values, ',');
            $sql = $sqlInsert . ' ' . $values . ';';
            $this->oDbl->query($sql);
        }
        */

        // añadir la fecha en que se ha realizado:
        $fecha_iso = $fecha->format(DateTimeInterface::ATOM);
        $sqlTime = "INSERT INTO $tabla (identif, apenom) VALUES ('1111' , '$fecha_iso');";
        $this->oDbl->query($sqlTime);
    }

    public function ultimaActualizacion()
    {
        $tabla = 'tmp_bdu';

        $sqlTime = "SELECT apenom FROM $tabla WHERE identif = '1111';";
        $sth = $this->oDbl->prepare($sqlTime);
        $sth->execute();
        $fecha_iso = $sth->fetchColumn();
        if ($fecha_iso === false) {
            $fecha_iso = (int)date('Y') - 5 . '-01-01T00:00:00+00:00'; // algo (5años para obligar a ejecutar la actualización)
        }

        $Fecha = DateTimeLocal::createFromFormat(DateTimeInterface::ATOM, $fecha_iso);

        return $Fecha->getFromLocalHora();
    }

}