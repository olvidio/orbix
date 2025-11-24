<?php

namespace src\ubis\domain\contracts;

trait PlanoOperationsTrait
{

    /**
     * Descarga el plano de una dirección
     *
     * @param int $id_direccion
     * @param string $nom_tabla
     * @return array
     */
    public function planoDownload(int $id_direccion): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sql = "SELECT plano_nom, plano_extension, plano_doc FROM $nom_tabla WHERE id_direccion = ?";
        $stmt = $oDbl->prepare($sql);
        $stmt->execute([$id_direccion]);
        $stmt->bindColumn(1, $plano_nom, \PDO::PARAM_STR, 256);
        $stmt->bindColumn(2, $plano_extension, \PDO::PARAM_STR, 256);
        $stmt->bindColumn(3, $plano_doc, \PDO::PARAM_LOB);
        $stmt->fetch(\PDO::FETCH_BOUND);

        return [
            'plano_nom' => $plano_nom,
            'plano_extension' => $plano_extension,
            'plano_doc' => $plano_doc,
        ];
    }

    /**
     * Sube/actualiza el plano de una dirección
     *
     * @param int $id_direccion
     * @param string $nom_tabla
     * @param string|null $nom
     * @param string|null $extension
     * @param mixed $fichero
     * @return void
     */
    public function planoUpload(int $id_direccion, ?string $nom, ?string $extension, $fichero): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $nom = $nom ?? '';
        $extension = $extension ?? '';
        $fichero = $fichero ?? '';

        $sql_update = "UPDATE $nom_tabla SET plano_nom = :plano_nom, plano_extension = :plano_extension, plano_doc = :plano_doc WHERE id_direccion = :id_direccion";

        $oDBSt = $oDbl->prepare($sql_update);
        $oDBSt->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
        $oDBSt->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
        $oDBSt->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);
        $oDBSt->bindParam(":id_direccion", $id_direccion, \PDO::PARAM_INT);

        $oDBSt->execute();
    }

    /**
     * Borra el plano de una dirección
     *
     * @param int $id_direccion
     * @param string $nom_tabla
     * @return void
     */
    public function planoBorrar(int $id_direccion): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $nom = null;
        $extension = null;
        $fichero = null;

        $sql_update = "UPDATE $nom_tabla SET plano_nom = :plano_nom, plano_extension = :plano_extension, plano_doc = :plano_doc WHERE id_direccion = :id_direccion";

        $oDBSt = $oDbl->prepare($sql_update);
        $oDBSt->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
        $oDBSt->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
        $oDBSt->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);
        $oDBSt->bindParam(":id_direccion", $id_direccion, \PDO::PARAM_INT);

        $oDBSt->execute();
    }

    /**
     * Método abstracto que debe ser implementado por los repositorios
     */
    abstract protected function getoDbl(): \PDO;
    abstract protected function getNomTabla(): string;
}