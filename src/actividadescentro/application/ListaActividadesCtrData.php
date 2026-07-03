<?php

namespace src\actividadescentro\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\procesos\domain\PermAccion;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use frontend\shared\web\Periodo;

/**
 * Caso de uso: construye la tabla principal de la pantalla
 * `actividadescentro/activ_ctr` — actividades del tipo elegido en el
 * periodo + los centros encargados de cada una, con los flags de permiso
 * necesarios para que el frontend decida como renderizar cada celda
 * (ver / modificar / crear).
 *
 * Sucesor de la rama `lista_activ` del dispatcher legacy
 * `apps/actividadescentro/controller/activ_ctr_ajax.php`. En el legacy la
 * rama imprimia HTML (`<table>` con `<tr>` y onclick handlers). Aqui
 * devuelve arrays neutros: la construccion del HTML vive en el controller
 * frontend `frontend/actividadescentro/controller/activ_ctr.php`.
 */
final class ListaActividadesCtrData
{
    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private CentroEncargadoRepositoryInterface $centroEncargadoRepository,
        private CasaRepositoryInterface $casaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     *
     * @return array{
     *     titulo: string,
     *     tipo: string,
     *     inicio_iso: string,
     *     fin_iso: string,
     *     filas: list<array<string, mixed>>
     * }
     */
    public function execute(array $input): array
    {
        $tipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'tipo');
        $year = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'year');
        $periodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
        $empiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
        $empiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

        if ($periodo === '') {
            $periodo = 'actual';
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($year);
        $oPeriodo->setEmpiezaMin($empiezamin);
        $oPeriodo->setEmpiezaMax($empiezamax);
        $oPeriodo->setPeriodo($periodo);
        $inicioIso = (string) ($oPeriodo->getF_ini_iso() ?? '');
        $finIso = (string) ($oPeriodo->getF_fin_iso() ?? '');

        $aWhere = [
            'f_ini' => "'$inicioIso','$finIso'",
            'status' => 3,
            '_ordre' => 'f_ini,nom_activ',
        ];
        $aOperador = [
            'f_ini' => 'BETWEEN',
            'status' => '<',
        ];
        $regex = $this->regexPorTipo($tipo);
        if ($regex !== null) {
            $aWhere['id_tipo_activ'] = $regex;
            $aOperador['id_tipo_activ'] = '~';
        }

        $cActividades = $this->actividadDlRepository->getActividades($aWhere, $aOperador);
        $a_casas = $this->casaRepository->getArrayCasas();

        $filas = [];
        $orderKeysFecha = [];
        $orderKeysCasa = [];
        foreach ($cActividades as $oActividad) {
            $id_activ = (int) $oActividad->getId_activ();
            $id_tipo_activ = (string) $oActividad->getId_tipo_activ();
            $dl_org = (string) ($oActividad->getDl_org() ?? '');
            $nom_activ = (string) $oActividad->getNom_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();
            $id_ubi_actividad = $oActividad->getId_ubi();

            [$oPermActiv, $oPermCtr] = $this->resolverPermisos($id_activ, $id_tipo_activ, $dl_org);

            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                continue;
            }
            if ($oPermActiv->have_perm_activ('ver') === false) {
                continue;
            }

            $centros = [];
            if ($oPermCtr->have_perm_activ('ver') === true) {
                $cCtrs = $this->centroEncargadoRepository->getCentrosEncargadosActividad($id_activ);
                foreach ($cCtrs as $oCentro) {
                    $centros[] = [
                        'id_ubi' => (int) $oCentro->getId_ubi(),
                        'nombre_ubi' => (string) $oCentro->getNombre_ubi(),
                    ];
                }
            }

            if (empty($id_ubi_actividad) || $id_ubi_actividad === 1) {
                $nombre_ubi_actividad = 'z';
            } else {
                $nombre_ubi_actividad = $a_casas[$id_ubi_actividad] ?? 'z';
            }

            $idx = count($filas);
            $filas[] = [
                'id_activ' => $id_activ,
                'nom_activ' => $nom_activ,
                'f_ini' => $f_ini,
                'f_fin' => $f_fin,
                'perm_modificar_ctr' => $oPermCtr->have_perm_activ('modificar') === true,
                'perm_crear_ctr' => $oPermCtr->have_perm_activ('crear') === true,
                'centros' => $centros,
            ];
            $fIniVo = $oActividad->getF_ini();
            $orderKeysFecha[$idx] = $fIniVo instanceof DateTimeLocal ? (string) $fIniVo->getIso() : '';
            $orderKeysCasa[$idx] = (string) $nombre_ubi_actividad;
        }

        if ($filas !== []) {
            array_multisort(
                $orderKeysFecha,
                SORT_STRING,
                $orderKeysCasa,
                SORT_STRING,
                $filas
            );
        }

        return [
            'titulo' => sprintf(_("listado de actividades %s"), $tipo),
            'tipo' => $tipo,
            'inicio_iso' => $inicioIso,
            'fin_iso' => $finIso,
            'filas' => $filas,
        ];
    }

    private function regexPorTipo(string $tipo): ?string
    {
        switch ($tipo) {
            case 'sg':
                return '^1[45]';
            case 'sr':
                return '^17';
            case 'nagd':
                return '^1[13]';
            case 'sfsg':
                return '^2[45]';
            case 'sfsr':
                return '^27';
            case 'sfnagd':
                return '^2[123]';
            case 'sssc':
                return '^16';
        }
        return null;
    }

    /**
     * @return array{0: PermAccion, 1: PermAccion}
     */
    private function resolverPermisos(int $id_activ, string $id_tipo_activ, string $dl_org): array
    {
        if (ConfigGlobal::is_app_installed('procesos')) {
            $oPermSesion = $_SESSION['oPermActividades'] ?? null;
            if ($oPermSesion instanceof PermisosActividades) {
                $oPermSesion->setActividad($id_activ, $id_tipo_activ, $dl_org);
                $oPermActiv = $oPermSesion->getPermisoActual('datos');
                $oPermCtr = $oPermSesion->getPermisoActual('ctr');

                return [$oPermActiv, $oPermCtr];
            }
        }
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        $oPermActiv = $oPermActividades->getPermisoActual('datos');
        $oPermCtr = $oPermActividades->getPermisoActual('ctr');

        return [$oPermActiv, $oPermCtr];
    }
}
