<?php

namespace notas\model;

use core\ClasePropiedades;
use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\notas\application\services\ResumenTempTablesService;
use src\notas\domain\contracts\NotaRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Resumen extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Lista. para indicar si devuelve la lista de nombres o sólo el número
     *
     * @var boolean
     */
    protected bool $blista;
    /**
     *
     * @var array
     */
    protected array $a_dl;

    protected $dinicurso;
    protected $dfincurso;
    protected $iany;
    protected $iany2;
    protected $diniverano;
    protected $sce_lugar;

    protected $a_asignaturas;
    protected $a_creditos;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * NomTabla de Acta
     *
     * @var string
     */
    protected $sNomTabla;
    protected $sNomNotas;
    protected $sNomPersonas;
    protected $sNomAsignaturas;
    // para las cr, se mira directamente en la table de 'e_notas', no 'e_notas_dl'.
    protected $tablaNotas;
    protected ResumenTempTablesService $tempTablesService;


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom,iid_nivel
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($nom = '')
    {
        $tabla = "tmp_est_" . $nom;
        $notas = "tmp_notas_" . $nom;
        $asignaturas = "tmp_asignaturas";
        switch ($nom) {
            case 'numerarios':
                $personas = "p_numerarios";
                break;
            case 'agd':
            case 'agregados':
                $personas = "p_agregados";
                break;
            case 'profesores':
                $personas = "personas_dl";
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }

        $this->setNomTabla($tabla);
        $this->setNomNotas($notas);
        $this->setNomAsignaturas($asignaturas);
        $this->setNomPersonas($personas);
        $this->tempTablesService = new ResumenTempTablesService();

        // En el caso cr-stgr, se consulta la tabla de notas
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $this->tablaNotas = 'publicv.e_notas';
        } else {
            $this->tablaNotas = 'e_notas_dl';
        }

    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getNomPersonas()
    {
        return $this->sNomPersonas;
    }

    public function setNomPersonas($personas)
    {
        $this->sNomPersonas = $personas;
    }

    public function getNomNotas()
    {
        return $this->sNomNotas;
    }

    public function setNomNotas($notas)
    {
        $this->sNomNotas = $notas;
    }

    public function getNomAsignaturas()
    {
        return $this->sNomAsignaturas;
    }

    public function setNomAsignaturas($asignaturas)
    {
        $this->sNomAsignaturas = $asignaturas;
    }

    public function getLista()
    {
        return $this->blista;
    }

    public function setLista($blista)
    {
        $this->blista = $blista;
    }

    public function getCe_lugar()
    {
        return $this->sce_lugar;
    }

    public function setCe_lugar($sce_lugar)
    {
        $this->sce_lugar = $sce_lugar;
    }

    public function getAnyIniCurs()
    {
        if (empty($this->iany)) {
            $this->iany = date("Y");
        }
        return $this->iany;
    }

    public function setAnyIniCurs($iany)
    {
        $this->iany = $iany;
    }

    public function getAnyFiCurs()
    {
        if (empty($this->iany2)) {
            $this->iany2 = (int)$this->getAnyIniCurs() + 1;
        }
        return $this->iany2;
    }

    public function setAnyFiCurs($iany2)
    {
        $this->iany = $iany2;
    }

    public function getIniCurso()
    {
        if (empty($this->dinicurso)) {
            $any = $this->getAnyIniCurs();
            $this->dinicurso = date("Y-m-d", mktime(0, 0, 0, 10, 1, $any));
        }
        return $this->dinicurso;
    }

    public function setIniCurso($dinicurso)
    {
        $this->dinicurso = $dinicurso;
    }

    public function getFinCurso()
    {
        if (empty($this->dfincurso)) {
            $any = $this->getAnyFiCurs();
            $this->dfincurso = date("Y-m-d", mktime(0, 0, 0, 9, 30, $any));
        }
        return $this->dfincurso;
    }

    public function setFinCurso($dfincurso)
    {
        $this->dfincurso = $dfincurso;
    }

    /* Pongo en la variable $curso el periodo del curso */
    public function getCurso()
    {
        $curso = "BETWEEN '" . $this->getIniCurso() . "' AND '" . $this->getFinCurso() . "' ";
        return $curso;
    }

    /*
     $tabla="tmp_est_numerarios";
     $personas="p_numerarios";
     $notas="tmp_notas_numerarios";
     */

    public function nuevaTabla()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $personas = $this->getNomPersonas();

        $curs = $this->getCurso();
        $fincurs = $this->getFinCurso();

        $any = (int)$this->getAnyIniCurs() + 1; //para los incorporados a partir del 1-jun.
        $this->tempTablesService->rebuildMainTable(
            $tabla,
            $personas,
            $fincurs,
            $curs,
            ConfigGlobal::mi_ambito() === 'rstgr',
            $this->getArrayDl() ?? []
        );

        // Busco los que han ido a un ci


        // Miro los que se han incorporado "recientemente": desde el 1-junio
        $ssql = "SELECT  p.nom, p.apellido1, p.apellido2, p.ctr, p.nivel_stgr
			FROM $tabla p
			WHERE p.situacion='A' AND p.f_situacion > '$any-6-1'
				AND (p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")) ";
        //echo "qry: $ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $nf = $statement->rowCount();
        if ($this->blista && $nf != 0) {
            echo "<p>Existen $nf Alumnos que se han incorporado \"recientemente\" (desde el 1-junio) a la dl<br>
					Sí se cuentan en la estadística.</p>";
            // Para sacar una lista
            echo $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        }

        // Miro si existe alguna excepción: Alguien incorporado a la dl después del 1 de OCT
        $ssql = "SELECT  p.nom, p.apellido1, p.apellido2, p.ctr, p.nivel_stgr
			FROM $tabla p
			WHERE p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
				AND (p.situacion='A' AND p.f_situacion > '$fincurs')";
        $statement = $this->tempTablesService->query($ssql);
        $nf = $statement->rowCount();

        if ($this->blista && $nf != 0) {
            echo "<p>Existen $nf alumnos que se han incorporado después del 1-OCT a la dl<br>
					No se van a contar</p>";
            // Para sacar una lista
            echo $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        }

        $this->tempTablesService->applyNivelUpdates(
            $tabla,
            $this->tablaNotas,
            $curs,
            NivelStgrId::B,
            NivelStgrId::C2
        );

        $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
        $a_superadas = $NotaRepository->getArrayNotasSuperadas();
        // Tengo que acceder a publicv, porque con los traslados las notas se cambian de esquema.
        if (ConfigGlobal::mi_sfsv() == 1) {
            $notas_vf = 'publicv.e_notas';
        }
        if (ConfigGlobal::mi_sfsv() == 2) {
            $notas_vf = 'publicf.e_notas';
        }
        $this->tempTablesService->rebuildNotasTable($notas, $tabla, $curs, $notas_vf, $a_superadas);

        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $cAsignaturas = $AsignaturaRepository->getAsignaturas(array('active' => 'true'));
        $this->tempTablesService->rebuildAsignaturasTable($asignaturas, $cAsignaturas);
    }

    public function ListaAsig($a_Asql, $statement)
    {
        // Para sacar una lista
        $html = "<table>";
        $id_nom = 0;
        $cont = 0; // para saber cuánta gente le queda
        $cont_asig = 0;
        $cont_nom = 0;
        $a_sql = $statement->fetchAll();
        foreach ($a_sql as $nombre) {
            $cont_nom++;
            // Si cambio de persona, vuelvo a empezar con las asignaturas
            if ($nombre["id_nom"] != $id_nom) {
                $cont_asig = 0;
                $cont++;
                $id_nom = $nombre["id_nom"];
                $nom_ap = $nombre["nom_ap"];
                $html .= "<tr><td colspan=2 class=titulo>$nom_ap</td></tr>";
            }
            if ($cont_asig >= 28) {
                $html .= "Pasa de 28 asignaturas";
            } else {
                $asig_nivel = $a_Asql[$cont_asig]['id_nivel'];
                $cont_asig++;
                while ($nombre['id_nivel'] > $asig_nivel) {

                    $asig_nivel = $a_Asql[$cont_asig]['id_nivel'];
                    $asig_nombre_corto = $a_Asql[$cont_asig]['nombre_corto'];

                    $html .= "<tr><td></td><td>$asig_nombre_corto</td></tr>";
                    $cont_asig++;
                    if ($cont_asig > 28) exit ("Pasa de 28 asignaturas!!");
                }
            }
            //miro si el siguiente registro es de la misma persona, sino, pongo las asignaturas que quedan hasta acabar el bienio
            if (count($a_sql) > $cont_nom) {
                $siguiente_id_nom = $a_sql[$cont_nom]['id_nom'];
                if ($siguiente_id_nom != $id_nom) {
                    while ($asig_nombre_corto = @$a_Asql[$cont_asig++]['nombre_corto']) {
                        $html .= "<tr><td></td><td>$asig_nombre_corto</td></tr>";
                    }
                    //$cont_asig=0;
                }
            }
        }

        $html .= "<tr><td colspan=7><hr>";
        $html .= "</table>";
        // end lista
        $html .= "<p>Total: $cont</p>";
        return $html;
    }


    public function Lista($sql, $campos, $cabecera)
    {
        // $campos es un string con los campos que se quiere listar, separados por comas
        $camp = explode(',', $campos);
        $html = "<table>";
        if (!empty($cabecera)) {
            $html .= "<tr><td width=20></td>";
            foreach ($camp as $key => $titulo) {
                $html .= "<th>$titulo</th>";
            }
            $html .= "</tr>";
            $p = reset($camp);
        }
        foreach ($this->tempTablesService->query($sql) as $fila => $valor) {
            $html .= "<tr><td width=20></td>";
            foreach ($camp as $key => $val) {
                $html .= "<td>$valor[$val]</td>";
            }
            $html .= "</tr>";
            $p = reset($camp);
        }
        if (empty($cabecera)) $html .= "<tr><td colspan=7><hr>";
        $html .= "</table>";

        return $html;
    }

    public function enBienio()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,ctr
		FROM $tabla p
		WHERE p.nivel_stgr=" . NivelStgrId::B . "
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function enCuadrienio($c = 'all')
    {
        $tabla = $this->getNomTabla();
        $where = '';
        switch ($c) {
            case 1:
                $where = "WHERE p.nivel_stgr=" . NivelStgrId::C1;
                break;
            case 2:
                $where = "WHERE p.nivel_stgr=" . NivelStgrId::C2;
                break;
            case 'all':
                $where = "WHERE p.nivel_stgr IN (" . NivelStgrId::C1 . "," . NivelStgrId::C2 . ")";
                break;
        }
        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM $tabla p
				$where
				ORDER BY p.apellido1,p.apellido2,p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function enRepaso()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
		FROM $tabla p
		WHERE p.nivel_stgr=" . NivelStgrId::R . "
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function enTotal()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr,p.nivel_stgr
		FROM $tabla p
		WHERE p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function enStgrSinO()
    {
        $iniverano = $this->diniverano;
        $tabla = $this->getNomTabla();

        /*
         $ssql="SELECT p.nom, p.apellido1, p.apellido2
         FROM $tabla p
         WHERE p.f_fl IS NULL
         AND (p.nivel_stgr='b' OR p.nivel_stgr ILIKE 'c%') AND (p.f_o > '$iniverano' OR p.f_o IS NULL)
         ORDER BY p.apellido1,p.apellido2,p.nom
         ";
         
         $statement = $this->tempTablesService->query($ssql);
         $rta['num'] = $statement->rowCount();
         if (is_true($this->blista) && $rta['num'] > 0) {
         $rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2",1);
         } else {
         $rta['lista'] = '';
         }
         return $rta;
         */
        return array('num' => '?', 'lista' => 'falta poner fecha o en tablas');
    }

    /**
     * Numerarios
     * Incluye a los que han empezado este curso, y los que han terminado este curso
     * Debe contar también a los que se han ido a otras dl.
     *
     * @return array
     */
    public function enCe()
    {
        //$tabla = $this->getNomTabla();
        $tabla = 'p_numerarios';
        //$ce_lugar = $this->getCe_lugar();
        $any = $this->getAnyFiCurs();

        $rta = [];
        // curso anterior
        /*        $ssql="SELECT p.id_nom, p.apellido1, p.apellido2, p.nom
         FROM $tabla p
         WHERE p.ce_lugar = '$ce_lugar'
         AND p.ce_ini IS NOT NULL AND (p.ce_fin IS NULL OR p.ce_fin = '$any')
         AND (p.situacion = 'A' OR p.situacion = 'D')
         ORDER BY p.apellido1,p.apellido2,p.nom  ";
         */

        // En el caso cr-stgr, añado la dl al nombre ubi.
        $where_dl = '';
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            // Si tengo dl filtro por dl.
            if (!empty($this->getArrayDl())) {
                $dl_csv = implode("','", $this->getArrayDl());
                $where_dl = "p.dl IN ('$dl_csv') AND";
            }
        }
        $ssql = "SELECT p.id_nom, p.apellido1, p.apellido2, p.nom, p.ce_lugar
            FROM $tabla p
            WHERE $where_dl p.ce_lugar IS NOT NULL
               AND p.ce_ini IS NOT NULL AND (p.ce_fin IS NULL OR p.ce_fin = '$any')
               AND (p.situacion = 'A' OR p.situacion = 'D' OR p.situacion = 'L')
            ORDER BY p.apellido1,p.apellido2,p.nom  ";
        $statement = $this->tempTablesService->query($ssql);
        $nf = $statement->rowCount();
        if ($nf >= 1) {
            $rta['error'] = true;
            $rta['num'] = $nf;
            if (is_true($this->blista) && $rta['num'] > 0) {
                $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ce_lugar", 1);
            } else {
                $rta['lista'] = '';
            }
            return $rta;
        }

        return array('num' => 0, 'lista' => '');
    }

    public function sinCe()
    {
        $tabla = $this->getNomTabla();
        //$ce_lugar = $this->getCe_lugar();
        //$any = $this->getAnyFiCurs();

        $ssql = "SELECT p.nom, p.apellido1, p.apellido2, p.ctr
            FROM $tabla p
            WHERE (p.nivel_stgr=" . NivelStgrId::B . ")
                AND (p.ce_lugar IS NULL OR p.ce_lugar = '')
            ORDER BY p.apellido1,p.apellido2,p.nom
            ";

        //echo "sql: $ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function aprobadasCe()
    {
        //$tabla = $this->getNomTabla();
        $tabla = 'p_numerarios';
        //$notas = $this->getNomNotas();
        if (ConfigGlobal::mi_sfsv() == 1) {
            $notas_vf = 'publicv.e_notas';
        }
        if (ConfigGlobal::mi_sfsv() == 2) {
            $notas_vf = 'publicf.e_notas';
        }
        $ce_lugar = $this->getCe_lugar();
        $a_lugares = explode(',', $ce_lugar);
        $condiciones = [];
        foreach ($a_lugares as $lugar) {
            // Limpiar espacios en blanco alrededor de cada valor
            $lugar_limpio = trim($lugar);
            $condiciones[] = "p.ce_lugar LIKE '%" . $lugar_limpio . "%'";
        }
        $where_clause = implode(' OR ', $condiciones);
        $any = $this->getAnyFiCurs();
        $curs = $this->getCurso();

        $NotaRepository = $GLOBALS['container']->get(NotaRepositoryInterface::class);
        $a_superadas = $NotaRepository->getArrayNotasSuperadas();
        $Where_superada = "AND id_situacion IN (" . implode(',', $a_superadas) . ")";
        /*
        $ssql="SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
                AND (p.ce_lugar = '$ce_lugar' AND p.ce_ini IS NOT NULL AND (p.ce_fin IS NULL OR p.ce_fin = '$any'))
                AND (p.situacion = 'A' OR p.situacion = 'D' OR p.situacion = 'L')
			";
        */
        $ssql = "SELECT count(*)
            FROM $tabla p, $notas_vf n
			WHERE p.id_nom=n.id_nom
				AND n.f_acta $curs
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
                AND (($where_clause) AND p.ce_ini IS NOT NULL AND (p.ce_fin IS NULL OR p.ce_fin = '$any'))
                AND (p.situacion = 'A' OR p.situacion = 'D' OR p.situacion = 'L')
                $Where_superada
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->fetchColumn();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = '';
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function aprobadasSinCe()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        //$ce_lugar = $this->getCe_lugar();
        //$any = $this->getAnyFiCurs();

        $ssql = "SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
				AND (p.ce_lugar ISNULL OR p.ce_lugar = '')
			 	AND (p.nivel_stgr=" . NivelStgrId::B . ")
			";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->fetchColumn();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = '';
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /**
     * personas con nivel_stgr != 'b' y con FinBienio = NULL
     *
     * @return array
     */
    public function bienioSinAcabar()
    {
        $tabla = $this->getNomTabla();

        $rta = [];
        $ssql = " SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
                FROM $tabla p
                WHERE p.nivel_stgr IN (".NivelStgrId::C1.", " . NivelStgrId::C2 . ")
                EXCEPT
                SELECT t.id_nom, t.nom, t.apellido1, t.apellido2, t.ctr
                FROM $tabla t JOIN $this->tablaNotas n USING (id_nom)
                WHERE n.id_nivel=9999
                ORDER BY 3
                ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }


    /**
     *
     * @return array
     */
    public function ceAcabadoEnBienio()
    {
        //$ce_lugar = $this->getCe_lugar();
        $any = $this->getAnyFiCurs();
        $tabla = $this->getNomTabla();

        $rta = [];
        $ssql = "SELECT p.id_nom, p.apellido1, p.apellido2, p.nom, p.ctr, p.nivel_stgr
            FROM $tabla p
            WHERE  p.ce_fin < '$any' AND p.ce_lugar IS NOT NULL AND p.nivel_stgr = " . NivelStgrId::B . "
            ORDER BY p.apellido1,p.apellido2,p.nom  ";
        $statement = $this->tempTablesService->query($ssql);
        $nf = $statement->rowCount();
        if ($nf >= 1) {
            $rta['error'] = true;
            $rta['num'] = $nf;
            if (is_true($this->blista) && $rta['num'] > 0) {
                $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
            } else {
                $rta['lista'] = '';
            }
            return $rta;
        }
        return array('num' => 0, 'lista' => '');
    }


    public function aprobadasBienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        $ssql = "SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr, a.nombre_corto, n.acta, n.preceptor
				FROM $tabla p,$notas n, $asignaturas a
				WHERE p.id_nom=n.id_nom AND n.id_asignatura=a.id_asignatura
					AND (n.id_nivel BETWEEN 1100 AND 1232)
					AND p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::BC . ")
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = sprintf(_("total de asignaturas superadas en bienio %s"), $rta['num']);
            $rta['lista'] .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nombre_corto,acta,preceptor", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function aprobadasCuadrienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        /*
         //Miro que no exista nadie de repaso que haya cursado alguna asignatura
         // fecha acabado bienio > fecha ultima asignatura
         $ssql="SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
         FROM $tabla p,$notas n
         WHERE p.id_nom=n.id_nom
         AND (n.id_nivel BETWEEN 2100 AND 2500)
         AND p.nivel_stgr='r'
         GROUP BY p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
         ORDER BY p.apellido1, p.apellido2, p.nom
         ";
         $statement=$this->tempTablesService->query($ssql);
         $nf=$statement->rowCount();
         if ($nf >= 1){
         $rta['error'] = true;
         $rta['num'] = $nf;
         if (is_true($this->blista) && $rta['num'] > 0) {
         $rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2,ctr",1);
         } else {
         $rta['lista'] = '';
         }
         return $rta;
         }
         */
        //		$ssql="SELECT count(*)
        //				FROM $notas n
        //				WHERE n.id_nivel BETWEEN 2100 AND 2500
        //				 ";
        //		$statement=$this->tempTablesService->query($ssql);
        //		$rta['num'] = $statement->fetchColumn();
        $ssql = "SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr, a.nombre_corto, n.acta, n.preceptor
				FROM $tabla p,$notas n, $asignaturas a
				WHERE p.id_nom=n.id_nom AND n.id_asignatura=a.id_asignatura
					AND (n.id_nivel BETWEEN 2100 AND 2500)
					AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
				ORDER BY p.apellido1, p.apellido2, p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = sprintf(_("total de asignaturas superadas en cuadrienio %s"), $rta['num']);
            $rta['lista'] .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nombre_corto,acta,preceptor", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // cuadrienio
    public function masAsignaturasQue($numAsig = 10)
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p,$notas n,$asignaturas a
		WHERE p.id_nom=n.id_nom AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ") AND n.id_asignatura=a.id_asignatura
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		HAVING count(*) > $numAsig
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

        //echo "qry: $ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function masCreditosQue($creditos = '28.5')
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p,$notas n,$asignaturas a
		WHERE p.id_nom=n.id_nom AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ") AND n.id_asignatura=a.id_asignatura
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) > $creditos
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

        //echo "qry: $ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function menosAsignaturasQue($numASig = 5)
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        $ssql = "SELECT n.id_nom,p.nom, p.apellido1,p.apellido2,p.ctr
		FROM $tabla p, $notas n, $asignaturas a
		WHERE p.id_nom=n.id_nom AND  n.id_nivel=a.id_nivel
			AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ", " . NivelStgrId::R . ") 
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom,p.nom, p.apellido1,p.apellido2, p.ctr
		HAVING count(*) <= $numASig
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function menosCreditosQue($creditos = '14')
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();

        $ssql = "SELECT n.id_nom,p.nom, p.apellido1,p.apellido2,p.ctr
		FROM $tabla p, $notas n, $asignaturas a
		WHERE p.id_nom=n.id_nom AND  n.id_nivel=a.id_nivel
			AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ", " . NivelStgrId::R . ") 
			AND (n.id_nivel BETWEEN 2100 AND 2500)
		GROUP BY n.id_nom,p.nom, p.apellido1,p.apellido2, p.ctr
		HAVING SUM( CASE WHEN n.id_nivel < 2430 THEN a.creditos else 1 END) <= $creditos
		ORDER BY p.apellido1,p.apellido2,p.nom  ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function ningunaSuperada()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p LEFT JOIN $notas n USING (id_nom)
		WHERE p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ") 
			AND n.id_nom IS NULL
		ORDER BY p.apellido1,p.apellido2,p.nom
		";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function conPreceptorBienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2,p.ctr
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t'
			AND p.nivel_stgr = " . NivelStgrId::B . "
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1,p.apellido2,p.nom ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function conPreceptorCuadrienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2,p.ctr
		FROM $notas n, $tabla p
		WHERE n.id_nom=p.id_nom AND n.preceptor='t'
			AND p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1,p.apellido2,p.nom ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function terminadoCuadrienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $curs = $this->getCurso();

        $ssql = "SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		FROM $tabla p, $notas n
		WHERE p.id_nom=n.id_nom
			AND (n.id_nivel=9998) AND n.f_acta $curs
		GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
		ORDER BY p.apellido1, p.apellido2,p.nom";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /**
     * Gente que está de repaso (strg='r') no sacd, y
     * que no haya termiinado este curso.
     *
     * @return array
     */
    public function laicosConCuadrienio()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $curs = $this->getCurso();

        $ssql = "SELECT pp.id_nom,pp.nom, pp.apellido1, pp.apellido2, pp.ctr
			FROM $tabla pp
			WHERE pp.nivel_stgr=" . NivelStgrId::R . " AND pp.sacd='f'
			ORDER BY pp.apellido1, pp.apellido2, pp.nom";

        /*
         $sql2="SELECT n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
         FROM $tabla p, $notas n
         WHERE p.id_nom=n.id_nom
         AND (n.id_nivel=9998) AND n.f_acta $curs
         GROUP BY n.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
         
         ORDER BY p.apellido1, p.apellido2,p.nom";
         
         $ssql = "( $sql1 ) EXCEPT ( $sql2 ) ORDER BY 3,4,2";
         */

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // -------------------- Profesores ------------------------------------------

    /*
     * Posibles Profesores
     * posibles profesores (n y agd)
     * posibles profesores asociados (añado s y sss+)
     */
    public function nuevaTablaProfe()
    {
        $tabla = $this->getNomTabla();
        $personas = $this->getNomPersonas();

        $this->tempTablesService->rebuildProfesorTable($tabla, $personas);

        /*
         try {
         $this->tempTablesService->query($sqlCreate);
         $this->tempTablesService->query("CREATE INDEX IF NOT EXISTS $tabla"."_id_nom"." ON $tabla (id_nom)");
         } catch (\PDOException $e) {
         echo $e->getMessage();
         $stmt = $this->tempTablesService->prepare($sqlDelete);
         $stmt->execute();
         echo 'The number of row(s) deleted: ' . $deletedRows . '<br>';
         }
         *
         */
    }

    public function profesorDeTipo($id_tipo = 0)
    {
        $tabla = $this->getNomTabla();

        $where_tipo = '';
        if ($id_tipo > 0) {
            $where_tipo = "id_tipo_profesor=$id_tipo AND";
        }
        $ssql = "SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE $where_tipo f_cese is null";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function profesorDeLatin()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_latin JOIN $tabla p USING (id_nom)
				WHERE latin='t'";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    public function arrayProfesorDepartamento()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT p.id_nom,d.id_departamento
				FROM $tabla p JOIN d_profesor_stgr d USING(id_nom)
				WHERE d.f_cese is null";
        $statement = $this->tempTablesService->prepare($ssql);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    /*44. Número de profesores que dieron clase de su especialidad*/
    public function profesorEspecialidad($otras = FALSE)
    {
        $any = $this->getAnyFiCurs();
        $curso_inicio = $any - 1;
        $SectorRepository = $GLOBALS['container']->get(SectorRepositoryInterface::class);
        $a_sectores = $SectorRepository->getArraySectoresPorDepartamento();
        $asignaturas = $this->getNomAsignaturas();
        $a_profe_dept = $this->arrayProfesorDepartamento();
        $docencia_dep = [];
        $docencia_no_dep = [];
        $nombres = [];
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        foreach ($a_profe_dept as $row) {
            $id_nom = $row['id_nom'];
            $id_departamento = $row['id_departamento'];
            // asignaturas (sector) por profesor. No contar las preceptuaciones
            $ssql = "SELECT DISTINCT d.id_nom,d.id_activ,a.id_sector,a.nombre_corto"
                . " FROM d_docencia_stgr d JOIN $asignaturas a USING (id_asignatura)"
                . " WHERE d.id_nom=$id_nom AND curso_inicio=$curso_inicio AND d.tipo != 'p'";
            //echo "sql: $ssql<br>";
            foreach ($this->tempTablesService->query($ssql) as $row2) {
                $id_nom = $row2['id_nom'];
                $id_activ = $row2['id_activ'];
                $id_sector = $row2['id_sector'];
                $nombre_corto = $row2['nombre_corto'];
                if (in_array($id_sector, $a_sectores[$id_departamento])) {
                    $docencia_dep[$id_nom] = 1;
                } else {
                    $docencia_no_dep[$id_nom] = 1;
                }

                if (is_true($this->blista)) {
                    $oPersonaDl = $PersonaDlRepository->findById($id_nom);
                    $nom = $oPersonaDl->getNom();
                    $apellido1 = $oPersonaDl->getApellido1();
                    $apellido2 = $oPersonaDl->getApellido2();

                    $nom_activ = '';
                    if (!empty($id_activ)) {
                        // En el caso cr-stgr, se consulta la tabla global.
                        if (ConfigGlobal::mi_region() === ConfigGlobal::mi_delef()) {
                            $oActividad = $ActividadAllRepository->findById($id_activ);
                        } else {
                            $oActividad = $ActividadAllRepository->findById($id_activ);
                        }
                        $nom_activ = $oActividad->getNom_activ();
                    }
                    $nombres[$id_nom] = array('nom' => $nom,
                        'apellido1' => $apellido1,
                        'apellido2' => $apellido2,
                        'asignatura' => $nombre_corto,
                        'actividad' => $nom_activ);
                }
            }
        }
        if ($otras) {
            $rta['num'] = count($docencia_no_dep);
            $a_docencia = $docencia_no_dep;
        } else {
            $rta['num'] = count($docencia_dep);
            $a_docencia = $docencia_dep;
        }

        if (is_true($this->blista) && $rta['num'] > 0) {
            //$rta['lista'] = $this->Lista($ssql,"nom,apellido1,apellido2",1);
            $camp = explode(',', 'nom,apellido1,apellido2,asignatura,actividad');
            $html = "<table>";
            $html .= "<tr><td width=20></td>";
            foreach ($camp as $key => $titulo) {
                $html .= "<th>$titulo</th>";
            }
            $html .= "</tr>";
            $p = reset($camp);
            foreach ($a_docencia as $id_nom => $valor) {
                $html .= "<tr><td width=20></td>";
                foreach ($camp as $key => $val) {
                    $data = $nombres[$id_nom][$val];
                    $html .= "<td>$data</td>";
                }
                $html .= "</tr>";
                $p = reset($camp);
            }
            $html .= "<tr><td colspan=7><hr>";
            $html .= "</table>";

            $rta['lista'] = $html;
        } else {
            $rta['lista'] = '';
        }

        return $rta;
    }

    /*42. Número de profesores asistentes a congresos...*/
    public function ProfesorCongreso()
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $curs = $this->getCurso();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2, p.ctr
				FROM d_congresos JOIN $tabla p USING (id_nom) WHERE f_ini $curs ";
        //echo "$ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Profesores de Bienio.
    public function ProfesoresEnBienio()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE f_cese is null AND id_departamento=1
				ORDER BY p.apellido1,p.apellido2,p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Profesores de Cuadrienio.
    public function ProfesoresEnCuadrienio()
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE f_cese is null AND id_departamento!=1
				ORDER BY p.apellido1,p.apellido2,p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (is_true($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Numero de departamentos con director
    public function Departamentos()
    {
        $tabla = $this->getNomTabla();

        $ProfesorDirectorRepository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
        $cDirectores = $ProfesorDirectorRepository->getProfesoresDirectores(array('f_cese' => 1), array('f_cese' => 'IS NULL'));
        $DepartamentoRepository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);

        $rta['num'] = count($cDirectores);
        if (is_true($this->blista) && $rta['num'] > 0) {
            $html = '<table>';
            foreach ($cDirectores as $oDirector) {
                $id_departamento = $oDirector->getId_departamento();
                $id_nom = $oDirector->getId_nom();
                $nom_dep = $DepartamentoRepository->findById($id_departamento)?->getNombreDepartamentoVo()->value();
                $oPersonaDl = $PersonaDlRepository->findById($id_nom);
                if ($oPersonaDl === null) {
                    continue;
                }
                $nom_persona = $oPersonaDl->getPrefApellidosNombre();
                $html .= "<tr><td>$nom_dep</td><td>$nom_persona</td></tr>";
            }
            $html .= '</table>';
            $rta['lista'] = $html;
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /**
     * @return mixed
     */
    public function getArrayDl()
    {
        return $this->a_dl;
    }

    /**
     * @param mixed $sdl
     */
    public function setArrayDl($dl)
    {
        $this->a_dl = $dl;
    }

}
