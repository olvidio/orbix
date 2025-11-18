<?php

namespace actividadestudios\model\entity;

use core\ClasePropiedades;
use notas\model\entity\GestorPersonaNotaDB;
use src\notas\application\repositories\NotaRepository;

/**
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/11/2016
 */
class PosiblesCa extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Id_nom de posiblesCa
     *
     * @var integer
     */
    protected $id_nom;
    /**
     * asignaturas de posiblesCa
     *
     * @var array
     */
    protected $aAsignaturas;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */


    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     *
     * @param integer $id_nom
     * @param array $aAsignaturas id_asignatura => array(nombre_asignatura, creditos)
     * @return array    [ 'suma'=> suma creditos,  'lista' => array(id_asignatura => datosAsignatura) ]
     */
    function contar_creditos(int $id_nom, array $aAsignaturas)
    {
        $suma_creditos = 0;
        $NotaRepository = new NotaRepository();
        $aNotas = $NotaRepository->getArrayNotasSuperadas();
        $aSuperadas = [];
        foreach ($aNotas as $id_situacion) {
            $aSuperadas[$id_situacion] = 't';
        }
        $GesPersonaNotas = new GestorPersonaNotaDB();
        $cPersonaNotas = $GesPersonaNotas->getPersonaNotas(array('id_nom' => $id_nom));
        $num_opcionales = 0;
        $todas_asig_p = [];
        foreach ($cPersonaNotas as $oPersonaNota) {
            $id_situacion = $oPersonaNota->getId_situacion();
            $id_asignatura = $oPersonaNota->getId_asignatura();
            $id_nivel = $oPersonaNota->getId_nivel();
            if (array_key_exists($id_situacion, $aSuperadas)) {
                $todas_asig_p[] = $id_asignatura;
                // Apunto las opcionales a parte.
                if (substr($id_nivel, 0, 3) == '243' || substr($id_nivel, 0, 3) == '123') {
                    $num_opcionales++;
                }
            }
        }
        $aLista = [];
        foreach ($aAsignaturas as $id_asignatura => $datosAsignatura) {
            $creditos = $datosAsignatura['creditos'];
            // Ojo con las opcionales
            if ($id_asignatura > 3000) {
                if ($num_opcionales >= 7) continue;
            }
            if (!in_array($id_asignatura, $todas_asig_p)) {
                $suma_creditos += $creditos;
                $aLista [$id_asignatura] = $datosAsignatura;
            }
        }
        $result = ['suma' => $suma_creditos, 'lista' => $aLista];
        return $result;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
