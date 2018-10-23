<?php
namespace asistentes\model\entity;
use core;
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
 * Classe que implementa l'entitat av_asistentes
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class Asistente Extends AsistentePub {
	// tipo plaza constants.
	//1:pedida, 2:en espera, 3: denegada, 4:asignada, 5:confirmada
    const PLAZA_PEDIDA     = 1; // Pedida
    const PLAZA_EN_ESPERA   = 2; // En espera.
    const PLAZA_DENEGADA    = 3; // Denegada. De hecho ahora no se usa, pero sirve como frontera (menor que, mayor que)
    const PLAZA_ASIGNADA    = 4; // Asignada.
    const PLAZA_CONFIRMADA  = 5; // Confirmada.
	
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */


	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$aDades = $this->getAllAtributes();
		$id_tabla = $this->getId_tabla();
		switch ($id_tabla) {
			case 'dl':
				$oAsistente = new AsistenteDl($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				$oAsistente->DBGuardar();
				break;
			case 'out':
				$oAsistente = new AsistenteOut($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				$oAsistente->DBGuardar();
				break;
			case 'ex':
				$oAsistente = new AsistenteEx($this->aPrimary_key);
				$oAsistente->setAllAtributes($aDades);
				$oAsistente->DBGuardar();
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
	
	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
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

	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

}
?>
