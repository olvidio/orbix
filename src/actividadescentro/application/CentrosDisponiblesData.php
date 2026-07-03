<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Devuelve la lista de centros disponibles (candidatos) para asignar como
 * encargados de una actividad segun el tipo: sg / sr / nagd / sssc / sfsg /
 * sfsr / sfnagd.
 *
 * Para `tipo=sg` la respuesta incluye tambien, por centro, el numero de
 * actividades en el periodo `[inicio, fin]` y la diferencia de dias con la
 * proxima/anterior actividad del centro respecto a `f_ini_act`.
 *
 * Sucesor de las ramas `nuevo_*` del dispatcher legacy `activ_ctr_ajax.php`.
 * Por la excepcion tolerable de `refactor.md` (dispatcher de lectura con
 * ramas que comparten contrato JSON) se agrupan en un unico use case con
 * parametro `tipo`.
 */
final class CentrosDisponiblesData
{
    public const TIPOS_VALIDOS = ['sg', 'sr', 'nagd', 'sssc', 'sfsg', 'sfsr', 'sfnagd'];

    public function __construct(
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $tipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo');
        $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            return [
                'tipo' => $tipo,
                'id_activ' => $id_activ,
                'centros' => [],
                'error' => _("tipo no valido"),
            ];
        }

        [$aWhere, $aOperador, $usarCentroEllas] = $this->filtros($tipo);
        $repo = $usarCentroEllas ? $this->centroEllasRepository : $this->centroDlRepository;
        $cCentros = $repo->getCentros($aWhere, $aOperador);

        $centros = [];
        if ($tipo === 'sg') {
            $inicio = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'inicio');
            $fin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'fin');
            $f_ini_act = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'f_ini_act');
            $f_ini_act_iso = '';
            if ($f_ini_act !== '') {
                $oDate = DateTimeLocal::createFromLocal($f_ini_act);
                if ($oDate !== false && $oDate !== null) {
                    $f_ini_act_iso = $oDate->getIso();
                }
            }
            $periodo = ($inicio !== '' && $fin !== '')
                ? "f_ini BETWEEN '" . $inicio . "' AND '" . $fin . "'"
                : '';

            foreach ($cCentros as $oCentro) {
                $id_ubi = (int) $oCentro->getId_ubi();
                $num_activ = 0;
                if ($periodo !== '') {
                    $cActivs = $this->centroEncargadoRepository->getActividadesDeCentros($id_ubi, $periodo);
                    $num_activ = count($cActivs);
                }
                $dif = $f_ini_act_iso !== ''
                    ? $this->centroEncargadoRepository->getProximasActividadesDeCentro($id_ubi, $f_ini_act_iso)
                    : '';
                $centros[] = [
                    'id_ubi' => $id_ubi,
                    'nombre_ubi' => (string) $oCentro->getNombre_ubi(),
                    'num_actividades_periodo' => $num_activ,
                    'dif_dias' => $dif,
                ];
            }
        } else {
            foreach ($cCentros as $oCentro) {
                $centros[] = [
                    'id_ubi' => (int) $oCentro->getId_ubi(),
                    'nombre_ubi' => (string) $oCentro->getNombre_ubi(),
                ];
            }
        }

        return [
            'tipo' => $tipo,
            'id_activ' => $id_activ,
            'centros' => $centros,
        ];
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>, 2: bool}
     */
    private function filtros(string $tipo): array
    {
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $aOperador = [];
        $usarCentroEllas = false;

        switch ($tipo) {
            case 'sg':
                $aWhere['tipo_ctr'] = '^s[^s]*';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sr':
                $aWhere['tipo_labor'] = '512';
                $aOperador['tipo_labor'] = '&';
                break;
            case 'nagd':
                $aWhere['tipo_ctr'] = '^[na]';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sssc':
                $aWhere['tipo_ctr'] = '^(ss|sss)$';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sfsg':
                $aWhere['tipo_labor'] = '64';
                $aOperador['tipo_labor'] = '&';
                $usarCentroEllas = true;
                break;
            case 'sfsr':
                $aWhere['tipo_labor'] = '512';
                $aOperador['tipo_labor'] = '&';
                $usarCentroEllas = true;
                break;
            case 'sfnagd':
                $aWhere['tipo_ctr'] = '^[na]';
                $aOperador['tipo_ctr'] = '~';
                $usarCentroEllas = true;
                break;
        }
        return [$aWhere, $aOperador, $usarCentroEllas];
    }
}
