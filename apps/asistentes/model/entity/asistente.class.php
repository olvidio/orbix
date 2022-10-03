<?php
namespace asistentes\model\entity;
/**
 * Fitxer amb la Classe que accedeix a la vista av_asistentes
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad av_asistentes
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class Asistente extends AsistentePub
{
    // tipo plaza constantes.
    //1:pedida, 2:en espera, 3: denegada, 4:asignada, 5:confirmada
    const PLAZA_PEDIDA = 1; // Pedida
    const PLAZA_EN_ESPERA = 2; // En espera.
    const PLAZA_DENEGADA = 3; // Denegada. De hecho ahora no se usa, pero sirve como frontera (menor que, mayor que)
    const PLAZA_ASIGNADA = 4; // Asignada.
    const PLAZA_CONFIRMADA = 5; // Confirmada.

    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */


    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /* Ahora lo hago con:
     * 		$oAsistentePub = new AsistentePub();
	 *		$oAsistente = $oAsistentePub->getClaseAsistente($Qid_nom,$Qid_activ);
	 *		$oAsistente->setPrimary_key(array('id_activ'=>$Qid_activ,'id_nom'=>$Qid_nom));
	 *		$oAsistente->DBCarregar();
     * 
     */

    /*
	public function DBGuardar($quiet=0) {
		$aDades = $this->getAllAtributes();
		$id_tabla = $this->getId_tabla();
		switch ($id_tabla) {
			case 'dl':
				$oAsistente = new AsistenteDl($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				return $oAsistente->DBGuardar($quiet=0);
				break;
			case 'out':
				$oAsistente = new AsistenteOut($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				return $oAsistente->DBGuardar($quiet=0);
				break;
			case 'ex':
				$oAsistente = new AsistenteEx($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				return $oAsistente->DBGuardar($quiet=0);
				break;
			case 'in':
				echo _("el asistente es de otra dl. Se debe modificar en la dl origen.");
				break;
			default:
				echo _("no sé a que dl pertenece el asistente.");
				echo "tabla: $id_tabla<br>";
				break;
		}
	}
	*/

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    /*
	public function DBEliminar() {
		$id_tabla = $this->getId_tabla();
		switch ($id_tabla) {
			case 'dl':
				$oAsistente = new AsistenteDl($this->aPrimary_key);
				$oAsistente->DBEliminar();
				break;
			case 'out':
				$oAsistente = new AsistenteOut($this->aPrimary_key);
				$oAsistente->DBEliminar();
				break;
			case 'ex':
				$oAsistente = new AsistenteEx($this->aPrimary_key);
				$oAsistente->DBEliminar();
				break;
			case 'in':
				echo _("el asistente es de otra dl. Se debe modificar en la dl origen.");
				break;
		}
	}
	*/

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

}

?>
