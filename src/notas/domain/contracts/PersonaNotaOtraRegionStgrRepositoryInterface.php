<?php

namespace src\notas\domain\contracts;

use PDO;
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

    public function addCertificado(int $id_nom, string $certificado, $oF_certificado);

    public function deleteCertificado(?string $certificado);

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    public function getPersonaNotas(array $aWhere = [], array $aOperators = []): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool;

    public function Guardar(PersonaNotaOtraRegionStgr $personaNotaOtraRegionStgr): bool;

    public function getErrorTxt(): string;

    public function getoDbl(): PDO;

    public function setoDbl(PDO $oDbl): void;

    public function getNomTabla(): string;

    public function datosById(int $id_nom, int $id_nivel, int $tipo_acta): array|bool;

    public function datosByPk(PersonaNotaPk $pk): array|bool;

    /**
     * Busca la clase con id_situacion en el repositorio.
     */
    public function findById(int $id_nom, int $id_nivel, int $tipo_acta): ?PersonaNotaOtraRegionStgr;

    public function findByPk(PersonaNotaPk $pk): ?PersonaNotaOtraRegionStgr;
}