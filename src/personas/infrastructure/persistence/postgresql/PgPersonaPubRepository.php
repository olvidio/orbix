<?php

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaPub;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaGlobalListsTrait;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\personas\infrastructure\persistence\postgresql\traits\PersonaCriteriaTrait;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;


/**
 * Clase que adapta la tabla personas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PgPersonaPubRepository extends ClaseRepository implements PersonaPubRepositoryInterface
{
    use PersonaGlobalListsTrait;
    use PersonaCriteriaTrait;

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
    ) {
        $this->setoDbl(GlobalPdo::get('oDBP'));
        // Fuente real: global.personas (publicado_para) + p_de_paso; no depender de
        // v_personas_pub por si la migración de la vista aún no se aplicó.
        $this->setNomTabla($this->sqlFromPersonasPub());
    }

    /**
     * Subconsulta equivalente a v_personas_pub con mapa publicado_para e id_schema.
     */
    private function sqlFromPersonasPub(): string
    {
        return '(
            SELECT
                p.id_nom, p.id_tabla, p.dl, p.sacd, p.trato, p.nom, p.nx1, p.apellido1,
                p.nx2, p.apellido2, p.f_nacimiento, p.idioma_preferido, p.situacion,
                p.f_situacion, p.apel_fam, p.inc, p.f_inc, p.nivel_stgr, p.profesion,
                p.eap, p.observ, p.lugar_nacimiento,
                NULL::smallint AS edad,
                NULL::boolean AS profesor_stgr,
                p.publicado_para,
                p.id_schema
            FROM global.personas p
            WHERE p.publicado_para IS NOT NULL
              AND jsonb_typeof(p.publicado_para) = \'object\'
              AND p.publicado_para <> \'{}\'::jsonb
            UNION ALL
            SELECT
                pp.id_nom, pp.id_tabla, pp.dl, pp.sacd, pp.trato, pp.nom, pp.nx1, pp.apellido1,
                pp.nx2, pp.apellido2, pp.f_nacimiento, pp.idioma_preferido, pp.situacion,
                pp.f_situacion, pp.apel_fam, pp.inc, pp.f_inc, pp.nivel_stgr, pp.profesion,
                pp.eap, pp.observ, pp.lugar_nacimiento,
                pp.edad,
                pp.profesor_stgr,
                \'{"*": null}\'::jsonb AS publicado_para,
                NULL::integer AS id_schema
            FROM p_de_paso pp
        ) AS v_personas_pub';
    }

    /**
     * Crea una entidad PersonaPub desde un array de datos
     */
    /** @param array<string, mixed> $aDatos */
    protected function createEntityFromArray(array $aDatos): PersonaPub
    {
        return PersonaPub::fromArray($this->withIdSchema($aDatos));
    }

    /**
     * Si falta id_schema (p.ej. p_de_paso), se obtiene del esquema de la dl de origen.
     */
    /**
     * @param array<string, mixed> $aDatos
     * @return array<string, mixed>
     */
    private function withIdSchema(array $aDatos): array
    {
        $idSchema = $aDatos['id_schema'] ?? null;
        if (is_numeric($idSchema) && (int) $idSchema > 0) {
            $aDatos['id_schema'] = (int) $idSchema;

            return $aDatos;
        }

        $dl = $aDatos['dl'] ?? '';
        if (is_string($dl) && $dl !== '' && $dl !== 'cg') {
            try {
                $resolved = $this->delegacionRepository->mi_region_stgr($dl)['mi_id_schema'] ?? null;
                if (is_numeric($resolved) && (int) $resolved > 0) {
                    $aDatos['id_schema'] = (int) $resolved;

                    return $aDatos;
                }
            } catch (\Throwable) {
                // p_de_paso u otras filas sin región stgr resoluble
            }
        }

        // Nunca pasar null a PersonaPub::setId_schema(int).
        $aDatos['id_schema'] = 0;

        return $aDatos;
    }

    /**
     * Filtro listable: publicada vigente para mi DL o para todas (*).
     */
    private function sqlFiltroPublicadoParaMiDl(): string
    {
        $miDl = ConfigGlobal::mi_dele();
        $oDbl = $this->getoDbl();
        $quotedDl = $oDbl->quote($miDl);
        $quotedAll = $oDbl->quote(PersonaPublicacion::DL_TODAS);

        return PersonaPublicacion::sqlVigenteParaDl($quotedDl, $quotedAll);
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Personas que cumplen el criterio.
     *
     * @return list<PersonaPub>
     */
    public function matching(Criteria $criteria): array
    {
        $result = [];
        foreach ($this->filasPersonasMatching($criteria, $this->sqlFiltroPublicadoParaMiDl()) as $aDatos) {
            try {
                $result[] = $this->createEntityFromArray($aDatos);
            } catch (RegionStgrConfigException $e) {
                $nombre = trim(
                    $this->scalarFieldAsString($aDatos, 'id_nom') . ': '
                    . $this->scalarFieldAsString($aDatos, 'apellido1') . ' '
                    . $this->scalarFieldAsString($aDatos, 'apellido2') . ', '
                    . $this->scalarFieldAsString($aDatos, 'nom')
                );
                throw new \RuntimeException($nombre, 0, $e);
            }
        }

        return $result;
    }

    /**
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param array<int, true> $sinRegionStgrPorIdNom
     * @return list<PersonaPub>
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<int, true> $sinRegionStgrPorIdNom
     */
    public function getPersonasParaListado(
        Criteria $criteria,
        array &$problemasRegionStgr,
        array &$sinRegionStgrPorIdNom = [],
    ): array {
        $sinRegionStgrPorIdNom = [];
        $PersonaDlSet = new Set();
        foreach ($this->filasPersonasMatching($criteria, $this->sqlFiltroPublicadoParaMiDl()) as $aDatos) {
            $marcaAviso = false;
            $persona = $this->createEntityParaListado($aDatos, $problemasRegionStgr, $marcaAviso);
            if ($marcaAviso) {
                $sinRegionStgrPorIdNom[$persona->getId_nom()] = true;
            }
            $PersonaDlSet->add($persona);
        }

        /** @var list<PersonaPub> $result */
        $result = array_values($PersonaDlSet->getTot());

        return $result;
    }

    /**
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out bool $marcaAvisoRegionStgr
     */
    public function findByIdParaListado(int $id_nom, array &$problemasRegionStgr, bool &$marcaAvisoRegionStgr): ?PersonaPub
    {
        $marcaAvisoRegionStgr = false;
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }

        return $this->createEntityParaListado($aDatos, $problemasRegionStgr, $marcaAvisoRegionStgr);
    }

    /**
     * @param array<string, mixed> $aDatos
     * @param array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out array<string, array<int|string, string>> $problemasRegionStgr
     * @param-out bool $marcaAvisoRegionStgr
     */
    private function createEntityParaListado(
        array $aDatos,
        array &$problemasRegionStgr,
        bool &$marcaAvisoRegionStgr = false,
    ): PersonaPub {
        $marcaAvisoRegionStgr = false;
        try {
            return PersonaPub::fromArray($this->withIdSchema($aDatos));
        } catch (\RuntimeException $e) {
            if (!RegionStgrAviso::esDlSinRegion($e)) {
                throw $e;
            }
            RegionStgrAviso::registrar($problemasRegionStgr, $e);
            $marcaAvisoRegionStgr = true;
            $aDatos['id_schema'] = 0;

            return PersonaPub::fromArray($aDatos);
        }
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
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom ORDER BY CASE WHEN situacion = 'A' THEN 0 ELSE 1 END LIMIT 1";
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
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?PersonaPub
    {
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }
        return $this->createEntityFromArray($aDatos);
    }

    /**
     * @param array<string, mixed> $aDatos
     */
    private function scalarFieldAsString(array $aDatos, string $key): string
    {
        $value = $aDatos[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}