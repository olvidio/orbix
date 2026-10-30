<?php

namespace src\usuarios\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\usuarios\domain\criteria\UsuarioGrupoField;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;

/**
 * Clase que adapta la tabla aux_cross_usuarios_grupos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgUsuarioGrupoRepository extends ClaseRepository implements UsuarioGrupoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBE');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBE_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('aux_cross_usuarios_grupos');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * UsuarioGrupo que cumplen el criterio.
     *
     * @return list<UsuarioGrupo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(UsuarioGrupoField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = UsuarioGrupo::fromArray($aDatos);
        }

        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(UsuarioGrupo $UsuarioGrupo): bool
    {
        $id_usuario = $UsuarioGrupo->getId_usuario();
        $id_grupo = $UsuarioGrupo->getId_grupo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_usuario = $id_usuario AND id_grupo = $id_grupo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(UsuarioGrupo $UsuarioGrupo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $aDatos = [];
        $aDatos['id_grupo'] = $UsuarioGrupo->getId_grupo();
        $aDatos['id_usuario'] = $UsuarioGrupo->getId_usuario();
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);

        //INSERT
        $campos = "(id_usuario,id_grupo)";
        $valores = "(:id_usuario,:id_grupo)";
        $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_usuario
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_usuario): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario";
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
     * Busca la clase con id_usuario en la base de datos .
     */
    public function findById(int $id_usuario): ?UsuarioGrupo
    {
        $aDatos = $this->datosById($id_usuario);
        if ($aDatos === false) {
            return null;
        }
        return UsuarioGrupo::fromArray($aDatos);
    }
}