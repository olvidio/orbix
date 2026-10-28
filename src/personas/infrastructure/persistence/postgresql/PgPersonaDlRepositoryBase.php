<?php

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaGlobalListsTrait;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\personas\domain\criteria\PersonaField;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaCriteriaTrait;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;

/**
 * Clase base abstracta para repositorios de PersonaDl y subtipos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/1/2026
 */
abstract class PgPersonaDlRepositoryBase extends ClaseRepository
{
    use PersonaGlobalListsTrait;
    use PersonaCriteriaTrait;

    /**
     * Método abstracto que cada hijo debe implementar para crear su tipo específico de entidad
     *
     * @param array<string, mixed> $aDatos
     */
    abstract protected function createEntityFromArray(array $aDatos): PersonaDl;

    /* --------------------  BASIC SEARCH ---------------------------------------- */

    /**
     * Personas que cumplen el criterio.
     *
     * @return list<PersonaDl>
     */
    public function matching(Criteria $criteria): array
    {
        $result = [];
        foreach ($this->filasPersonasMatching($criteria) as $aDatos) {
            // Cada repositorio hijo crea su tipo específico
            $result[] = $this->createEntityFromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PersonaDl>
     */
    public function getPersonas(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(PersonaField::class, []);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    protected function isNew(int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     * @param int $id_nom
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();
        $aDatos = PersonaPublicacion::hydrateRow($aDatos);
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * Busca la clase con id_nom en la base de datos.
     * Devuelve el tipo específico según el repositorio hijo
     */
    public function findById(int $id_nom): ?PersonaDl
    {
        $aDatos = $this->datosById($id_nom);
        if (empty($aDatos)) {
            return null;
        }
        return $this->createEntityFromArray($aDatos);
    }
}
