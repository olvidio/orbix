<?php

namespace src\usuarios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use function core\is_true;

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

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('aux_usuarios');
    }

    public function getArrayUsuarios(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_usuario, usuario FROM $nom_tabla ORDER BY usuario";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo usuario
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo usuario
     */
    public function getUsuarios(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $usuarioSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para los bytea: (resources)
            $handle = $aDatos['password'];
            if ($handle !== null) {
                $contents = stream_get_contents($handle);
                fclose($handle);
                $password = $contents;
                $aDatos['password'] = $password;
            }
            $usuario = Usuario::fromArray($aDatos);
            $usuarioSet->add($usuario);
        }
        return $usuarioSet->getTot();
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

        $aDatos = [];
        $aDatos['usuario'] = $usuario->getUsuarioAsString();
        $aDatos['id_role'] = $usuario->getId_role();
        $aDatos['email'] = $usuario->getEmailAsString();
        $aDatos['id_pau'] = $usuario->getCsvIdPauAsString();
        $aDatos['nom_usuario'] = $usuario->getNomUsuarioAsString();
        $aDatos['has_2fa'] = $usuario->has2fa();
        $aDatos['secret_2fa'] = $usuario->getSecret2faAsString();
        $aDatos['cambio_password'] = $usuario->isCambio_password();
        // para los bytea, pero el passwd ya lo tengo en hex con MyCrypt
        // $aDatos['password'] = bin2hex($usuario->getPassword());
        $aDatos['password'] = $usuario->getPasswordAsString();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['has_2fa'])) {
            $aDatos['has_2fa'] = 'true';
        } else {
            $aDatos['has_2fa'] = 'false';
        }
        if (is_true($aDatos['cambio_password'])) {
            $aDatos['cambio_password'] = 'true';
        } else {
            $aDatos['cambio_password'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					usuario                  = :usuario,
					id_role                  = :id_role,
					password                 = :password,
					email                    = :email,
					id_pau                   = :id_pau,
					nom_usuario              = :nom_usuario,
					has_2fa                  = :has_2fa,
					secret_2fa               = :secret_2fa,
                    cambio_password          = :cambio_password";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_usuario = $id_usuario";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $aDatos['id_usuario'] = $usuario->getId_usuario();
            $campos = "(id_usuario,usuario,id_role,password,email,id_pau,nom_usuario,has_2fa,secret_2fa,cambio_password)";
            $valores = "(:id_usuario,:usuario,:id_role,:password,:email,:id_pau,:nom_usuario,:has_2fa,:secret_2fa,:cambio_password)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_usuario): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @return array|bool
     */
    public function datosById(int $id_usuario): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario");
        if ($oDblSt === false) {
            $sClaveError = 'PgusuarioRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }

        // para los bytea, sobre escribo los valores:
        $spassword = '';
        $oDblSt->bindColumn('password', $spassword, PDO::PARAM_STR);
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        if ($aDatos !== false) {
            //$aDatos['password'] = hex2bin($spassword ?? '');
            $aDatos['password'] = $spassword ?? '';
        }

        return $aDatos;
    }

    /**
     * Busca la clase con id_usuario en la base de datos .
     */
    public function findById(int $id_usuario): ?Usuario
    {
        $aDatos = $this->datosById($id_usuario);
        if (empty($aDatos)) {
            return null;
        }
        return Usuario::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select (4::text || nextval('aux_grupos_y_usuarios_id_usuario_seq'::regclass))::numeric";

        return $oDbl->query($sQuery)->fetchColumn();
    }
}
