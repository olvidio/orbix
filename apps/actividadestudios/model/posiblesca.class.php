<?php
namespace actividadestudios\model;
use core;
use notas\model as notas;
/**
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/11/2016
 */
class PosiblesCa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Id_nom de posiblesCa
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * asignaturas de posiblesCa
	 *
	 * @var array
	 */
	 protected $aasignaturas;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */


	/* METODES PUBLICS ----------------------------------------------------------*/
	
	function contar_creditos($id_nom,$asignaturas) {
		$suma_creditos=0;
		$GesNotas = new notas\GestorNota();
		$cNotas = 	$GesNotas->getNotas(array('superada'=>'t'));
		$aSuperadas = array();
		foreach ($cNotas as $oNota) {
			$id_situacion = $oNota->getId_situacion();
			$aSuperadas[$id_situacion] = 't';
		}
		$GesPersonaNotas = new notas\GestorPersonaNota();
		$cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom'=>$id_nom));
		$a=0;
		$todas_asig_p = array();
		foreach ($cPersonaNotas as $oPersonaNota) {
			$id_situacion = $oPersonaNota->getId_situacion();
			$id_asignatura = $oPersonaNota->getId_asignatura();
			if (array_key_exists($id_situacion,$aSuperadas)) {
				$todas_asig_p[]=$id_asignatura;
			}
		}
		foreach( $asignaturas as $id_asignatura => $creditos ) {
			if (!in_array( $id_asignatura, $todas_asig_p)) { $suma_creditos += $creditos; }
		}
		return $suma_creditos;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
