<?php

namespace src\dossiers\domain\contracts;

use src\dossiers\domain\entity\Dossier;
use src\shared\domain\criteria\Criteria;
use src\dossiers\domain\value_objects\DossierPk as DossierPkAlias;


/**
 * Interfaz de la clase Dossier y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
interface DossierRepositoryInterface
{

    //public function DossiersNotEmpty($pau = '', $id = ''): array<string, mixed>|false;

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Dossiers que cumplen el criterio.
     *
     * @return list<Dossier>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Dossier $Dossier): bool;

    public function Guardar(Dossier $Dossier): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    /**
     * @deprecated usar datosByPk
     */
    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_dossier, int $id_pau, string $tabla): array|false;

    /**
     * Obtiene los datos por clave primaria compuesta usando un Value Object
     *
     * @return array<string, mixed>|false
     */
    public function datosByPk(DossierPkAlias $pk): array|false;

    /**
     * @deprecated usar findByPk
     */
    public function findById(int $id_tipo_dossier, int $id_pau, string $tabla): ?Dossier;

    /**
     * Busca la clase por clave primaria compuesta usando un Value Object
     */
    public function findByPk(DossierPkAlias $pk): ?Dossier;

    /**
     * Crea una entidad Dossier en memoria a partir de la clave primaria (sin persistir).
     */
    public function crearDossier(DossierPkAlias $pk): Dossier;
}