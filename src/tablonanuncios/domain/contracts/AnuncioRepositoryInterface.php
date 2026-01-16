<?php

namespace src\tablonanuncios\domain\contracts;

use PDO;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;


/**
 * Interfaz de la clase Certificado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/10/2024
 */
interface AnuncioRepositoryInterface
{

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Certificado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Certificado
     */
    public function getAnuncios(array $aWhere = [], array $aOperators = []): array|false;

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Anuncio $Anuncio);

    public function Guardar(Anuncio $Anuncio);

    public function getErrorTxt();

    public function getoDbl();

    public function setoDbl(PDO $oDbl);

    public function getNomTabla();

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param AnuncioId $vo
     * @return array|bool
     */
    public function datosById(AnuncioId $vo): array|bool;

    /**
     * Busca la clase con uuid_item en el repositorio.
     */
    public function findById(AnuncioId $vo);

}