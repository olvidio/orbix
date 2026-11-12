<?php

namespace src\notas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\notas\domain\criteria\ActaTribunalField;
use src\notas\infrastructure\persistence\postgresql\traits\NotasCriteriaTrait;
use src\shared\domain\criteria\Criteria;
use src\shared\config\ConfigGlobal;
use PDO;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla e_actas_tribunal_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgActaTribunalRepository extends ClaseRepository implements ActaTribunalRepositoryInterface
{
    use HandlesPdoErrors;
    use NotasCriteriaTrait;

    public function __construct()
    {
        // Si es cr, se mira en todas:
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $oDbl = GlobalPdo::get('oDBP');
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal');
        } else {
            $oDbl = GlobalPdo::get('oDB');
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal_dl');
        }
    }

    /**
     * retorna JSON llista d'examinadors
     * des del 2020 (perque els d'abans són amb llatí)
     */
    public function getJsonExaminadores(string $sQuery = ''): string
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        // El texto llega del usuario (autocompletado): va como parámetro y escapado para que se busque literal.
        // Antes se interpolaba en el SQL (inyección) y una comilla rompía la consulta.
        $params = [];
        $sCondi = "WHERE substring(acta, '\\/(\\d{2})')::integer > 19 ";
        if ($sQuery !== '') {
            $sCondi .= "AND public.sin_acentos(examinador::text) ~* public.sin_acentos(:patron::text) ";
            $params['patron'] = preg_quote($sQuery);
        }
        $sQry = "SELECT DISTINCT examinador FROM $nom_tabla " . $sCondi . " ORDER BY examinador LIMIT 25";
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $params, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return '[]';
        }

        $examinadores = [];
        while (($aDades = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            $examinador = is_array($aDades) ? ($aDades['examinador'] ?? '') : '';
            if (is_string($examinador) && $examinador !== '') {
                $examinadores[] = ['label' => $examinador];
            }
        }

        // json_encode: con la concatenación anterior un nombre con comillas rompía el JSON.
        return json_encode($examinadores, JSON_UNESCAPED_UNICODE) ?: '[]';
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActaTribunal que cumplen el criterio.
     *
     * @return list<ActaTribunal>
     */
    public function matching(Criteria $criteria): array
    {
        return array_map(ActaTribunal::fromArray(...), $this->filasNotasMatching($criteria, ActaTribunalField::class));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActaTribunal $ActaTribunal): bool
    {
        $id_item = $ActaTribunal->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActaTribunal $ActaTribunal): bool
    {
        $id_item = $ActaTribunal->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ActaTribunal->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					acta                     = :acta,
					examinador               = :examinador,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(acta,examinador,orden,id_item)";
            $valores = "(:acta,:examinador,:orden,:id_item)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ActaTribunal
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ActaTribunal::fromArray($aDatos);
    }

}