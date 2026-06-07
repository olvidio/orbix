<?php

namespace src\dbextern\domain;

use DateTimeInterface;
use PDO;
use RuntimeException;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\GlobalPdo;

class CopiarBDU
{
    private PDO $oDbU;
    private PDO $oDbl;

    public function __construct()
    {
        try {
            $this->oDbU = GlobalPdo::get('oDBListas');
        } catch (RuntimeException) {
            exit(_("no se puede conectar con la base de datos de la BDU"));
        }
        $this->oDbl = GlobalPdo::get('oDBP');
    }

    public function crearTablaTmp(): bool
    {
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
        $oDblSt = $this->oDbl->prepare("INSERT INTO $tabla $nom_campos VALUES $valores");
        if ($oDblSt === false) {
            return false;
        }
        $fecha = new DateTimeLocal();
        $sQuery = "SELECT $nom_campos_select FROM dbo.q_dl_Estudios_b";
        $stmt = $this->oDbU->query($sQuery);
        if ($stmt === false) {
            return false;
        }
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach ($stmt as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            array_walk($aDatos, 'src\shared\domain\helpers\poner_null');
            $oDblSt->execute($aDatos);
        }

        $fecha_iso = $fecha->format(DateTimeInterface::ATOM);
        $sqlTime = "INSERT INTO $tabla (identif, apenom) VALUES ('1111' , '$fecha_iso');";
        $this->oDbl->query($sqlTime);

        return true;
    }

    public function ultimaActualizacion(): string
    {
        $tabla = 'tmp_bdu';

        $sqlTime = "SELECT apenom FROM $tabla WHERE identif = '1111';";
        $sth = $this->oDbl->prepare($sqlTime);
        if ($sth === false) {
            return (int)date('Y') - 5 . '-01-01T00:00:00+00:00';
        }
        $sth->execute();
        $fecha_iso = $sth->fetchColumn();
        if ($fecha_iso === false) {
            $fecha_iso = (int)date('Y') - 5 . '-01-01T00:00:00+00:00';
        }

        $Fecha = DateTimeLocal::createFromFormat(DateTimeInterface::ATOM, (string)$fecha_iso);
        if ($Fecha === false) {
            return (int)date('Y') - 5 . '-01-01T00:00:00+00:00';
        }

        return $Fecha->getFromLocalHora();
    }
}
