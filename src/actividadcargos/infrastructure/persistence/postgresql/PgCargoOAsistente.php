<?php

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use PDOStatement;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividadcargos\domain\entity\CargoOAsistente;
use src\actividades\domain\entity\ActividadAll;
use src\personas\domain\entity\PersonaGlobal;
use src\shared\config\ConfigGlobal;
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
    private PDO $oDblEx;

    public function __construct()
    {
        $this->oDbl = GlobalPdo::get('oDBE');
        $this->oDblEx = GlobalPdo::get('oDBER_Select');
    }

    /**
     * Asistencias de una persona de paso (`id_nom` negativo): están en `resto{v|f}.d_asistentes_ex`, tabla
     * común a todas las dl. Se leen con la conexión de `resto` (como `PgAsistenteExRepository`), no con la
     * de la dl: en producción el usuario de la dl no tiene permiso sobre esa tabla. Las personas de la dl
     * (`id_nom` positivo) no tienen filas ahí.
     *
     * @return list<array{id_activ: int, propio: bool, id_cargo: int}>
     */
    private function asistenciasEx(int $id_nom): array
    {
        if ($id_nom >= 0) {
            return [];
        }
        $tabla = (ConfigGlobal::mi_sfsv() === 2 ? 'restof' : 'restov') . '.d_asistentes_ex';
        $stmt = $this->pdoQuery($this->oDblEx, "SELECT id_activ, propio FROM $tabla WHERE id_nom=$id_nom", __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $filas = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_activ']) || !is_numeric($aDades['id_activ'])) {
                continue;
            }
            $filas[] = [
                'id_activ' => (int) $aDades['id_activ'],
                'propio' => \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['propio'] ?? false) ?? false,
                'id_cargo' => 0,
            ];
        }

        return $filas;
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

        // Las de `d_asistentes_ex` llegan por otra conexión: se mezclan aquí con el mismo orden que la
        // consulta (actividad, y dentro de ella primero la asistencia propia y el cargo al final).
        $filas = array_merge($stmt->fetchAll(PDO::FETCH_ASSOC), $this->asistenciasEx($iid_nom));
        $esTrue = static fn (mixed $v): bool => \src\shared\domain\helpers\FuncTablasSupport::isTrue($v) ?? false;
        usort($filas, static fn (array $a, array $b): int => [(int) $a['id_activ'], $esTrue($b['propio']), (int) $a['id_cargo']] <=> [(int) $b['id_activ'], $esTrue($a['propio']), (int) $b['id_cargo']]);

        $aRepe = [];
        $c = 0;
        foreach ($filas as $aDades) {
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
                $oCargoOAsistente->setPropio(\src\shared\domain\helpers\FuncTablasSupport::isTrue($aDades['propio']) ?? false);
            }
            // Si la primera fila de la actividad ya es un cargo (sin asistencia), guardarlo.
            if (isset($aDades['id_cargo']) && is_numeric($aDades['id_cargo'])) {
                $oCargoOAsistente->setId_cargo((int) $aDades['id_cargo']);
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

        // Una segunda llamada en la misma conexión no debe fallar al crearlas ni
        // arrastrar las filas de la anterior.
        $this->pdoExec($oDbl, "DROP TABLE IF EXISTS $tabla_tmp; DROP TABLE IF EXISTS $tabla_p_tmp", __METHOD__, __FILE__, __LINE__);

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
        /** @var array<int, array{f_ini: string, f_fin: string, id_ubi: int|null}> $aActividades */
        $aActividades = [];
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
                $aActividades[(int) $aDadesActiv['id_activ']] = $aDadesActiv;
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
                // Las de `d_asistentes_ex` (otra conexión), solo de las actividades pedidas.
                foreach ($this->asistenciasEx((int) $id_nom) as $aEx) {
                    $id_activ = $aEx['id_activ'];
                    if (!isset($aActividades[$id_activ]) || in_array($id_activ, $aRepe, true)) {
                        continue;
                    }
                    $this->pdoExecute($sentencia_2, [
                        'id_nom' => $id_nom,
                        'id_activ' => $id_activ,
                        'f_ini' => $aActividades[$id_activ]['f_ini'],
                        'f_fin' => $aActividades[$id_activ]['f_fin'],
                        'id_ubi' => $aActividades[$id_activ]['id_ubi'],
                    ], __METHOD__, __FILE__, __LINE__);
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
                // 0 = todavía ninguna persona; un id negativo es una persona de paso.
                if ($id_nom_anterior !== 0) {
                    $a_solapes[$id_nom_anterior] = $a_actividades;
                    $a_actividades = [];
                }
                $a_actividades[] = $id_activ;
            }
            $id_nom_anterior = $id_nom;
        }
        if ($id_nom_anterior !== 0) {
            $a_solapes[$id_nom_anterior] = $a_actividades;
        }

        return $a_solapes;
    }
}
