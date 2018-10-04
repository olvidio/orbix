<?php
namespace usuarios\model;
use core;

/* No vale el underscore en el nombre */
class InfoLocales extends core\datosInfo {

	public function __construct() {
		$this->setTxtTitulo(_("idiomas posibles para la aplicación"));
		$this->setTxtEliminar();
		$this->setTxtBuscar(_("idioma a buscar"));
		$this->setTxtExplicacion();
		
		$this->setClase('usuarios\\model\\entity\\Local');
		$this->setMetodoGestor('getLocales');
	}
	
	public function getColeccion() {
		// para el datos_sql.php
		// Si se quiere listar una selección, $this->k_buscar
		if (empty($this->k_buscar)) {
			$aWhere=array();
			$aOperador=array();
		} else {
			$aWhere=array('nom_idioma' => $this->k_buscar);
			$aOperador=array('nom_idioma' => 'sin_acentos');
		}
		$aWhere['_ordre'] = 'activo DESC,nom_idioma ASC';
		$oLista = new entity\GestorLocal();
		$Coleccion = $oLista->getLocales($aWhere,$aOperador);

		return $Coleccion;
	}
}
