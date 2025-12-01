<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;

/**
 * Repositorio para gestionar la relación Casa-Dirección
 * Tabla: u_cross_dir_cdc
 */
class PgRelacionUbiDireccionRepository extends ClaseRepository  implements RelacionUbiDireccionRepositoryInterface
{
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_cross_dir_cdc');
    }

    /**
     * Obtiene todos los datos de relación de direcciones asociadas a una casa
     */
    public function getRelacionesPorUbi(int $id_ubi): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        // Seleccionamos también el campo propietario
        $sql = "SELECT id_direccion, principal, propietario 
                FROM $nom_tabla 
                WHERE id_ubi = :id_ubi
                ORDER BY principal DESC, propietario DESC";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($stmt === false) { return []; }
        $stmt->bindValue(':id_ubi', $id_ubi, \PDO::PARAM_INT);
        if (!$this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__)) { return []; }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /** Asocia una dirección a una casa */
    public function asociarDireccion(int $id_ubi, int $id_direccion, ?bool $principal = null): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "INSERT INTO $nom_tabla (id_ubi, id_direccion, principal)
                VALUES (:id_ubi, :id_direccion, :principal)
                ON CONFLICT (id_ubi, id_direccion)
                DO UPDATE SET principal = :principal";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return false; }
        $stmt->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
        $stmt->bindValue(':id_direccion', $id_direccion, PDO::PARAM_INT);
        if ($principal === null) {
            $stmt->bindValue(':principal', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':principal', $principal, PDO::PARAM_BOOL);
        }
        return $this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__);
    }

    /** Desasocia una dirección de una casa */
    public function desasociarDireccion(int $id_ubi, int $id_direccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla
                WHERE id_ubi = :id_ubi AND id_direccion = :id_direccion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return false; }
        $stmt->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
        $stmt->bindValue(':id_direccion', $id_direccion, PDO::PARAM_INT);
        return $this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__);
    }

    /** Obtiene todos los IDs de direcciones asociadas a una casa */
    public function getDireccionesPorUbi(int $id_ubi): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT id_direccion, principal, propietario
                FROM $nom_tabla
                WHERE id_ubi = :id_ubi
                ORDER BY principal DESC NULLS LAST";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return []; }
        $stmt->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
        if (!$this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__)) { return []; }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Obtiene todas las casas asociadas a una dirección */
    public function getUbisPorDireccion(int $id_direccion): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT id_ubi, principal
                FROM $nom_tabla
                WHERE id_direccion = :id_direccion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return []; }
        $stmt->bindValue(':id_direccion', $id_direccion, PDO::PARAM_INT);
        if (!$this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__)) { return []; }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Verifica si existe la relación */
    public function existeRelacion(int $id_ubi, int $id_direccion): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT COUNT(*) FROM $nom_tabla
                WHERE id_ubi = :id_ubi AND id_direccion = :id_direccion";

        $stmt = $oDbl->prepare($sql);
        $stmt->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
        $stmt->bindValue(':id_direccion', $id_direccion, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn() > 0;
    }

    /** Obtiene la dirección principal de una casa (si existe) */
    public function getDireccionPrincipal(int $id_ubi): ?int
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT id_direccion FROM $nom_tabla
                WHERE id_ubi = :id_ubi AND principal = true
                LIMIT 1";

        $stmt = $oDbl->prepare($sql);
        $stmt->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchColumn();
        return $result !== false ? (int)$result : null;
    }

    /** Establece una dirección como principal (y quita el flag de las demás) */
    public function establecerDireccionPrincipal(int $id_ubi, int $id_direccion): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oDbl->beginTransaction();

        try {
            // Quitar el flag principal de todas las direcciones de esta casa
            $sql1 = "UPDATE $nom_tabla
                     SET principal = false
                     WHERE id_ubi = :id_ubi";
            $stmt1 = $oDbl->prepare($sql1);
            $stmt1->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
            $stmt1->execute();

            // Establecer la nueva como principal
            $sql2 = "UPDATE $nom_tabla
                     SET principal = true
                     WHERE id_ubi = :id_ubi AND id_direccion = :id_direccion";
            $stmt2 = $oDbl->prepare($sql2);
            $stmt2->bindValue(':id_ubi', $id_ubi, PDO::PARAM_INT);
            $stmt2->bindValue(':id_direccion', $id_direccion, PDO::PARAM_INT);
            $stmt2->execute();

            $oDbl->commit();
            return true;
        } catch (\Exception $e) {
            $oDbl->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza solo el campo propietario
     */
    public function updatePropietario(int $id_ubi, int $id_direccion, bool $esPropietario): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sql = "UPDATE $nom_tabla 
                SET propietario = :propietario 
                WHERE id_ubi = :id_ubi AND id_direccion = :id_direccion";

        $stmt = $oDbl->prepare($sql);
        $stmt->bindValue(':id_ubi', $id_ubi, \PDO::PARAM_INT);
        $stmt->bindValue(':id_direccion', $id_direccion, \PDO::PARAM_INT);
        $stmt->bindValue(':propietario', $esPropietario, \PDO::PARAM_BOOL);

        return $stmt->execute();
    }

}
