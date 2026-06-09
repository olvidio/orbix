<?php

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use PDOStatement;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividadcargos\domain\entity\CargoOAsistente;
use src\actividades\domain\entity\ActividadAll;
use src\personas\domain\entity\PersonaGlobal;
use function src\shared\domain\helpers\is_true;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\StoresPdoErrorTxt;

/**
 * GestorCargoOAsistente — lista de objetos CargoOAsistente.
 */
class PgCargoOAsistente implements CargoOAsistenteInterface
{
    use HandlesPdoErrors;
    use StoresPdoErrorTxt;

    private PDO $oDbl;

    public function __construct()
    {
        $this->oDbl = GlobalPdo::get('oDBE');
    }

    /**
     * @return list<CargoOAsistente>
     */
    public function getCargoOAsistente(int $iid_nom): array
    {
        $oDbl = $this->oDbl;

        $oCargoOAsistenteSet = new Set();
        $sQuery = "SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_dl WHERE id_nom=$iid_nom
					UNION ALL
		        SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_out WHERE id_nom=$iid_nom
					UNION ALL
				SELECT id_activ,'f' as propio,id_cargo FROM d_cargos_activ_dl WHERE id_nom=$iid_nom
				ORDER BY 1,2 DESC";

        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aRepe = [];
        $c = 0;
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_activ'])) {
                continue;
            }
            $id_activ = is_numeric($aDades['id_activ']) ? (int) $aDades['id_activ'] : 0;
            if (in_array($id_activ, $aRepe, true)) {
                $Obj = $oCargoOAsistenteSet->getElement($c - 1);
                if ($Obj instanceof CargoOAsistente && isset($aDades['id_cargo'])) {
                    $Obj->setId_cargo(is_numeric($aDades['id_cargo']) ? (int) $aDades['id_cargo'] : 0);
                    $oCargoOAsistenteSet->setElement($c - 1, $Obj);
                }
                continue;
            }
            $oCargoOAsistente = new CargoOAsistente($id_activ);
            $oCargoOAsistente->setId_nom($iid_nom);
            if (isset($aDades['propio'])) {
                $oCargoOAsistente->setPropio(is_true($aDades['propio']) ?? false);
            }
            $oCargoOAsistenteSet->add($oCargoOAsistente);
            $aRepe[] = $id_activ;
            $c++;
        }
        /** @var list<CargoOAsistente> $result */
        $result = array_values($oCargoOAsistenteSet->getTot());

        return $result;
    }

    /**
     * @param iterable<PersonaGlobal> $cPersonas
     * @param iterable<ActividadAll> $cActividades
     * @return array<int, list<int>>
     */
    public function getSolapes(iterable $cPersonas, iterable $cActividades): array
    {
        $oDbl = $this->oDbl;

        $tabla_tmp = 'tmp_activ_solape';
        $tabla_p_tmp = 'tmp_sacd_solape';

        $sqlCreateA = "CREATE TEMP TABLE $tabla_tmp (
						id_activ bigint,
						f_ini date, 
                        f_fin date,
                        id_ubi integer
					 )";

        $this->pdoQuery($oDbl, $sqlCreateA, __METHOD__, __FILE__, __LINE__);

        $sqlCreateP = "CREATE TEMP TABLE $tabla_p_tmp (
						id SERIAL,
                        id_nom integer,
						id_activ bigint,
						f_ini date, 
                        f_fin date,
                        id_ubi integer
					 )";

        $this->pdoQuery($oDbl, $sqlCreateP, __METHOD__, __FILE__, __LINE__);

        $sql = "INSERT INTO $tabla_tmp (id_activ, f_ini, f_fin, id_ubi) VALUES (:id_activ, :f_ini, :f_fin, :id_ubi);";
        $sentencia_1 = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($sentencia_1 instanceof PDOStatement) {
            foreach ($cActividades as $oActividad) {
                $fIni = $oActividad->getF_ini();
                $fFin = $oActividad->getF_fin();
                if ($fIni === null || $fFin === null) {
                    continue;
                }
                $aDadesActiv = [
                    'id_activ' => $oActividad->getId_activ(),
                    'f_ini' => $fIni->getIso(),
                    'f_fin' => $fFin->getIso(),
                    'id_ubi' => $oActividad->getId_ubi(),
                ];
                $this->pdoExecute($sentencia_1, $aDadesActiv, __METHOD__, __FILE__, __LINE__);
            }
        }

        $sql2 = "INSERT INTO $tabla_p_tmp (id_nom, id_activ, f_ini, f_fin, id_ubi) VALUES (:id_nom, :id_activ, :f_ini, :f_fin, :id_ubi);";
        $sentencia_2 = $this->pdoPrepare($oDbl, $sql2, __METHOD__, __FILE__, __LINE__);
        if ($sentencia_2 instanceof PDOStatement) {
            foreach ($cPersonas as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $sQuery = "SELECT d.id_activ, d.propio, 0 as id_cargo, a.f_ini, a.f_fin, a.id_ubi
                        FROM d_asistentes_dl d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                        UNION ALL
                    SELECT d.id_activ, d.propio, 0 as id_cargo, a.f_ini, a.f_fin, a.id_ubi
                        FROM d_asistentes_out d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                        UNION ALL
                    SELECT d.id_activ, 'f' as propio, d.id_cargo, a.f_ini, a.f_fin, a.id_ubi 
                        FROM d_cargos_activ_dl d JOIN $tabla_tmp a USING (id_activ) WHERE id_nom=$id_nom
                    ORDER BY 1,2 DESC";

                $cargosoAsistencias = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
                if ($cargosoAsistencias === false) {
                    continue;
                }

                $aRepe = [];
                foreach ($cargosoAsistencias as $aDades) {
                    if (!is_array($aDades) || !isset($aDades['id_activ'])) {
                        continue;
                    }
                    $id_activ = is_numeric($aDades['id_activ']) ? (int) $aDades['id_activ'] : 0;
                    if (in_array($id_activ, $aRepe, true)) {
                        continue;
                    }
                    $aDadesSacd = [
                        'id_nom' => $id_nom,
                        'id_activ' => $id_activ,
                        'f_ini' => $aDades['f_ini'] ?? null,
                        'f_fin' => $aDades['f_fin'] ?? null,
                        'id_ubi' => $aDades['id_ubi'] ?? null,
                    ];
                    $this->pdoExecute($sentencia_2, $aDadesSacd, __METHOD__, __FILE__, __LINE__);
                    $aRepe[] = $id_activ;
                }
            }
        }

        $sQuery = "
                SELECT f1.*
                FROM $tabla_p_tmp f1
                WHERE exists (select 1
                    from $tabla_p_tmp f2
                    where tsrange(f2.f_ini, f2.f_fin, '[]') && tsrange(f1.f_ini, f1.f_fin, '[]')
                    and f2.id_nom = f1.id_nom
                    and f2.id <> f1.id)
                ORDER BY f1.id_nom,f1.f_ini
                ;
        ";
        $solapes = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($solapes === false) {
            return [];
        }

        $a_solapes = [];
        $id_nom_anterior = 0;
        $a_actividades = [];
        foreach ($solapes as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_nom'], $aDades['id_activ'])) {
                continue;
            }
            $id_nom = (int) $aDades['id_nom'];
            $id_activ = is_numeric($aDades['id_activ']) ? (int) $aDades['id_activ'] : 0;

            if ($id_nom === $id_nom_anterior) {
                $a_actividades[] = $id_activ;
            } else {
                if ($id_nom_anterior > 0) {
                    $a_solapes[$id_nom_anterior] = $a_actividades;
                    $a_actividades = [];
                }
                $a_actividades[] = $id_activ;
            }
            $id_nom_anterior = $id_nom;
        }
        if ($id_nom_anterior > 0) {
            $a_solapes[$id_nom_anterior] = $a_actividades;
        }

        return $a_solapes;
    }
}
