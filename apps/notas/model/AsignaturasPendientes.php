<?php

namespace notas\model;

use core\ClasePropiedades;
use core\ConfigGlobal;
use asignaturas\model\entity\GestorAsignatura;
use function core\is_true;

/**
 * Classe que
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class AsignaturasPendientes extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * oDbl de Acta
     *
     * @var object
     */
    protected $oDbl;
    /**
     *
     *
     * @var string
     */
    protected $sNomPersonas;
    protected $sNomAsignaturas;

    protected $tablaNotas;

    /**
     * Lista. para indicar si devuelve la lista de nombres o sólo el número
     *
     * @var boolean
     */
    protected $blista;

    protected $iasignaturasB;
    protected $iasignaturasC;
    protected $iasignaturasC1;
    protected $iasignaturasC2;
    protected $aIdNivel;


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom,iid_nivel
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($personas = '')
    {
        $oDbl = $GLOBALS['oDB'];

        $this->setoDbl($oDbl);
        if (!empty($personas)) {
            $this->setNomPersonas($personas);
        }
        $this->setNomAsignaturas('tmp_xa_asignaturas');

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

    public function getAsignaturasB()
    {
        if (empty($this->iasignaturasB)) {
            $gesAsignaturas = new GestorAsignatura();
            $cAsignaturasB = $gesAsignaturas->getAsignaturas(array('status' => 't', 'id_nivel' => '1100,1300'), array('id_nivel' => 'BETWEEN'));

            $this->iasignaturasB = count($cAsignaturasB);
            $aIdNivel = [];
            foreach ($cAsignaturasB as $oAsignatura) {
                $aIdNivel[] = $oAsignatura->getId_nivel();
            }
            $this->aIdNivel = $aIdNivel;
        }
        return $this->iasignaturasB;
    }

    public function setAsignaturasB($asignaturasB)
    {
        $this->iasignaturasB = $asignaturasB;
    }

    public function getAsignaturasC()
    {
        if (empty($this->iasignaturasC)) {
            $gesAsignaturas = new GestorAsignatura();
            $cAsignaturasC = $gesAsignaturas->getAsignaturas(array('status' => 't', 'id_nivel' => '2100,2500'), array('id_nivel' => 'BETWEEN'));

            $this->iasignaturasC = count($cAsignaturasC);
            $aIdNivel = [];
            foreach ($cAsignaturasC as $oAsignatura) {
                $aIdNivel[] = $oAsignatura->getId_nivel();
            }
            $this->aIdNivel = $aIdNivel;
        }
        return $this->iasignaturasC;
    }

    public function setAsignaturasC($asignaturasC)
    {
        $this->iasignaturasC = $asignaturasC;
    }

    public function getAsignaturasC1()
    {
        if (empty($this->iasignaturasC1)) {
            $gesAsignaturas = new GestorAsignatura();
            $cAsignaturasC1 = $gesAsignaturas->getAsignaturas(array('status' => 't', 'id_nivel' => '2100,2113'), array('id_nivel' => 'BETWEEN'));
            $this->iasignaturasC1 = count($cAsignaturasC1);
            $aIdNivel = [];
            foreach ($cAsignaturasC1 as $oAsignatura) {
                $aIdNivel[] = $oAsignatura->getId_nivel();
            }
            $this->aIdNivel = $aIdNivel;
        }
        return $this->iasignaturasC1;
    }

    public function setAsignaturasC1($asignaturasC1)
    {
        $this->iasignaturasC1 = $asignaturasC1;
    }

    public function getAsignaturasC2()
    {
        if (empty($this->iasignaturasC2)) {
            $gesAsignaturas = new GestorAsignatura();
            $cAsignaturasC2 = $gesAsignaturas->getAsignaturas(array('status' => 't', 'id_nivel' => '2200,2500'), array('id_nivel' => 'BETWEEN'));
            $this->iasignaturasC2 = count($cAsignaturasC2);
            $aIdNivel = [];
            foreach ($cAsignaturasC2 as $oAsignatura) {
                $aIdNivel[] = $oAsignatura->getId_nivel();
            }
            $this->aIdNivel = $aIdNivel;
        }
        return $this->iasignaturasC2;
    }

    public function setAsignaturasC2($asignaturasC2)
    {
        $this->iasignaturasC2 = $asignaturasC2;
    }

    public function condicion($curso)
    {
        $num_curso = 0;
        switch ($curso) {
            case 'bienio':
                $num_curso = $this->getAsignaturasB();
                $condicion = "AND id_nivel IN (" . implode(',', $this->aIdNivel) . ")";
                $condicion_stgr = "AND p.stgr = 'b'";
                break;
            case 'cuadrienio':
                $num_curso = $this->getAsignaturasC();
                $condicion = "AND id_nivel IN (" . implode(',', $this->aIdNivel) . ")";
                $condicion_stgr = "AND p.stgr ~ '^c'";
                break;
            case 'c1':
                $num_curso = $this->getAsignaturasC1();
                $condicion = "AND id_nivel IN (" . implode(',', $this->aIdNivel) . ")";
                $condicion_stgr = "AND p.stgr = 'c1'";
                break;
            case 'c2':
                $num_curso = $this->getAsignaturasC2();
                $condicion = "AND id_nivel IN (" . implode(',', $this->aIdNivel) . ")";
                $condicion_stgr = "AND p.stgr = 'c2'";
                break;
        }
        return array('num' => (int)$num_curso, 'condicion' => $condicion, 'condicion_stgr' => $condicion_stgr);
    }

    public function personasQueLesFalta(int $num_asignaturas, $curso)
    {
        $lista = $this->blista;
        $oDbl = $this->getoDbl();
        $personas = $this->getNomPersonas();

        $aCondicion = $this->condicion($curso);
        $num_curso = $aCondicion['num'];
        $condicion = $aCondicion['condicion'];
        $condicion_stgr = $aCondicion['condicion_stgr'];
        //echo "num = $num_curso<br>";
        $num = $num_curso - $num_asignaturas;

        $ssql = "SELECT DISTINCT p.id_nom, Count(*) as asignaturas, p.apellido1, p.apellido2, p.nom
			FROM $personas p LEFT JOIN $this->tablaNotas n USING (id_nom)
			WHERE p.situacion='A'
			 $condicion $condicion_stgr
			GROUP BY  p.id_nom, p.apellido1, p.apellido2, p.nom
			HAVING Count(*) >= $num AND Count(*) < $num_curso
			";

        // Si $num =< 0, hay que sumar los que no tienen ninguna asignatura:
        if ($num < 1) {
            $sql_0 = "SELECT p.id_nom, 0 as asignaturas, p.apellido1, p.apellido2, p.nom
			FROM $personas p LEFT JOIN $this->tablaNotas n USING (id_nom)
			WHERE p.situacion='A'
			     $condicion_stgr AND n.id_nom IS NULL
			GROUP BY  p.id_nom, p.apellido1, p.apellido2, p.nom
			";

            $sql = "($ssql) UNION ($sql_0) ORDER BY 3,4,5";
        } else {
            $sql = "$ssql ORDER BY 3,4,5";
        }

        $aId_nom = [];
        foreach ($oDbl->query($sql) as $row) {
            $id_nom = $row['id_nom'];
            if (!is_true($lista)) { // El numero de asignaturas que faltan
                $aId_nom[$id_nom] = $num_curso - $row['asignaturas'];
            } else { // El listado de asignaturas que faltan
                $aAsignaturas = $this->asignaturasQueFaltanPersona($id_nom, $curso);
                $aId_nom[$id_nom] = $aAsignaturas;
            }
        }
        return $aId_nom;
    }

    public function personasQueLesFaltaAsignatura($id_asignatura, $curso, $id_tipo_asignatura)
    {
        $aId_nom = [];
        if (!empty($id_asignatura)) {
            $lista = $this->blista;
            $oDbl = $this->getoDbl();
            $personas = $this->getNomPersonas();

            $aCondicion = $this->condicion($curso);
            $condicion_stgr = $aCondicion['condicion_stgr'];

            $query = "SELECT p.id_nom
					FROM $personas p 
					WHERE p.situacion = 'A' $condicion_stgr
					";
            if ($id_tipo_asignatura == 8) { // opcional
                $query_op = "SELECT n.id_nom
					FROM $this->tablaNotas n
					WHERE n.id_nivel = $id_asignatura";
            } else {
                $query_op = "SELECT n.id_nom
					FROM $this->tablaNotas n
					WHERE n.id_asignatura = $id_asignatura";
            }
            $query_tot = "$query EXCEPT $query_op";
            //echo "$query_tot<br>";
            $a_id_noms = $oDbl->query($query_tot);
            foreach ($a_id_noms as $row) {
                $id_nom = $row['id_nom'];
                if (!is_true($lista)) { // El numero de asignaturas que faltan
                    $aId_nom[$id_nom]++;
                } else {
                    $aId_nom[$id_nom] = 'si';
                }
            }
        }
        return $aId_nom;
    }

    public function asignaturasQueFaltanPersona($id_nom, $curso)
    {
        $oDbl = $this->getoDbl();
        $asignaturas = $this->getNomAsignaturas();
        $this->createAsignaturas(); // crear tabla temporal asignaturas

        $aCondicion = $this->condicion($curso);
        $num_curso = $aCondicion['num'];
        $condicion = $aCondicion['condicion'];

        $query = "SELECT a.nombre_corto, Notas.id_asignatura
				FROM $asignaturas a LEFT JOIN (SELECT id_asignatura from $this->tablaNotas where id_nom=$id_nom and id_asignatura < 3000) AS Notas USING (id_asignatura)
				WHERE a.id_tipo != 8 AND Notas.id_asignatura is null
				$condicion
				";
        $query_op = "SELECT a.nombre_corto, Notas.id_nivel
				FROM $asignaturas a LEFT JOIN (SELECT id_nivel from $this->tablaNotas where id_nom=$id_nom and id_asignatura > 3000) AS Notas USING (id_nivel)
				WHERE a.id_tipo = 8 AND Notas.id_nivel is null
				$condicion
				";
        $query_tot = "$query UNION $query_op  ORDER BY 2";
        //echo "query asig: $query_tot<br>";

        $a_nomAsignaturas = [];
        foreach ($oDbl->query($query_tot) as $asig) {
            $a_nomAsignaturas[] = $asig["nombre_corto"];
        }
        return $a_nomAsignaturas;
    }

    public function createAsignaturas()
    {
        //Como ahora las asignaturas estan en otra base de datos(comun) hago una copia para poder hacer unions...
        $oDbl = $this->getoDbl();
        $asignaturas = $this->getNomAsignaturas();

        // No hace falta DELETE si pongo TEMP. (Por alguna razon SI existe)
        $sqlDelete = "DROP TABLE IF EXISTS $asignaturas";
        $sqlCreate = "CREATE TEMP TABLE $asignaturas(
						id_asignatura integer,
						id_nivel integer,
						nombre_asignatura character varying(100) NOT NULL,
						nombre_corto character varying(23),
						creditos numeric(4,2),
						year character varying(3),
						id_sector smallint,
						status boolean DEFAULT true NOT NULL,
						id_tipo integer
					 )";


        $oDbl->query($sqlDelete);
        $oDbl->query($sqlCreate);
        $oDbl->query("CREATE INDEX IF NOT EXISTS $asignaturas" . "_nivel" . " ON $asignaturas (id_nivel)");
        $oDbl->query("CREATE INDEX IF NOT EXISTS $asignaturas" . "_id_asignatura" . " ON $asignaturas (id_asignatura)");

        $gesAsignaturas = new GestorAsignatura();
        $cAsignaturas = $gesAsignaturas->getAsignaturas(array('status' => 'true'));

        $prep = $oDbl->prepare("INSERT INTO $asignaturas VALUES(:id_asignatura, :id_nivel, :nombre_asignatura, :nombre_corto, :creditos, :year, :id_sector, :status, :id_tipo)");
        foreach ($cAsignaturas as $oAsignatura) {
            $aDades = [];
            $aDades['id_asignatura'] = $oAsignatura->getId_asignatura();
            $aDades['id_nivel'] = $oAsignatura->getId_nivel();
            $aDades['nombre_asignatura'] = $oAsignatura->getNombre_asignatura();
            $aDades['nombre_corto'] = $oAsignatura->getNombre_corto();
            $aDades['creditos'] = $oAsignatura->getCreditos();
            $aDades['year'] = $oAsignatura->getYear();
            $aDades['id_sector'] = $oAsignatura->getId_sector();
            $aDades['status'] = $oAsignatura->getStatus();
            $aDades['id_tipo'] = $oAsignatura->getId_tipo();

            $prep->execute($aDades);
        }
    }
}
