<?php

namespace src\actividadplazas\application\services;

use src\asistentes\application\services\AsistenteActividadService;
use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use frontend\shared\web\Desplegable;
use function src\shared\domain\helpers\is_true;

/**
 * Servicio de aplicación para gestionar el resumen de plazas de actividades.
 *
 * Este servicio orquesta la lógica de negocio relacionada con el cálculo y gestión
 * de plazas de actividades (propias, cedidas, conseguidas, disponibles, ocupadas).
 *
 * Movido desde domain/ a application/services/ para cumplir con DDD:
 * - Este es un servicio de aplicación (orquesta múltiples repositorios)
 * - No es lógica de dominio pura (depende de configuración global, sesión)
 * - No es una entidad ni value object
 *
 * @package orbix
 * @subpackage application
 * @author Daniel Serrabou
 * @version 2.0
 * @created 09/11/2016
 * @updated 2026-01-02 (Movido a application layer)
 */
class ResumenPlazasService
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_activ de actividadPlazas
     *
     * @var integer
     */
    protected $iid_activ;

    /**
     * Plazas de actividadPlazas
     *
     * @var integer
     */
    protected $dl_org;

    /**
     * Array de dl por nombre
     *
     * @var array
     */
    protected $a_dele;
    /**
     * Array de dl por id
     *
     * @var array
     */
    protected $a_id_dele;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private ActividadAllRepositoryInterface $ActividadAllRepository;
    private ActividadPlazasRepositoryInterface $ActividadPlazasRepository;
    private DelegacionRepositoryInterface $DelegacionRepository;
    private AsistenteActividadService $AsistenteActividadService;

    public function __construct(
        ActividadAllRepositoryInterface $actividadAllRepository,
        ActividadPlazasRepositoryInterface $actividadPlazasRepository,
        DelegacionRepositoryInterface $delegacionRepository,
        AsistenteActividadService $asistenteActividadService
    ) {
        $this->ActividadAllRepository = $actividadAllRepository;
        $this->ActividadPlazasRepository = $actividadPlazasRepository;
        $this->DelegacionRepository = $delegacionRepository;
        $this->AsistenteActividadService = $asistenteActividadService;
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Devuelve el array de opciones para el desplegable de posibles
     * propietarios de la plaza (propias + cedidas) en la actividad
     * `id_activ`. Es datos puros; la UI se construye en
     * {@see getPosiblesPropietarios()} (legacy) o en el frontend
     * (con el payload de {@see \src\actividadplazas\application\PosiblesPropietariosData}).
     *
     * @param false|string $dl_de_paso
     * @return array<string,string> clave "dl_org>dl_destino" => label
     */
    public function getPosiblesPropietariosOpciones($dl_de_paso = false): array
    {
        $id_activ = $this->getId_activ();
        $mi_dl = ConfigGlobal::mi_delef();
        $id_mi_dl = $this->getDlId($mi_dl);

        $asistenteActividadService = $this->AsistenteActividadService;
        $a_dl = [];
        $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(['id_activ' => $id_activ]);
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $id_dl_otra = $oActividadPlazas->getId_dl();
            $dl_otra = $this->getDlText($id_dl_otra);

            $aCedidas = $oActividadPlazas->getArrayCedidas();
            if (!empty($aCedidas)) {
                foreach ($aCedidas as $dl_2 => $num_plazas) {
                    if ($mi_dl === $dl_2) {
                        $ocu = $asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dl, $dl_otra);
                        $a_dl["$dl_otra>$dl_2"] = "$dl_otra ($ocu de $num_plazas)";
                    }
                    if ($dl_de_paso !== false) {
                        if ($dl_de_paso == $dl_2 && $id_dl_otra == $id_mi_dl) {
                            $ocu = $asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $dl_2, $mi_dl);
                            $a_dl["$mi_dl>$dl_de_paso"] = "$dl_de_paso ($ocu de $num_plazas)";
                        }
                    }
                }
            }
        }
        $pl_propias = $this->getPlazasPropias();
        if ($pl_propias > 0) {
            $ocu = $asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dl, $mi_dl);
            $a_dl["$mi_dl>$mi_dl"] = "$mi_dl ($ocu de $pl_propias)";
        }
        if (count($a_dl) === 0) {
            $a_dl['xxx'] = (string)_("no disponibles");
        }
        return $a_dl;
    }

    /**
     * Indica si una opcion del desplegable de propietarios tiene plazas libres.
     * Las etiquetas siguen el formato "dl (ocupadas de total)"; "2 de 2" no esta disponible.
     */
    public static function esPropiedadOpcionDisponible(string $label): bool
    {
        if (preg_match('/\((\d+) de (\d+)\)\s*$/', $label, $matches)) {
            return (int)$matches[1] < (int)$matches[2];
        }

        return false;
    }

    /**
     * Indica si la clave de propietario (p. ej. "dlA>dlB") tiene plazas libres
     * segun las opciones del desplegable.
     *
     * @param false|string $dl_de_paso
     */
    public function esPropiedadClaveDisponible(string $key, $dl_de_paso = false): bool
    {
        if ($key === '' || $key === 'xxx') {
            return false;
        }

        $opciones = $this->getPosiblesPropietariosOpciones($dl_de_paso);

        return isset($opciones[$key])
            && self::esPropiedadOpcionDisponible($opciones[$key]);
    }

    /**
     * Devuelve la clave de la primera propiedad con plazas libres, en el mismo
     * orden que el desplegable de posibles propietarios.
     *
     * @param false|string $dl_de_paso
     */
    public function getPrimeraPropiedadLibre($dl_de_paso = false): ?string
    {
        foreach ($this->getPosiblesPropietariosOpciones($dl_de_paso) as $key => $label) {
            if ($key === 'xxx') {
                continue;
            }
            if (self::esPropiedadOpcionDisponible($label)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Wrapper legacy que devuelve un `frontend\shared\web\Desplegable` envolviendo las
     * opciones de {@see getPosiblesPropietariosOpciones()}. Se mantiene
     * para compatibilidad con callers que aun renderizan el `<select>`
     * en servidor (ver `apps/asistentes/controller/form_{1301,3101}.php`).
     * El resto debe consumir las opciones directamente.
     *
     * @param false|string $dl_de_paso
     */
    public function getPosiblesPropietarios($dl_de_paso = false): Desplegable
    {
        $a_dl = $this->getPosiblesPropietariosOpciones($dl_de_paso);
        return new Desplegable('', $a_dl, '', true);
    }

    /**
     * Devuelve las plazas diponibles para una dl de una actividad
     */
    public function getPlazasCalendario($dl)
    {

        $id_activ = $this->getId_activ();
        $mi_dl = $dl;
        $id_mi_dl = $this->getDlId($mi_dl);
        $dl_org = $this->getDl_org();

        // plazas de calendario de cada dl
        $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(array('id_activ' => $id_activ, 'id_dl' => $id_mi_dl, 'dl_tabla' => $dl_org));
        $plazas_calendario = 0;
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $plazas_calendario += $oActividadPlazas->getPlazasVo()?->value()?? 0;
        }
        return $plazas_calendario;
    }

    /**
     * Devuelve las plazas cedidas para una dl de una actividad
     */
    public function getPlazasCedidas($dl)
    {
        $plazas_cedidas = 0;
        $id_activ = $this->getId_activ();
        $mi_dl = $dl;
        $id_mi_dl = $this->getDlId($mi_dl);
        $dl_org = $this->getDl_org();

        // plazas de calendario de cada dl
        $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(array('id_activ' => $id_activ, 'id_dl' => $id_mi_dl, 'dl_tabla' => $mi_dl));
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $aCedidas = $oActividadPlazas->getArrayCedidas();
            $plazas_cedidas = 0;
            if (!empty($aCedidas)) {
                foreach ($aCedidas as $dl_otra => $plazas) {
                    $plazas_cedidas += $plazas;
                }
            }
        }
        return $plazas_cedidas;
    }

    /**
     * Devuelve las plazas conseguidas para una dl de un actividad
     */
    public function getPlazasConseguidas($dl)
    {
        $id_activ = $this->getId_activ();
        $mi_dl = $dl;
        $dl_org = $this->getDl_org();

        // plazas de calendario de cada dl
        $plazas_conseguidas = 0;
        $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(array('id_activ' => $id_activ));
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $id_dl_otra = $oActividadPlazas->getId_dl();
            $dl_otra = $this->getDlText($id_dl_otra);
            $dl_tabla = $oActividadPlazas->getDlTablaVo()->value();

            $aCedidas = $oActividadPlazas->getArrayCedidas();
            if (!empty($aCedidas)) {
                foreach ($aCedidas as $dl_2 => $num_plazas) {
                    if ($mi_dl == $dl_2) {
                        $plazas_conseguidas += $num_plazas;
                    }
                }
            }
        }
        return $plazas_conseguidas;

    }

    /**
     * Devuelve las plazas diponibles para una dl de una actividad
     */
    public function getPlazasDisponibles($dl)
    {
        $plazas_calendario = $this->getPlazasCalendario($dl);
        $plazas_cedidas = $this->getPlazasCedidas($dl);
        $plazas_conseguidas = $this->getPlazasConseguidas($dl);

        $plazas_disponibles = $plazas_calendario + $plazas_conseguidas - $plazas_cedidas;

        return $plazas_disponibles;
    }

    /**
     * Devuelve las plazas totales de una actividad, o las de la casa.
     */
    public function getPlazasTotales()
    {
        $id_activ = $this->getId_activ();
        $oActividad = $this->ActividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return '?';
        }
        $plazas_totales = $oActividad->getPlazasVo()?->value();
        if (empty($plazas_totales)) {
            $id_ubi = $oActividad->getId_ubi();
            $plazas_totales = '';
            if ($id_ubi !== null) {
                $oCasa = Ubi::NewUbi($id_ubi);
                // Si la casa es un ctr de otra dl, no sé las plazas
                if ($oCasa !== null && method_exists($oCasa, 'getPlazas')) {
                    $plazas_totales = $oCasa->getPlazasVo()?->value();
                }
            }
            if (empty($plazas_totales)) {
                $plazas_totales = '?';
            }
        }

        return $plazas_totales;
    }

    /**
     *
     * @param integer $id_activ
     * @return array $a_plazas
     *
     */
    public function getResumen()
    {
        $a_plazas = [];
        $id_activ = $this->getId_activ();
        $AsistenteActividadService = $this->AsistenteActividadService;
        $oActividad = $this->ActividadAllRepository->findById($id_activ);
        $dl_org = $oActividad->getDl_org();
        $plazas_totales = $this->getPlazasTotales();
        /*
        // si la actividad no está publicada, no hay plazas de otras dl. Todas para la dl org.
        if ($oActividad->isPublicado() === false) {
//			$ocupadas = $AsistenteActividadService->getPlazasOcupadasPorDl($id_activ,$dl_org);
//			if ($ocupadas < 0) { // No se sabe
//				$a_plazas[$dl_org]['ocupadas'] = '?';
//				$a_plazas['total']['ocupadas'] = $ocupadas;
//			} else {
//				$a_plazas[$dl_org]['ocupadas'] = $ocupadas;
//				$a_plazas['total']['ocupadas'] = $ocupadas;
//			}

            $a_plazas['total']['actividad'] = $plazas_totales;
            $a_plazas['total']['calendario'] = $plazas_totales;
            $a_plazas['total']['cedidas'] = 0;
            $a_plazas['total']['conseguidas'] = 0;
            $a_plazas['total']['actual'] = $plazas_totales;

            return $a_plazas;
        }
         *
         */
        // plazas de calendario de cada dl + cedidas
        $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(['id_activ' => $id_activ]);
        foreach ($cActividadPlazas as $oActividadPlazas) {
            $id_dl = $oActividadPlazas->getId_dl();
            $dl = $this->getDlText($id_dl);
            $dl_tabla = $oActividadPlazas->getDlTablaVo()->value();
            if (empty($a_plazas[$dl]['cedidas'])) {
                $a_plazas[$dl]['cedidas'] = [];
            }
            if (empty($a_plazas[$dl]['conseguidas'])) {
                $a_plazas[$dl]['conseguidas'] = [];
            }
            //$a_plazas[$dl]['calendario'] = 0;
            if ($dl_org == $dl_tabla) {
                $a_plazas[$dl]['calendario'] = $oActividadPlazas->getPlazasVo()?->value() ?? 0;
                // las cedidas se guardan en la tabla que pertenece a la dl
                if ($dl === $dl_org) {
                    $aCedidas = $oActividadPlazas->getArrayCedidas();
                    if (!empty($aCedidas)) {
                        $a_plazas[$dl]['cedidas'] = $aCedidas;
                    }
                }
            } else { //para plazas cedidas de una dl que no es la que organiza.
                $aCedidas = $oActividadPlazas->getArrayCedidas();
                if (!empty($aCedidas)) {
                    $a_plazas[$dl]['cedidas'] = $aCedidas;
                }
            }
            $a_plazas[$dl]['total_cedidas'] = 0;
            $a_plazas[$dl]['total_conseguidas'] = 0;
        }
        //Calcular totales
        $tot_calendario = 0;
        $tot_disponibles = 0;
        $tot_ocupadas = 0;
        $tot_cedidas = 0;
        $tot_conseguidas = 0;
        // Conseguidas
        foreach ($a_plazas as $dl => $aa) {
            $total_cedidas = 0;
            // si no tiene por calendario le pongo 0
            if (!array_key_exists('calendario', $aa)) {
                $num_plazas_calendario = 0;
            } else {
                $num_plazas_calendario = $aa['calendario'];
            }
            if (!array_key_exists('cedidas', $aa)) {
                $aCedidas = [];
            } else {
                $aCedidas = $aa['cedidas'];
            }
            foreach ($aCedidas as $dl_otra => $num_plazas) {
                if ($dl != $dl_otra && array_key_exists($dl_otra, $a_plazas)) {
                    $a_plazas[$dl_otra]['conseguidas'][$dl] = $num_plazas;
                } else {
                    $a_plazas[$dl_otra]['conseguidas'][$dl] = $num_plazas;
//					
//					$ocu = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$dl_otra,$dl);
//					$a_plazas[$dl]['ocupadas'][$dl_otra] = $ocu;
                }
                $total_cedidas += $num_plazas;
            }
            $a_plazas[$dl]['total_cedidas'] = $total_cedidas;
            $tot_calendario += $num_plazas_calendario;
            $tot_cedidas += $total_cedidas;
        }
        foreach ($a_plazas as $dl => $aa) {
            $total_conseguidas = 0;
            $aCedidas = $aa['conseguidas'];
            foreach ($aCedidas as $dl_otra => $num_plazas) {
                $total_conseguidas += $num_plazas;
            }
            $a_plazas[$dl]['total_conseguidas'] = $total_conseguidas;
            $tot_conseguidas += $total_conseguidas;
        }
        // Disponibles (calendario - cedidas // conseguidas)
        foreach ($a_plazas as $dl => $aa) {
            $total_disponibles = 0;
            $aa['calendario'] = empty($aa['calendario']) ? 0 : $aa['calendario'];
            if (empty($aa['cedidas'])) {
                $disponibles = $aa['calendario'];
            } else {
                $disponibles = $aa['calendario'] - \array_sum($aa['cedidas']);
            }
            $total_disponibles += $disponibles;
            $a_plazas[$dl]['disponibles'][$dl] = $disponibles;
            foreach ($aa['conseguidas'] as $dl_otra => $num) {
                // conseguidas - cedidas
                $a_plazas[$dl]['disponibles'][$dl_otra] = $num;
                $total_disponibles += $num;
            }
            $a_plazas[$dl]['total_disponibles'] = $total_disponibles;
            $tot_disponibles += $total_disponibles;
        }
        // Ocupadas (de las disponibles)
        foreach ($a_plazas as $dl => $aa) {
            foreach ($aa['disponibles'] as $dl_otra => $num) {
                $ocupadas = $AsistenteActividadService->getPlazasOcupadasPorDl($id_activ, $dl, $dl_otra);
                $a_plazas[$dl]['ocupadas'][$dl_otra] = $ocupadas;
            }
            $a_plazas[$dl]['total_ocupadas'] = \array_sum($a_plazas[$dl]['ocupadas']);
            $tot_ocupadas += $a_plazas[$dl]['total_ocupadas'];
        }

        $a_plazas['total']['actividad'] = $plazas_totales;
        $a_plazas['total']['calendario'] = $tot_calendario;
        $a_plazas['total']['cedidas'] = $tot_cedidas;
        $a_plazas['total']['conseguidas'] = $tot_conseguidas;
        $a_plazas['total']['disponibles'] = $tot_disponibles;
        $a_plazas['total']['ocupadas'] = $tot_ocupadas;

        ksort($a_plazas);
        return $a_plazas;
    }

    /**
     * Plazas disponibles menos las ocupadas
     *
     * @param string $dl delegación, si esta vacio: la mia.
     * @return integer numero de plazas libres para la dl
     *
     */
    public function getLibres(string $dl = '')
    {
        if (empty($dl)) {
            $dl = ConfigGlobal::mi_delef();
        }

        $a_plazas = $this->getResumen();
        // Puede no tener plazas asignadas...
        if (isset($a_plazas[$dl]['total_disponibles']) && isset ($a_plazas[$dl]['total_ocupadas'])) {
            $libres = $a_plazas[$dl]['total_disponibles'] - $a_plazas[$dl]['total_ocupadas'];
        } else {
            $libres = 0;
        }
        return $libres;
    }

    /**
     * Devuelve el nombre del popietario de la primera plaza libre
     *
     */
    public function getPropiedadPlazaLibre()
    {
        /*
        puede ser una plaza propia o una cedida.
         */
        $err_txt = '';

        $id_activ = $this->getId_activ();
        $mi_dl = ConfigGlobal::mi_delef();

        $propiedad = [];
        $pl_propias = $this->getPlazasPropias();

        if ($pl_propias > 0) {
            $plazasOcupadas = $this->AsistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dl, $mi_dl);
            if ($plazasOcupadas < $pl_propias) {
                $propiedad["$mi_dl>$mi_dl"] = "$mi_dl ($plazasOcupadas de $pl_propias)";
            } else {
                $err_txt = _("Ya están todas las plazas ocupadas");
            }
        }

        // Si no quedan, ver si dispongo de otras
        if (empty($propiedad)) {
            //Conseguidas
            // plazas de calendario de cada dl
            $cActividadPlazas = $this->ActividadPlazasRepository->getActividadesPlazas(array('id_activ' => $id_activ));
            foreach ($cActividadPlazas as $oActividadPlazas) {
                $id_dl_otra = $oActividadPlazas->getId_dl();
                $dl_otra = $this->getDlText($id_dl_otra);

                $aCedidas = $oActividadPlazas->getArrayCedidas();
                if (!empty($aCedidas)) {
                    foreach ($aCedidas as $dl_2 => $num_plazas) {
                        if ($mi_dl == $dl_2) {
                            $ocu = $this->AsistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dl, $dl_otra);
                            if ($ocu < $num_plazas) {
                                $propiedad["$dl_otra>$dl_2"] = "$dl_otra ($ocu de $num_plazas)";
                            }
                        }
                    }
                }
            }
        }
        if (empty($propiedad)) {
            $rta['success'] = FALSE;
            $rta['mensaje'] = $err_txt;
        } else {
            $rta['success'] = TRUE;
            $rta['mensaje'] = $err_txt;
            $rta['propiedad'] = $propiedad;
        }

        return $rta;
    }

    /**
     * Plazas de la propia dl. Si no está publicado són las de la actividad, o
     * las de la casa si no están definidas.
     *
     * @return integer
     */
    private function getPlazasPropias()
    {
        $id_activ = $this->getId_activ();
        $mi_dl = ConfigGlobal::mi_delef();

        $oActividad = $this->ActividadAllRepository->findById($id_activ);
        $publicado = $oActividad->isPublicado();
        // Si no está publicada no tiene plazas de calendario.
        // Se toman todas la de la actividad como propias.
        if (!is_true($publicado)) {
            $pl_propias = $this->getPlazasTotales();
        } else {
            // las que me corresponden por calendario - las cedidas
            $pl_calendario = $this->getPlazasCalendario($mi_dl);
            $pl_cedidas = $this->getPlazasCedidas($mi_dl);
            $pl_propias = $pl_calendario - $pl_cedidas;
        }
        return $pl_propias;
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    public function setId_activ($iid_activ = '')
    {
        $this->iid_activ = $iid_activ;
    }

    protected function getId_activ()
    {
        if (!isset($this->iid_activ)) {
            exit('error');
        }
        return $this->iid_activ;
    }

    protected function getDl_org()
    {
        if (!isset($this->dl_org)) {
            $id_activ = $this->getId_activ();
            $oActividad = $this->ActividadAllRepository->findById($id_activ);
            $this->dl_org = $oActividad->getDl_org();
        }
        return $this->dl_org;
    }

    // array de id=>dl
    protected function setArrayDl()
    {
        if (!isset($this->a_dele)) {
            $cDelegaciones = $this->DelegacionRepository->getDelegaciones(array('_ordre' => 'region,dl'));
            $this->a_dele = [];
            $this->a_id_dele = [];
            foreach ($cDelegaciones as $oDelegacion) {
                $dl = $oDelegacion->getDlVo()?->value() ?? '';
                if (ConfigGlobal::mi_sfsv() == 2) {
                    $dl .= 'f';
                }
                $id_dl = $oDelegacion->getIdDlVo()?->value() ?? 0;
                $a_dele[$id_dl] = $dl;
                $a_id_dele[$dl] = $id_dl;
            }
            $this->a_dele = $a_dele;
            $this->a_id_dele = $a_id_dele;
        }
        return true;
    }

    protected function getDlText($id_dl)
    {
        if (!isset($this->a_dele)) {
            $this->setArrayDl();
        }
        $a_dele = $this->a_dele;
        return $a_dele[$id_dl];
    }

    protected function getDlId($dl)
    {
        if (!isset($this->a_id_dele)) {
            $this->setArrayDl();
        }
        $a_id_dele = $this->a_id_dele;
        return $a_id_dele[$dl];
    }
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
