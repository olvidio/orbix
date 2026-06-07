<?php

namespace src\notas\application;


use src\shared\config\ConfigGlobal;
use src\notas\application\support\ResumenFactory;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Calcula el informe anual STGR de "agregados" (puntos 21..33 + `x`).
 *
 * Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que
 * los controllers del frontend no importen la clase legacy directamente.
 * Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado.
 */
final class InformeStgrAgregados
{

    public function __construct(
        private readonly DelegacionRepositoryInterface $delegacionRepository,
        private readonly ResumenFactory $resumenFactory,
    ) {
    }
    /**
     * @param array<int,int|string> $a_dl   ids de delegacion seleccionadas (filtro STGR).
     * @param bool                  $lista  si `true`, cada metrica incluye el
     *                                      listado HTML de personas.
     *
     * @return array{res: array<int|string, array{num: int|float|string, lista?: string}>,
     *               textos: array<int|string, string>,
     *               curso_txt: string}
     */
    public function calcular(array $a_dl, bool $lista): array
    {
        [$any_ini_curs, $curso_txt] = $this->cursoActual();

        $Resumen = $this->resumenFactory->create('agregados');
        $Resumen->setArrayDl($this->mapearDelegaciones($a_dl));
        $Resumen->setAnyIniCurs($any_ini_curs);
        $Resumen->setLista($lista);
        $Resumen->nuevaTabla();

        $res = [];
        $textos = [];

        // ---- Bienio -------------------------------------------------------
        $res[21] = $Resumen->enBienio();
        $textos[21] = ucfirst(_("número de agregados en Bienio"));

        $a_aprobadas_bienio = $Resumen->aprobadasBienio();
        $res[22] = [
            'num' => $this->media((int) $res[21]['num'], (int) $a_aprobadas_bienio['num']),
            'lista' => $a_aprobadas_bienio['lista'],
        ];
        $textos[22] = ucfirst(_("media de asignaturas superadas por alumno en bienio"));

        $res[23] = $Resumen->bienioSinAcabar();
        $textos[23] = ucfirst(_("nº de agd en cuadrienio con bienio pendiente"));

        $res['23.1'] = $Resumen->conPreceptorBienio();
        $textos['23.1'] = ucfirst(_("nº de agd en bienio que han superado asignaturas con preceptor"));

        // ---- Cuadrienio ---------------------------------------------------
        $res[24] = $Resumen->enCuadrienio('all');
        $textos[24] = ucfirst(_("número de agregados Cuadrienio"));

        /** @var array{num: int|float|string, lista: string, error?: bool} $a_aprobadas */
        $a_aprobadas = $Resumen->aprobadasCuadrienio();
        if (!array_key_exists('error', $a_aprobadas)) {
            $res[25] = [
                'num' => $this->media((int) $res[24]['num'], (int) $a_aprobadas['num']),
                'lista' => $a_aprobadas['lista'],
            ];
            $textos[25] = ucfirst(_("media de asignaturas superadas por alumno en cuadrienio"));
        } else {
            $res[25] = $a_aprobadas;
            $textos[25] = sprintf(
                _("hay %s agregados que ya estaban en Repaso y han cursado asignaturas. Arreglarlo a mano"),
                $a_aprobadas['num']
            );
        }

        $res[26] = $Resumen->masAsignaturasQue(10);
        $textos[26] = ucfirst(_("número de agregados de cuadrienio que han superado 1 curso"));
        $res[27] = $Resumen->masAsignaturasQue(5);
        $textos[27] = ucfirst(_("número de agregados de cuadrienio que han superado 1 semestre"));
        $res[28] = $Resumen->menosAsignaturasQue(5);
        $textos[28] = ucfirst(_("número de agregados de cuadrienio que han superado menos de 1 semestre"));
        $res[29] = $Resumen->ningunaSuperada();
        $textos[29] = ucfirst(_("número de agregados de cuadrienio que no han superado ninguna asignatura"));
        $res[30] = $Resumen->conPreceptorCuadrienio();
        $textos[30] = ucfirst(_("número de agregados que han superado asignaturas de cuadrienio con preceptor"));
        $res[31] = $Resumen->terminadoCuadrienio();
        $textos[31] = ucfirst(_("número de agregados que han terminado el cuadrienio este curso"));

        $res[32]['num'] = (int) $res[21]['num'] + (int) $res[24]['num'];
        $textos[32] = ucfirst(_("número total de alumnos agregados"));

        // ---- Repaso -------------------------------------------------------
        $res[33] = $Resumen->laicosConCuadrienio();
        $textos[33] = ucfirst(_("número de agregados laicos con el cuadrienio terminado"));

        if ($lista) {
            $res['x'] = $Resumen->enRepaso();
            $textos['x'] = ucfirst(_("número de agregados de repaso"));
        }

        return [
            'res' => $res,
            'textos' => $textos,
            'curso_txt' => $curso_txt,
        ];
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
