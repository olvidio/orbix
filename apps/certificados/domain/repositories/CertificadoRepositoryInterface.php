<?php

namespace certificados\domain\repositories;

use PDO;
use certificados\domain\entity\Certificado;
use web\Desplegable;


use web\DateTimeLocal;
use web\NullDateTimeLocal;
use core\ConverterDate;
use function core\is_true;
/**
 * Interfaz de la clase Certificado y su Repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
interface CertificadoRepositoryInterface
{

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Certificado
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Certificado
	
	 */
	public function getCertificados(array $aWhere=[], array $aOperators=[]);
	
/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Certificado $Certificado);

	public function Guardar(Certificado $Certificado);

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
    public function datosById(int $id_item);
	
    /**
     * Busca la clase con id_item en el repositorio.
	
     */
    public function findById(int $id_item);
	
    public function getNewId_item();
}