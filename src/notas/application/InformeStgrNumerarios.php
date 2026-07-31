<?php

namespace src\notas\application;


use src\shared\config\ConfigGlobal;
use src\notas\application\support\ResumenFactory;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Calcula el informe anual STGR de "numerarios" (puntos 1..18 + `x`).
 *
 * Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers
 * del frontend no importen la clase legacy directamente. Devuelve un array
 * neutro `{res, textos, curso_txt}` listo para renderizado.
 */
final class InformeStgrNumerarios
{

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly CentroEstudiosLookup $centroEstudiosLookup,
        private readonly ResumenFactory $resumenFactory,
    ) {
    }
    /**
     * @param array<int,int|string> $a_dl       ids de delegacion seleccionadas (filtro STGR).
     * @param bool                  $lista      si `true`, cada metrica incluye el listado HTML
     *                                          de personas (construido por `Resumen` legacy).
     * @param string                $ce_lugar   codigo(s) `ce_lugar` separados por coma.
     *
     * @return array{res: array<int|string, array{num: int|float|string, lista?: string}>,
     *               textos: array<int|string, string>,
     *               curso_txt: string,
     *               avisos_html: string}
     */
    public function calcular(array $a_dl, bool $lista, string $ce_lugar): array
    {
        [$any_ini_curs, $curso_txt] = $this->cursoActual();

        $Resumen = $this->resumenFactory->create('numerarios');
        $Resumen->setArrayDl($this->mapearDelegaciones($a_dl));
        $Resumen->setAnyIniCurs($any_ini_curs);
        $Resumen->setLista($lista);
        $Resumen->nuevaTabla();
        $Resumen->setCe_lugar($ce_lugar);

        $res = [];
        $textos = [];

        // ---- Bienio -------------------------------------------------------
        $res[1] = $Resumen->enCe();
        $textos[1] = ucfirst(_("numerarios en el ce"));
        $res[2] = $Resumen->sinCe();
        $textos[2] = ucfirst(_("numerarios sin haber hecho el ce"));
        $res[3] = $Resumen->ceAcabadoEnBienio();
        $textos[3] = ucfirst(_("numerarios que han terminado el ce (otros años) y con el bienio sin acabar"));
        $res[4]['num'] = (int) $res[1]['num'] + (int) $res[2]['num'] + (int) $res[3]['num'];
        $res[4]['lista'] = ucfirst(_("es la suma de los puntos: 1+2+3"));
        $textos[4] = ucfirst(_("número de numerarios en Bienio"));

        $a_aprobadas_ce = $Resumen->aprobadasCe();
        $res[5]['num'] = $this->media((int)$res[1]['num'], (int)$a_aprobadas_ce['num']);
        $textos[5] = ucfirst(_("media de asignaturas superadas por alumno en ce (n. 1)"));

        $a_aprobadas_sin_ce = $Resumen->aprobadasSinCe();
        $res[6]['num'] = $this->media((int)$res[2]['num'], (int)$a_aprobadas_sin_ce['num']);
        $textos[6] = ucfirst(_("media de asignaturas superadas por alumno sin haber hecho el ce (n. 2)"));

        $res[7] = $Resumen->conPreceptorBienio();
        $textos[7] = ucfirst(_("nº de n en bienio que han superado asignaturas con preceptor"));

        // ---- Cuadrienio ---------------------------------------------------
        $res[8] = $Resumen->enCuadrienio(1);
        $textos[8] = ucfirst(_("número de numerarios en año I de Cuadrienio"));
        $res[9] = $Resumen->enCuadrienio(2);
        $textos[9] = ucfirst(_("número de numerarios en años II-IV de Cuadrienio"));
        $res[10] = $Resumen->enCuadrienio('all');
        $textos[10] = ucfirst(_("número de numerarios en cuadrienio"));

        /** @var array{num: int|float|string, lista: string, error?: bool} $a_aprobadas */
        $a_aprobadas = $Resumen->aprobadasCuadrienio();
        if (!array_key_exists('error', $a_aprobadas)) {
            $res[11]['num'] = $this->media((int)$res[10]['num'], (int)$a_aprobadas['num']);
            $res[11]['lista'] = $a_aprobadas['lista'];
            $textos[11] = ucfirst(_("media de asignaturas superadas por alumno en cuadrienio"));
        } else {
            $res[11] = $a_aprobadas;
            $textos[11] = sprintf(
                _("ERROR: hay %s numerarios que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),
                $a_aprobadas['num']
            );
        }

        $res[12] = $Resumen->masAsignaturasQue(10);
        $textos[12] = ucfirst(_("número de numerarios de cuadrienio que han superado 1 curso"));
        $res[13] = $Resumen->masAsignaturasQue(5);
        $textos[13] = ucfirst(_("número de numerarios de cuadrienio que han superado 1 semestre"));
        $res[14] = $Resumen->menosAsignaturasQue(5);
        $textos[14] = ucfirst(_("número de numerarios de cuadrienio que han superado menos de 1 semestre"));
        $res[15] = $Resumen->ningunaSuperada();
        $textos[15] = ucfirst(_("número de numerarios de cuadrienio que no han superado ninguna asignatura"));
        $res[16] = $Resumen->conPreceptorCuadrienio();
        $textos[16] = ucfirst(_("número de numerarios que han superado asignaturas con preceptor"));
        $res[17] = $Resumen->terminadoCuadrienio();
        $textos[17] = ucfirst(_("número de numerarios que han terminado el cuadrienio este curso"));

        // ---- Repaso -------------------------------------------------------
        $res[18] = $Resumen->laicosConCuadrienio();
        $textos[18] = ucfirst(_("número de numerarios laicos con el cuadrienio terminado"));

        if ($lista) {
            $res['x'] = $Resumen->enRepaso();
            $textos['x'] = ucfirst(_("número de numerarios de repaso"));
        }

        return [
            'res' => $res,
            'textos' => $textos,
            'curso_txt' => $curso_txt,
            'avisos_html' => $Resumen->getAvisosHtml(),
        ];
    }

    /**
     * Resuelve `ce_lugar` para un filtro de delegaciones (modo `rstgr`) o
     * cae al valor configurado en la sesion. Para las delegaciones especiales
     * H/M se devuelve el `ce_lugar` agregado de todas sus dependientes.
     */
    /**
     * @param array<int, int|string> $Qdl
     */
    public function resolverCeLugar(array $Qdl): string
    {
        if ($Qdl !== []) {
            return $this->centroEstudiosLookup->getFromDl($Qdl);
        }

        $dele = ConfigGlobal::mi_dele();
        if ($dele === 'H' || $dele === 'M') {
            $a_delegacionesStgr = $this->delegacionRepository->getArrayDlRegionStgr([$dele]);
            /** @var list<int|string> $ids */
            $ids = array_keys($a_delegacionesStgr);

            return $this->centroEstudiosLookup->getFromDl($ids);
        }

        $oConfig = $_SESSION['oConfig'] ?? null;
        return is_object($oConfig) && method_exists($oConfig, 'getCe_lugar')
            ? (string) $oConfig->getCe_lugar()
            : '';
    }

    /**
     * Traduce los ids de delegacion del POST al array de codigos (`dl`) que
     * espera `Resumen::setArrayDl()`, filtrando por la region STGR actual.
     *
     * @param array<int,int|string> $Qdl
     * @return array<int,string>
     */
    private function mapearDelegaciones(array $Qdl): array
    {
        if (empty($Qdl)) {
            return [];
        }
        $region_stgr = ConfigGlobal::mi_dele();
        $repoDelegacion = $this->delegacionRepository;
        $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);
        $a_dl = [];
        foreach ($Qdl as $id_dl) {
            if (isset($a_delegacionesStgr[$id_dl])) {
                $a_dl[] = $a_delegacionesStgr[$id_dl];
            }
        }
        return $a_dl;
    }

    /**
     * @return array{0: int, 1: string} `[any_ini_curs, curso_txt]`.
     */
    private function cursoActual(): array
    {
        $any = (int)date('Y');
        $mes = (int)date('m');
        if ($mes > 3) {
            $any1 = $any - 1;
            return [$any1, "$any1-$any"];
        }
        $any1 = $any - 2;
        $any--;
        return [$any1, "$any1-$any"];
    }

    private function media(int $total, int $aprobadas): string
    {
        if ($total === 0) {
            return '0';
        }
        return number_format($aprobadas / $total, 2, ',', '.');
    }
}
