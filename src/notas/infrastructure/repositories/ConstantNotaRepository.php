<?php

namespace src\notas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\notas\domain\entity\Nota;
use function core\is_true;


/**
 * Clase que adapta la tabla e_notas_situacion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class ConstantNotaRepository implements NotaRepositoryInterface
{
    public function __construct()
    {
        Nota::traduccion_init();
    }

    public function getArrayNotasNoSuperadas(): array
    {
        $aNoSuperadas = [
            Nota::DESCONOCIDO,
            Nota::CURSADA,
            Nota::PREVISTA_CA,
            Nota::PREVISTA_INV,
            Nota::NO_HECHA_CA,
            Nota::NO_HECHA_INV,
            Nota::EXAMINADO,
            Nota::FALTA_CERTIFICADO,
        ];

        return $aNoSuperadas;
    }

    public function getArrayNotasSuperadas(): array
    {
       $aSuperadas = [
            Nota::SUPERADA,
            Nota::MAGNA,
            Nota::SUMMA,
            Nota::CONVALIDADA,
            Nota::NUMERICA,
            Nota::EXENTO,
        ];

        return $aSuperadas;
    }

    public function getArrayNotas(): array
    {
        return Nota::$array_status_txt;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */


    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Nota $Nota): bool
    {
        $id_situacion = $Nota->getId_situacion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_situacion = $id_situacion")) === FALSE) {
            $sClaveError = 'PgNotaRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Nota $Nota): bool
    {
        $id_situacion = $Nota->getId_situacion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_situacion);

        $aDatos = [];
        $aDatos['descripcion'] = $Nota->getDescripcion();
        $aDatos['superada'] = $Nota->isSuperada();
        $aDatos['breve'] = $Nota->getBreve();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['superada'])) {
            $aDatos['superada'] = 'true';
        } else {
            $aDatos['superada'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					descripcion              = :descripcion,
					superada                 = :superada,
					breve                    = :breve";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_situacion = $id_situacion")) === FALSE) {
                $sClaveError = 'PgNotaRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgNotaRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['id_situacion'] = $Nota->getId_situacion();
            $campos = "(id_situacion,descripcion,superada,breve)";
            $valores = "(:id_situacion,:descripcion,:superada,:breve)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgNotaRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgNotaRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_situacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_situacion = $id_situacion")) === FALSE) {
            $sClaveError = 'PgNotaRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_situacion
     * @return array|bool
     */
    public function datosById(int $id_situacion): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_situacion = $id_situacion")) === FALSE) {
            $sClaveError = 'PgNotaRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_situacion en la base de datos .
     */
    public function findById(int $id_situacion): ?Nota
    {
        $aDatos = $this->datosById($id_situacion);
        if (empty($aDatos)) {
            return null;
        }
        return (new Nota())->setAllAttributes($aDatos);
    }
}