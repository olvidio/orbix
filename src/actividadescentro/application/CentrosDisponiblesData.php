<?php

namespace src\actividadescentro\application;

use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\CentroEllasField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
    /**
     * Tipo de filtro de centros (`sg`, `sfsg`, …) a partir del `id_tipo_activ` de la actividad.
     * Alineado con los regex de {@see ListaActividadesCtrData::regexPorTipo}.
     */
    public static function tipoDesdeIdTipoActiv(string $id_tipo_activ): ?string
    {
        if ($id_tipo_activ === '') {
            return null;
        }
        $map = [
            'sssc' => '/^16/',
            'sr' => '/^17/',
            'sfsr' => '/^27/',
            'sg' => '/^1[45]/',
            'nagd' => '/^1[13]/',
            'sfsg' => '/^2[45]/',
            'sfnagd' => '/^2[123]/',
        ];
        foreach ($map as $tipo => $regex) {
            if (preg_match($regex, $id_tipo_activ) === 1) {
                return $tipo;
            }
        }

        return null;
    }

    public function execute(array $input): array
    {
        $tipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo');
        $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_tipo_activ');
            $resuelto = self::tipoDesdeIdTipoActiv($id_tipo_activ);
            if ($resuelto !== null) {
                $tipo = $resuelto;
            }
        }
        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            return [
                'tipo' => $tipo,
                'id_activ' => $id_activ,
                'centros' => [],
                'error' => _("tipo no valido"),
            ];
        }

        $usarCentroEllas = in_array($tipo, ['sfsg', 'sfsr', 'sfnagd'], true);
        $repo = $usarCentroEllas ? $this->centroEllasRepository : $this->centroDlRepository;
        $cCentros = $repo->matching($this->criterio($tipo, $usarCentroEllas));

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
     * Filtro de centros activos del tipo pedido, ordenados por nombre. Cada repositorio solo
     * acepta su propio enum de campos (`CentroEllasField` o `CentroDlField`).
     */
    private function criterio(string $tipo, bool $usarCentroEllas): Criteria
    {
        /** @var class-string<CentroDlField>|class-string<CentroEllasField> $f */
        $f = $usarCentroEllas ? CentroEllasField::class : CentroDlField::class;
        $criteria = Criteria::create()->where(Filter::eq($f::Active, true))->orderBy($f::NombreUbi);

        switch ($tipo) {
            case 'sg':
                $criteria = $criteria->andWhere(Filter::matches($f::TipoCtr, '^s[^s]*'));
                break;
            case 'sr':
            case 'sfsr':
                $criteria = $criteria->andWhere(Filter::hasBits($f::TipoLabor, 512));
                break;
            case 'nagd':
            case 'sfnagd':
                $criteria = $criteria->andWhere(Filter::matches($f::TipoCtr, '^[na]'));
                break;
            case 'sssc':
                $criteria = $criteria->andWhere(Filter::matches($f::TipoCtr, '^(ss|sss)$'));
                break;
            case 'sfsg':
                $criteria = $criteria->andWhere(Filter::hasBits($f::TipoLabor, 64));
                break;
        }

        return $criteria;
    }
}
