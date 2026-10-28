<?php

namespace src\cambios\domain\contracts;

use src\shared\domain\criteria\Criteria;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;


/**
 * Interfaz de la clase CambioUsuarioObjetoPref y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
interface CambioUsuarioObjetoPrefRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<CambioUsuarioObjetoPref>
     */
    public function matching(Criteria $criteria): array;

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CambioUsuarioObjetoPref>
     */
    public function getCambioUsuarioObjetoPrefs(array $aWhere = [], array $aOperators = []): array;

    /**
     * Preferencias de aviso de un objeto y dl cuyo patrón `id_tipo_activ_txt` (regex en la columna)
     * encaja con el id de tipo de actividad dado. Orden: `aviso_tipo, id_usuario, id_tipo_activ_txt DESC`.
     *
     * @return list<CambioUsuarioObjetoPref>
     */
    public function findPorTipoActividad(string $objeto, string $dlOrg, string $idTipoActiv): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool;

    public function Guardar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool;

    public function getErrorTxt(): string;

    public function getNomTabla(): string;

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item_usuario_objeto): array|false;

    public function findById(int $id_item_usuario_objeto): ?CambioUsuarioObjetoPref;

    public function getNewId(): int;
}
