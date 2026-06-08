<?php

namespace src\ubis\application;

use src\permisos\domain\XPermisos;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

class ListCtrData
{
    public function __construct(
        private CentroRepositoryInterface $centroRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroExRepositoryInterface $centroExRepository,
        private CasaRepositoryInterface $casaRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
        private CasaExRepositoryInterface $casaExRepository,
        private UbiTelecoService $ubiTelecoService,
    ) {
    }

    /**
     * @return array{
     *     opciones_loc: array<string, string>,
     *     opciones_que_lista: array<string, string>,
     *     a_cabeceras: list<mixed>,
     *     a_valores: array<int|string, mixed>,
     *     a_botones: list<array{txt: string, click: string}>
     * }
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(
        string $Qloc,
        string $Qque_lista,
        string $idSel = '',
        string $scrollId = ''
    ): array {
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aWhere = [];
        $aWhere['active'] = 't';
        $aWhere['_ordre'] = 'nombre_ubi';
        $aOperador = [];

        if ($Qloc !== 'ex') {
            $a_reg = explode('-', $Qloc);
            $reg = $a_reg[0];
            if ($miSfsv === 1) {
                $dl = substr($a_reg[1], 0, -1);
            } else {
                $dl = $a_reg[1];
            }
            if ($dl === ConfigGlobal::mi_delef()) {
                $objCentro = 'CentroDl';
                $objCasa = 'CasaDl';
            } else {
                $objCentro = 'Centro';
                $objCasa = 'Casa';
                $aWhere['region'] = $reg;
                if ($dl !== 'cr') {
                    $aWhere['dl'] = $dl;
                }
            }
            switch ($Qque_lista) {
                case "todos_ctr_dl":
                    $obj = $objCentro;
                    $aWhere['_ordre'] = 'tipo_ctr,nombre_ubi';
                    break;
                case "ctr_n":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '^n';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_nax":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '^x';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_agd":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '^a[^p]*';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_s":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '^(sj|sm|s)$';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_sssc":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '^(ss|sss)$';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "oc":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '(oc)';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "cgi":
                    $obj = $objCentro;
                    $aWhere['tipo_ctr'] = '(cgi)';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "cdr_cdc_dl":
                    $obj = $objCasa;
                    $aWhere['tipo_casa'] = 'cdc|cdr';
                    $aOperador['tipo_casa'] = '~';
                    switch ($miSfsv) {
                        case 1:
                            if (($oPerm = $_SESSION['oPerm'] ?? null) instanceof XPermisos && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'))) {
                            } else {
                                $aWhere['sv'] = 't';
                            }
                            break;
                        case 2:
                            $aWhere['sf'] = 't';
                            break;
                    }
                    break;
                case "otros_cdc":
                    $obj = $objCasa;
                    $aWhere['tipo_casa'] = 'cdc|cdr|cgi';
                    $aOperador['tipo_casa'] = '!~';
                    switch ($miSfsv) {
                        case 1:
                            if (($oPerm = $_SESSION['oPerm'] ?? null) instanceof XPermisos && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'))) {
                            } else {
                                $aWhere['sv'] = 't';
                            }
                            break;
                        case 2:
                            $aWhere['sf'] = 't';
                            break;
                    }
                    break;
                default:
                    $obj = 'none';
                    break;
            }
        } else {
            switch ($Qque_lista) {
                case "ctr_n":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = '^n';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_nax":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = '^x';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_agd":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = '^a[^p]*';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "ctr_s":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = '^(sj|sm|s)$';
                    $aOperador['tipo_ctr'] = '~';
                    break;
                case "todos_ctr_ex":
                    $obj = 'CentroEx';
                    break;
                case "dl":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = 'dl';
                    break;
                case "cr":
                    $obj = 'CentroEx';
                    $aWhere['tipo_ctr'] = 'cr';
                    break;
                case "cdc_ex":
                    $obj = 'CasaEx';
                    break;
                default:
                    $obj = 'none';
                    break;
            }
        }

        switch ($obj) {
            case 'Centro':
                $CentroRepository = $this->centroRepository;
                $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
                break;
            case 'CentroDl':
                $CentroRepository = $this->centroDlRepository;
                $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
                break;
            case 'CentroEx':
                $CentroRepository = $this->centroExRepository;
                $cUbis = $CentroRepository->getCentros($aWhere, $aOperador);
                break;
            case 'Casa':
                $CasaRepository = $this->casaRepository;
                $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
                break;
            case 'CasaDl':
                $CasaRepository = $this->casaDlRepository;
                $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
                break;
            case 'CasaEx':
                $CasaRepository = $this->casaExRepository;
                $cUbis = $CasaRepository->getCasas($aWhere, $aOperador);
                break;
            case 'none':
            default:
                $cUbis = [];
                $obj = 'none';
                break;
        }

        $a_cabeceras = [];
        $a_cabeceras[] = ['name' => ucfirst(_("centro")), 'formatter' => 'clickFormatter'];
        $a_cabeceras[] = ucfirst(_("región"));
        $a_cabeceras[] = ucfirst(_("tipo ctr o casa"));
        $a_cabeceras[] = ucfirst(_("dirección"));
        $a_cabeceras[] = ucfirst(_("cp"));
        $a_cabeceras[] = ucfirst(_("ciudad"));
        $a_cabeceras[] = ucfirst(_("teléfono"));

        $i = 0;
        $meta = [];
        if ($idSel !== '') {
            $meta['select'] = $idSel;
        }
        if ($scrollId !== '') {
            $meta['scroll_id'] = $scrollId;
        }
        $filas = [];
        foreach ($cUbis as $oCentro) {
            $i++;
            $region = $oCentro->getRegion();
            $id_ubi = $oCentro->getId_ubi();
            $ctr = $oCentro->getNombre_ubi();

            $tipo = '';
            if (str_contains($obj, 'Centro') && method_exists($oCentro, 'getTipo_ctr')) {
                $tipo = $oCentro->getTipo_ctr();
            } elseif (str_contains($obj, 'Casa') && method_exists($oCentro, 'getTipo_casa')) {
                $tipo = $oCentro->getTipo_casa();
            }
            $cDirecciones = $oCentro->getDirecciones();

            $poblacion = '';
            $pais = '';
            $direccion = '';
            $c_p = '';
            if (!empty($cDirecciones)) {
                $d = 0;
                foreach ($cDirecciones as $oDireccion) {
                    $d++;
                    if ($d > 1) {
                        $poblacion .= '<br>';
                        $pais .= '<br>';
                        $direccion .= '<br>';
                        $c_p .= '<br>';
                    }
                    $poblacion .= $oDireccion->getPoblacion();
                    $pais .= $oDireccion->getPais();
                    $direccion .= $oDireccion->getDireccionVo()?->value() ?? '';
                    $c_p .= $oDireccion->getC_p();
                }
            }

            $filas[$i]['sel'] = "$id_ubi";
            $filas[$i][1] = [
                'link_spec' => [
                    'path' => 'frontend/ubis/controller/home_ubis.php',
                    'query' => ['pau' => 'u', 'id_ubi' => $id_ubi],
                ],
                'valor' => $ctr,
            ];
            $filas[$i][2] = $region;
            $filas[$i][3] = $tipo;
            $filas[$i][4] = $direccion;
            $filas[$i][5] = $c_p;

            if (!str_contains($obj, 'Ex')) {
                $filas[$i][6] = $poblacion;
            } elseif ($pais === "España") {
                $filas[$i][6] = $poblacion;
            } else {
                $filas[$i][6] = "$poblacion ($pais)";
            }
            $tels = $this->ubiTelecoService->texto($obj, (int)$id_ubi, 'telf', '', ' ');

            $filas[$i][7] = $tels;
        }
        $a_valores = $meta + $filas;

        $oDBPropiedades = new DBPropiedades();
        $opciones_loc = $oDBPropiedades->array_posibles_esquemas();
        if (!is_array($opciones_loc)) {
            $opciones_loc = [];
        }
        $opciones_loc['ex'] = _("otras");

        if ($Qloc !== 'ex') {
            $aOpciones = ['' => '',
                'ctr_n' => ucfirst(_("sólo centros de n")),
                'todos_ctr_dl' => ucfirst(_("todos los ctr de la dl")),
                'ctr_agd' => ucfirst(_("sólo centros de agd")),
                'ctr_s' => ucfirst(_("sólo centros de s")),
            ];
            if ($miSfsv === 1) {
                $aOpciones['ctr_sssc'] = ucfirst(_("sólo centros de sss+"));
            }
            if ($miSfsv === 2) {
                $aOpciones['ctr_nax'] = ucfirst(_("sólo centros de nax"));
            }
            $aOpciones['oc'] = ucfirst(_("sólo obras corporativas"));
            $aOpciones['cdr_cdc_dl'] = ucfirst(_("casas de retiros y de cv"));
            $aOpciones['cgi'] = ucfirst(_("sólo colegios"));
            $aOpciones['otros_cdc'] = ucfirst(_("resto casas cdc"));
        } else {
            $aOpciones = ['' => '',
                'ctr_n_ex' => ucfirst(_("sólo centros de n")),
                'ctr_agd_ex' => ucfirst(_("sólo centros de agd")),
                'ctr_s_ex' => ucfirst(_("sólo centros de s")),
                'todos_ctr_ex' => ucfirst(_("todos los centros")),
                'dl' => ucfirst(_("sólo delegaciones")),
                'cr' => ucfirst(_("sólo comisiones regionales")),
                'cdc_ex' => ucfirst(_("todas las casas")),
            ];
        }

        $a_botones = [['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"]];
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina('admin_sv')) {
            $a_botones[] = ['txt' => _("trasladar"), 'click' => "fnjs_ver_dl()"];
        }

        return [
            'opciones_loc' => $opciones_loc,
            'opciones_que_lista' => $aOpciones,
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_botones' => $a_botones,
        ];
    }
}
