<?php

namespace src\usuarios\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\usuarios\domain\criteria\UsuarioField;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

/**
 * Clase que adapta la tabla aux_usuarios a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgUsuarioRepository extends ClaseRepository implements UsuarioRepositoryInterface
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
        $this->setNomTabla('aux_usuarios');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayUsuarios(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_usuario, usuario FROM $nom_tabla ORDER BY usuario";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave) || !isset($aClave[0], $aClave[1])) {
                continue;
            }
            $clave = $aClave[0];
            $val = (string) $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Usuario que cumplen el criterio.
     *
     * @return list<Usuario>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(UsuarioField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            // para los bytea: (resources)
            $handle = $aDatos['password'] ?? null;
            if (is_resource($handle)) {
                $contents = stream_get_contents($handle);
                fclose($handle);
                $aDatos['password'] = $contents;
            }
            $result[] = Usuario::fromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Usuario>
     */
    public function getUsuarios(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(UsuarioField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Usuario $usuario): bool
    {
        $id_usuario = $usuario->getId_usuario();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_usuario = $id_usuario";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Usuario $usuario): bool
    {
        $id_usuario = $usuario->getId_usuario();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_usuario);

        $aDatos = $usuario->toArrayForDatabase();

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_usuario']);
            $update = "
					usuario                  = :usuario,
					id_role                  = :id_role,
					password                 = :password,
					email                    = :email,
					csv_id_pau               = :csv_id_pau,
					nom_usuario              = :nom_usuario,
					has_2fa                  = :has_2fa,
					secret_2fa               = :secret_2fa,
                    cambio_password          = :cambio_password";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_usuario = $id_usuario";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(id_usuario,usuario,id_role,password,email,csv_id_pau,nom_usuario,has_2fa,secret_2fa,cambio_password)";
            $valores = "(:id_usuario,:usuario,:id_role,:password,:email,:csv_id_pau,:nom_usuario,:has_2fa,:secret_2fa,:cambio_password)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_usuario): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario";
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

        // para los bytea: (resources)
        $handle = $aDatos['password'] ?? null;
        if (is_resource($handle)) {
            $contents = stream_get_contents($handle);
            fclose($handle);
            $aDatos['password'] = $contents;
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
    public function findById(int $id_usuario): ?Usuario
    {
        $aDatos = $this->datosById($id_usuario);
        if ($aDatos === false) {
            return null;
        }
        return Usuario::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select (4::text || nextval('aux_grupos_y_usuarios_id_usuario_seq'::regclass))::numeric";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}