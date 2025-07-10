<?php

namespace src\certificados\domain\contracts;

use PDO;
use src\certificados\domain\entity\CertificadoRecibido;


/**
 * Interfaz de la clase Certificado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
interface CertificadoRecibidoRepositoryInterface
{

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Certificado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|bool Una colección de objetos de tipo Certificado
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array|bool;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CertificadoRecibido $Certificado);

    public function Guardar(CertificadoRecibido $Certificado);

    public function getErrorTxt();

    public function getoDbl();

    public function setoDbl(PDO $oDbl);

    public function getNomTabla();

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool;

    /**
     * Busca la clase con id_item en el repositorio.
     */
    public function findById(int $id_item);

    public function getNewId_item();
}