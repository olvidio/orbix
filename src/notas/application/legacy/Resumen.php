<?php

namespace src\notas\application\legacy;


use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\notas\application\services\ResumenTempTablesService;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\StoresPdoErrorTxt;

/**
 * @phpstan-type ResumenRta array{num: int|float|string, lista: string}
 * @phpstan-type ResumenCeRta array{num: int, lista: string, error?: bool}
 */
class Resumen
{

    use HandlesPdoErrors;
    use StoresPdoErrorTxt;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Lista. para indicar si devuelve la lista de nombres o sólo el número
     *
     * @var boolean
     */
    protected bool $blista;
    /** @var array<int, int|string> */
    protected array $a_dl = [];

    protected string $dinicurso = '';
    protected string $dfincurso = '';
    protected int $iany = 0;
    protected int $iany2 = 0;
    protected string $sce_lugar = '';

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * NomTabla de Acta
     *
     * @var string
     */
    protected string $sNomTabla = '';
    protected string $sNomNotas = '';
    protected string $sNomPersonas = '';
    protected string $sNomAsignaturas = '';
    /**
     * Notas del expediente del alumno (todas las DL): padre `publicv/f.e_notas`.
     * La mayoría de indicadores STGR cuentan por persona, da igual dónde se cursó.
     */
    protected string $tablaNotas = '';
    /**
     * Notas solo de la DL de sesión (`e_notas_dl`). Reservado para métricas
     * «qué se examinó aquí»; hoy el resumen de alumnos no lo usa.
     */
    protected string $tablaNotasDl = 'e_notas_dl';

    /**
     * Avisos HTML de incorporación (1-jun / 1-oct) generados en {@see nuevaTabla()}.
     * No se hacen echo: el endpoint `_data` debe devolver solo JSON.
     */
    protected string $sAvisosHtml = '';


    /* CONSTRUCTOR -------------------------------------------------------------- */

    public function __construct(
        string $nom,
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly SectorRepositoryInterface $sectorRepository,
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository,
        private readonly ProfesorDirectorRepositoryInterface $profesorDirectorRepository,
        private readonly DepartamentoRepositoryInterface $departamentoRepository,
        private readonly ResumenTempTablesService $tempTablesService,
    ) {
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
                throw new \InvalidArgumentException(sprintf(
                    'Resumen: tipo "%s" no soportado (esperaba numerarios|agregados|agd|profesores)',
                    $nom
                ));
        }

        $this->setNomTabla($tabla);
        $this->setNomNotas($notas);
        $this->setNomAsignaturas($asignaturas);
        $this->setNomPersonas($personas);

        $this->tablaNotas = $this->tablaNotasExpediente();
    }

    /**
     * Tabla padre de notas (expediente agregado), según sfsv.
     */
    protected function tablaNotasExpediente(): string
    {
        return ConfigGlobal::mi_sfsv() == 2 ? 'publicf.e_notas' : 'publicv.e_notas';
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getNomTabla(): string {
        return $this->sNomTabla;
    }

    public function setNomTabla(string $nomTabla): void {
        $this->sNomTabla = $nomTabla;
    }

    public function getNomPersonas(): string {
        return $this->sNomPersonas;
    }

    public function setNomPersonas(string $personas): void {
        $this->sNomPersonas = $personas;
    }

    public function getNomNotas(): string {
        return $this->sNomNotas;
    }

    public function setNomNotas(string $notas): void {
        $this->sNomNotas = $notas;
    }

    public function getNomAsignaturas(): string {
        return $this->sNomAsignaturas;
    }

    public function setNomAsignaturas(string $asignaturas): void {
        $this->sNomAsignaturas = $asignaturas;
    }

    public function getLista(): bool {
        return $this->blista;
    }

    public function setLista(bool $blista): void {
        $this->blista = $blista;
    }

    public function getAvisosHtml(): string
    {
        return $this->sAvisosHtml;
    }

    public function getCe_lugar(): string {
        return $this->sce_lugar;
    }

    public function setCe_lugar(string $sce_lugar): void {
        $this->sce_lugar = $sce_lugar;
    }

    public function getAnyIniCurs(): int {
        if (empty($this->iany)) {
            $this->iany = (int) date("Y");
        }
        return $this->iany;
    }

    public function setAnyIniCurs(int $iany): void {
        $this->iany = $iany;
    }

    public function getAnyFiCurs(): int {
        if (empty($this->iany2)) {
            $this->iany2 = (int)$this->getAnyIniCurs() + 1;
        }
        return $this->iany2;
    }

    public function setAnyFiCurs(int $iany2): void {
        $this->iany2 = $iany2;
    }

    public function getIniCurso(): string {
        if (empty($this->dinicurso)) {
            $any = $this->getAnyIniCurs();
            $ts = mktime(0, 0, 0, 10, 1, (int) $any);
            $this->dinicurso = $ts !== false ? date("Y-m-d", $ts) : '';
        }
        return $this->dinicurso;
    }

    public function setIniCurso(string $dinicurso): void {
        $this->dinicurso = $dinicurso;
    }

    public function getFinCurso(): string {
        if (empty($this->dfincurso)) {
            $any = $this->getAnyFiCurs();
            $ts = mktime(0, 0, 0, 9, 30, (int) $any);
            $this->dfincurso = $ts !== false ? date("Y-m-d", $ts) : '';
        }
        return $this->dfincurso;
    }

    public function setFinCurso(string $dfincurso): void {
        $this->dfincurso = $dfincurso;
    }

    /* Pongo en la variable $curso el periodo del curso */
    public function getCurso(): string {
        $curso = "BETWEEN '" . $this->getIniCurso() . "' AND '" . $this->getFinCurso() . "' ";
        return $curso;
    }

    /*
     $tabla="tmp_est_numerarios";
     $personas="p_numerarios";
     $notas="tmp_notas_numerarios";
     */

    public function nuevaTabla(): void
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $personas = $this->getNomPersonas();

        $curs = $this->getCurso();
        $fincurs = $this->getFinCurso();
        $this->sAvisosHtml = '';

        $any = (int)$this->getAnyIniCurs() + 1; //para los incorporados a partir del 1-jun.
        $this->tempTablesService->rebuildMainTable(
            $tabla,
            $personas,
            $fincurs,
            $curs,
            ConfigGlobal::mi_ambito() === 'rstgr',
            $this->getArrayDl()
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
            $this->sAvisosHtml .= "<p>Existen $nf Alumnos que se han incorporado \"recientemente\" (desde el 1-junio) a la dl<br>
					Sí se cuentan en la estadística.</p>";
            // Para sacar una lista
            $this->sAvisosHtml .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        }

        // Miro si existe alguna excepción: Alguien incorporado a la dl después del 1 de OCT
        $ssql = "SELECT  p.nom, p.apellido1, p.apellido2, p.ctr, p.nivel_stgr
			FROM $tabla p
			WHERE p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
				AND (p.situacion='A' AND p.f_situacion > '$fincurs')";
        $statement = $this->tempTablesService->query($ssql);
        $nf = $statement->rowCount();

        if ($this->blista && $nf != 0) {
            $this->sAvisosHtml .= "<p>Existen $nf alumnos que se han incorporado después del 1-OCT a la dl<br>
					No se van a contar</p>";
            // Para sacar una lista
            $this->sAvisosHtml .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        }

        $this->tempTablesService->applyNivelUpdates(
            $tabla,
            $this->tablaNotas,
            $curs,
            NivelStgrId::B,
            NivelStgrId::C2
        );

        $a_superadas = NotaSituacion::getArraySuperadas();
        // Expediente del alumno: todas las notas (padre), no solo la DL local.
        $this->tempTablesService->rebuildNotasTable($notas, $tabla, $curs, $this->tablaNotas, $a_superadas);

        $AsignaturaRepository = $this->asignaturaRepository;
        $cAsignaturasMap = [];
        foreach ([PlanEstudios::PLAN_1997, PlanEstudios::PLAN_2026] as $plan) {
            [$aWhere, $aOperador] = PlanEstudiosFilter::apply($plan, ['active' => 'true']);
            foreach ($AsignaturaRepository->getAsignaturas($aWhere, $aOperador) as $oAsignatura) {
                $cAsignaturasMap[$oAsignatura->getId_asignatura()] = $oAsignatura;
            }
        }
        $this->tempTablesService->rebuildAsignaturasTable($asignaturas, array_values($cAsignaturasMap));
    }

    public function Lista(string $sql, string $campos, int|string $cabecera): string
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
            if (!is_array($valor)) {
                continue;
            }
            $html .= "<tr><td width=20></td>";
            foreach ($camp as $key => $val) {
                $cell = $valor[$val] ?? '';
                $html .= '<td>' . (is_scalar($cell) ? (string) $cell : '') . '</td>';
            }
            $html .= "</tr>";
            $p = reset($camp);
        }
        if (empty($cabecera)) $html .= "<tr><td colspan=7><hr>";
        $html .= "</table>";

        return $html;
    }

    /** @return ResumenRta */


    public function enBienio(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,ctr
		FROM $tabla p
		WHERE p.nivel_stgr=" . NivelStgrId::B . "
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */
    public function enCuadrienio(int|string $c = 'all'): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function enRepaso(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
		FROM $tabla p
		WHERE p.nivel_stgr=" . NivelStgrId::R . "
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function enTotal(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr,p.nivel_stgr
		FROM $tabla p
		WHERE p.nivel_stgr IN (" . NivelStgrId::B . ", " . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
		ORDER BY p.apellido1,p.apellido2,p.nom
		";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /**
     * Numerarios
     * Incluye a los que han empezado este curso, y los que han terminado este curso
     * Debe contar también a los que se han ido a otras dl.
     *
     * @return ResumenCeRta
     */
    public function enCe(): array
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
            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista)) {
                $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ce_lugar", 1);
            } else {
                $rta['lista'] = '';
            }
            return $rta;
        }

        return array('num' => 0, 'lista' => '');
    }

    /** @return ResumenRta */


    public function sinCe(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function aprobadasCe(): array
    {
        //$tabla = $this->getNomTabla();
        $tabla = 'p_numerarios';
        $notas_vf = $this->tablaNotas;
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

        $a_superadas = NotaSituacion::getArraySuperadas();
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
        $num = $statement->fetchColumn();
        $rta['num'] = is_numeric($num) ? (int) $num : 0;
        $rta['lista'] = '';
        return $rta;
    }

    /** @return ResumenRta */
    public function aprobadasSinCe(): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();

        $ssql = "SELECT count(*)
			FROM $tabla p, $notas n
			WHERE p.id_nom=n.id_nom
				AND (n.id_nivel BETWEEN 1100 AND 1229 OR n.id_nivel BETWEEN 2100 AND 2429)
				AND (p.ce_lugar ISNULL OR p.ce_lugar = '')
			 	AND (p.nivel_stgr=" . NivelStgrId::B . ")
			";

        $statement = $this->tempTablesService->query($ssql);
        $num = $statement->fetchColumn();
        return [
            'num' => is_numeric($num) ? (int) $num : 0,
            'lista' => '',
        ];
    }

    /**
     * personas con nivel_stgr != 'b' y con FinBienio = NULL
     *
     * @return array
     */
    /** @return ResumenRta */

    public function bienioSinAcabar(): array
    {
        $tabla = $this->getNomTabla();

        $rta = [];
        $ssql = " SELECT p.id_nom, p.nom, p.apellido1, p.apellido2, p.ctr
                FROM $tabla p
                WHERE p.nivel_stgr IN (" . NivelStgrId::C1 . ", " . NivelStgrId::C2 . ")
                EXCEPT
                SELECT t.id_nom, t.nom, t.apellido1, t.apellido2, t.ctr
                FROM $tabla t JOIN $this->tablaNotas n USING (id_nom)
                WHERE n.id_nivel=9999
                ORDER BY 3
                ";

        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista)) {
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
    /** @return ResumenRta */

    public function ceAcabadoEnBienio(): array
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
            if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista)) {
                $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nivel_stgr", 1);
            } else {
                $rta['lista'] = '';
            }
            return $rta;
        }
        return array('num' => 0, 'lista' => '');
    }


    /** @return ResumenRta */



    public function aprobadasBienio(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = sprintf(_("total de asignaturas superadas en bienio %s"), $rta['num']);
            $rta['lista'] .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nombre_corto,acta,preceptor", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function aprobadasCuadrienio(): array
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
         if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = sprintf(_("total de asignaturas superadas en cuadrienio %s"), $rta['num']);
            $rta['lista'] .= $this->Lista($ssql, "nom,apellido1,apellido2,ctr,nombre_corto,acta,preceptor", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // cuadrienio
    /** @return ResumenRta */

    public function masAsignaturasQue(int $numAsig = 10): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $numAsig = (int)$numAsig;

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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */
    public function masCreditosQue(int|float|string $creditos = '28.5'): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $creditos = (float)$creditos;

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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function menosAsignaturasQue(int $numASig = 5): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $numASig = (int)$numASig;

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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */
    public function menosCreditosQue(int|float|string $creditos = '14'): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $asignaturas = $this->getNomAsignaturas();
        $creditos = (float)$creditos;

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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function ningunaSuperada(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function conPreceptorBienio(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function conPreceptorCuadrienio(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function terminadoCuadrienio(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
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
    /** @return ResumenRta */

    public function laicosConCuadrienio(): array
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
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
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
    public function nuevaTablaProfe(): void
    {
        $tabla = $this->getNomTabla();
        $personas = $this->getNomPersonas();

        $this->tempTablesService->rebuildProfesorTable($tabla, $personas);
    }

    /** @return ResumenRta */
    public function profesorDeTipo(int $id_tipo = 0): array
    {
        $tabla = $this->getNomTabla();
        $id_tipo = (int)$id_tipo;

        $where_tipo = '';
        if ($id_tipo > 0) {
            $where_tipo = "id_tipo_profesor=$id_tipo AND";
        }
        $ssql = "SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE $where_tipo f_cese is null";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /** @return ResumenRta */


    public function profesorDeLatin(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_latin JOIN $tabla p USING (id_nom)
				WHERE latin='t'";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function arrayProfesorDepartamento(): array
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
    /** @return ResumenRta */
    public function profesorEspecialidad(bool $otras = false): array
    {
        $any = $this->getAnyFiCurs();
        $curso_inicio = $any - 1;
        $SectorRepository = $this->sectorRepository;
        $a_sectores = $SectorRepository->getArraySectoresPorDepartamento();
        $asignaturas = $this->getNomAsignaturas();
        $a_profe_dept = $this->arrayProfesorDepartamento();
        $docencia_dep = [];
        $docencia_no_dep = [];
        $nombres = [];
        $ActividadAllRepository = $this->actividadAllRepository;
        $PersonaDlRepository = $this->personaDlRepository;
        foreach ($a_profe_dept as $row) {
            $id_nom = $row['id_nom'] ?? null;
            $id_departamento = $row['id_departamento'] ?? null;
            if (!is_int($id_nom) && !is_string($id_nom)) {
                continue;
            }
            $id_nom = (int) $id_nom;
            if (!is_int($id_departamento) && !is_string($id_departamento)) {
                continue;
            }
            $id_departamento = (int) $id_departamento;
            // asignaturas (sector) por profesor. No contar las preceptuaciones
            $ssql = "SELECT DISTINCT d.id_nom,d.id_activ,a.id_sector,a.nombre_corto"
                . " FROM d_docencia_stgr d JOIN $asignaturas a USING (id_asignatura)"
                . " WHERE d.id_nom=$id_nom AND curso_inicio=$curso_inicio AND d.tipo != 'p'";
            //echo "sql: $ssql<br>";
            foreach ($this->tempTablesService->query($ssql) as $row2) {
                if (!is_array($row2)) {
                    continue;
                }
                $id_nom = is_numeric($row2['id_nom'] ?? null) ? (int) $row2['id_nom'] : 0;
                $id_activ = is_numeric($row2['id_activ'] ?? null) ? (int) $row2['id_activ'] : 0;
                $id_sector = is_numeric($row2['id_sector'] ?? null) ? (int) $row2['id_sector'] : 0;
                $nombre_corto = is_string($row2['nombre_corto'] ?? null) ? $row2['nombre_corto'] : '';
                $sectoresDept = $a_sectores[$id_departamento] ?? [];
                if (in_array($id_sector, $sectoresDept, true)) {
                    $docencia_dep[$id_nom] = 1;
                } else {
                    $docencia_no_dep[$id_nom] = 1;
                }

                if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista)) {
                    $oPersonaDl = $PersonaDlRepository->findById($id_nom);
                    if ($oPersonaDl === null) {
                        continue;
                    }
                    $nom = $oPersonaDl->getNom();
                    $apellido1 = $oPersonaDl->getApellido1();
                    $apellido2 = $oPersonaDl->getApellido2();

                    $nom_activ = '';
                    if ($id_activ > 0) {
                        $oActividad = $ActividadAllRepository->findById($id_activ);
                        if ($oActividad !== null) {
                            $nom_activ = $oActividad->getNom_activ();
                        }
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

        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
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
                    $data = (string) ($nombres[$id_nom][$val] ?? '');
                    $html .= '<td>' . $data . '</td>';
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
    /** @return ResumenRta */

    public function ProfesorCongreso(): array
    {
        $tabla = $this->getNomTabla();
        $notas = $this->getNomNotas();
        $curs = $this->getCurso();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2, p.ctr
				FROM d_congresos JOIN $tabla p USING (id_nom) WHERE f_ini $curs ";
        //echo "$ssql<br>";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Profesores de Bienio.
    /** @return ResumenRta */

    public function ProfesoresEnBienio(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE f_cese is null AND id_departamento=1
				ORDER BY p.apellido1,p.apellido2,p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Profesores de Cuadrienio.
    /** @return ResumenRta */

    public function ProfesoresEnCuadrienio(): array
    {
        $tabla = $this->getNomTabla();

        $ssql = "SELECT DISTINCT  p.id_nom,p.nom,p.apellido1,p.apellido2,p.ctr
				FROM d_profesor_stgr JOIN $tabla p USING (id_nom)
				WHERE f_cese is null AND id_departamento!=1
				ORDER BY p.apellido1,p.apellido2,p.nom
				";
        $statement = $this->tempTablesService->query($ssql);
        $rta['num'] = $statement->rowCount();
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
            $rta['lista'] = $this->Lista($ssql, "nom,apellido1,apellido2,ctr", 1);
        } else {
            $rta['lista'] = '';
        }
        return $rta;
    }

    // Numero de departamentos con director
    /** @return ResumenRta */

    public function Departamentos(): array
    {
        $tabla = $this->getNomTabla();

        $ProfesorDirectorRepository = $this->profesorDirectorRepository;
        $cDirectores = $ProfesorDirectorRepository->getProfesoresDirectores(array('f_cese' => 1), array('f_cese' => 'IS NULL'));
        $DepartamentoRepository = $this->departamentoRepository;
        $PersonaDlRepository = $this->personaDlRepository;

        $rta['num'] = count($cDirectores);
        if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($this->blista) && $rta['num'] > 0) {
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

    /** @return array<int, int|string> */
    public function getArrayDl(): array
    {
        return $this->a_dl;
    }

    /** @param array<int, int|string> $dl */
    public function setArrayDl(array $dl): void
    {
        $this->a_dl = $dl;
    }

}
