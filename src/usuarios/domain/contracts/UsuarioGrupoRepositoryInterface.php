<?php

namespace src\usuarios\domain\contracts;

use src\shared\domain\criteria\Criteria;
use PDO;
use src\usuarios\domain\entity\UsuarioGrupo;

/**
 * Interfaz de la clase UsuarioGrupo y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
interface UsuarioGrupoRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * @return list<UsuarioGrupo>
     */
    public function matching(Criteria $criteria): array;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(UsuarioGrupo $UsuarioGrupo): bool;

    public function Guardar(UsuarioGrupo $UsuarioGrupo): bool;

    public function getErrorTxt(): string;



    public function getNomTabla(): string;

    public function setoDbl_select(PDO $oDbl_Select): void;

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_usuario
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_usuario): array|false;

    /**
     * Busca la clase con id_usuario en el repositorio.
     */
    public function findById(int $id_usuario): ?UsuarioGrupo;
}