<?php

namespace src\actividadcargos\infrastructure\repositories;

use core\Set;
use PDO;
use src\actividadcargos\domain\contracts\CargoOAsistenteInterface;
use src\actividadcargos\domain\entity\CargoOAsistente;
use src\shared\traits\HandlesPdoErrors;

/**
 * GestorCargoOAsistente
 *
 * Classe per gestionar la llista d'objectes de la clase CargoOAsistente
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class PgCargoOAsistente implements CargoOAsistenteInterface
{
  use HandlesPdoErrors;

    private PDO $oDbl;

    function __construct()
    {
        $this->oDbl = $GLOBALS['oDBE'];
    }

    /**
     * retorna l'array d'objectes tipus CargoOAsistente
     *
     * @param integer id_nom
     * @return array|false
     */
    public function getCargoOAsistente(int $iid_nom): array
    {
        $oDbl = $this->oDbl;

        $oCargoOAsistenteSet = new Set();
        // lista de id_activ ordenados, primero los propios.
        $sQuery = "SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_dl WHERE id_nom=$iid_nom
					UNION ALL
		        SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_out WHERE id_nom=$iid_nom
					UNION ALL
				SELECT id_activ,'f' as propio,id_cargo FROM d_cargos_activ_dl WHERE id_nom=$iid_nom
				ORDER BY 1,2 DESC";

        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aRepe = [];
        $c = 0;
        foreach ($stmt as $aDades) {
            if (in_array($aDades['id_activ'], $aRepe)) { // si está repetido, el primero tiene propio=true.
                // Añado al primero el id_cargo del segundo.
                $Obj = $oCargoOAsistenteSet->getElement($c - 1);
                $Obj->setId_cargo($aDades['id_cargo']);
                $oCargoOAsistenteSet->setElement($c - 1, $Obj);
                continue;
            }
            $oCargoOAsistente = new CargoOAsistente($aDades['id_activ']);
            $oCargoOAsistente->setId_nom($iid_nom);
            $oCargoOAsistente->setPropio($aDades['propio']);
            $oCargoOAsistenteSet->add($oCargoOAsistente);
            $aRepe[] = $aDades['id_activ'];
            $c++;
        }
        return $oCargoOAsistenteSet->getTot();
    }

    function getSolapes(array $cPersonas,array $cActividades):array
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
        foreach ($cActividades as $oActividad) {
            $aDadesActiv['id_activ'] = $oActividad->getId_activ();
            $aDadesActiv['f_ini'] = $oActividad->getF_ini()->getIso();
            $aDadesActiv['f_fin'] = $oActividad->getF_fin()->getIso();
            $aDadesActiv['id_ubi'] = $oActividad->getId_ubi();

            $this->pdoExecute($sentencia_1, $aDadesActiv, __METHOD__, __FILE__, __LINE__);
        }


        $sql2 = "INSERT INTO $tabla_p_tmp (id_nom, id_activ, f_ini, f_fin, id_ubi) VALUES (:id_nom, :id_activ, :f_ini, :f_fin, :id_ubi);";
        $sentencia_2 = $this->pdoPrepare($oDbl, $sql2, __METHOD__, __FILE__, __LINE__);
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

            $aRepe = [];
            $c = 0;
            foreach ($cargosoAsistencias as $aDades) {
                if (in_array($aDades['id_activ'], $aRepe)) { // si está repetido, el primero tiene propio=true.
                    continue;
                }
                $aDadesSacd['id_nom'] = $id_nom;
                $aDadesSacd['id_activ'] = $aDades['id_activ'];
                $aDadesSacd['f_ini'] = $aDades['f_ini'];
                $aDadesSacd['f_fin'] = $aDades['f_fin'];
                $aDadesSacd['id_ubi'] = $aDades['id_ubi'];
                // insertar datos en la tabla $tabla_p_tmp:
                $this->pdoExecute($sentencia_2, $aDadesSacd, __METHOD__, __FILE__, __LINE__);

                $aRepe[] = $aDades['id_activ'];
                $c++;
            }
        }


        // solapes:
        // More details can be found in the manual:
        // http://www.postgresql.org/docs/current/static/rangetypes.html#RANGETYPES-INCLUSIVITY
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
        // No me deja ordenar en el sql
        $solapes = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $a_solapes = [];
        $id_nom_anterior = '';
        $a_actividades = [];
        foreach ($solapes as $aDades) {
            $id_nom = $aDades['id_nom'];
            $id_activ = $aDades['id_activ'];

            if ($id_nom === $id_nom_anterior) {
                $a_actividades[] = $id_activ;
            } else {
                if (!empty($id_nom_anterior)) {
                    $a_solapes[$id_nom_anterior] = $a_actividades;
                    $a_actividades = [];
                }
                $a_solapes[$id_nom] = '';
                $a_actividades[] = $id_activ;
            }
            $id_nom_anterior = $id_nom;
        }
        // el último:
        $a_solapes[$id_nom] = $a_actividades;

        return $a_solapes;

    }
    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}