<?php

namespace src\notas\domain\contracts;

use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\PersonaNotaPk;

/**
 * Interfaz de la clase Nota y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
interface PersonaNotaOtraRegionStgrRepositoryInterface
{

    public function addCertificado(int $id_nom, string $certificado, \src\shared\domain\value_objects\DateTimeLocal|\src\shared\domain\value_objects\NullDateTimeLocal|null $oF_certificado): void;

    public function deleteCertificado(?string $certificado): void;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /** @param array<string, mixed> $aWhere */


    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<\src\notas\domain\entity\PersonaNotaOtraRegionStgr>
     */
    public function getPersonaNotas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool;

    public function Guardar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom, int $id_nivel, int $tipo_acta): array|false;

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(PersonaNotaPk $pk): array|false;

    /**
     * Busca la clase con id_situacion en el repositorio.
     */
    public function findById(int $id_nom, int $id_nivel, int $tipo_acta): ?PersonaNotaOtraRegionStgr;

    public function findByPk(PersonaNotaPk $pk): ?PersonaNotaOtraRegionStgr;
}