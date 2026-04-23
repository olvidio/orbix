<?php

namespace src\cartaspresentacion\application;

use core\ConfigGlobal;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\CuadrosLabor;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;

use function core\strsinacentocmp;
use function core\strtoupper_dlb;

/**
 * Data builder: agrupa las cartas de presentacion por tipo de labor,
 * delegacion (si aplica) y poblacion, y devuelve el HTML ya formateado.
 *
 * Sucesor de `apps/cartaspresentacion/controller/cartas_presentacion_lista.php`.
 *
 * Admite tres modos ({@see self::execute()}):
 *  - `lista_dl`   → solo cartas cuya dl sea la del usuario.
 *  - `lista_todo` → todas las delegaciones.
 *  - `get`        → filtrado por `poblacion`, `pais`, `region`, `dl`.
 *
 * La respuesta tiene la forma:
 *   ['html_lista' => string, 'html_errores' => string]
 * de modo que el frontend solo tiene que imprimirla.
 */
final class CartasPresentacionListaData
{
    /** @var string */
    private string $msgError = '';

    /**
     * @param array{
     *   que?: string,
     *   poblacion?: string,
     *   pais?: string,
     *   region?: string,
     *   dl?: string
     * } $input
     * @return array{html_lista:string, html_errores:string}
     */
    public static function execute(array $input): array
    {
        return (new self())->run($input);
    }

    /**
     * @param array<string,mixed> $input
     * @return array{html_lista:string, html_errores:string}
     */
    private function run(array $input): array
    {
        $que = (string)($input['que'] ?? '');

        $ordenar_dl = 1;
        $a_mega = [];

        if ($que === 'lista_dl') {
            $repo = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
            $mi_dele = ConfigGlobal::mi_delef();
            $a_mega = $this->cartasRepoAgrupadas($repo, $ordenar_dl, $mi_dele);
        } elseif ($que === 'lista_todo') {
            $repo = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);
            $a_mega = $this->cartasRepoAgrupadas($repo, $ordenar_dl, null);
        } elseif ($que === 'get') {
            $a_mega = $this->cartasFiltro($input, $ordenar_dl);
        }

        $html_lista = '';
        if (!empty($a_mega)) {
            $html_lista = $this->renderListaCartas($a_mega, $ordenar_dl);
        }

        $html_errores = '';
        if ($this->msgError !== '') {
            $html_errores = '<br>'
                . _("Centros con el campo 'tipo labor' mal puesto:")
                . '<br>'
                . $this->msgError;
        }

        return [
            'html_lista' => $html_lista,
            'html_errores' => $html_errores,
        ];
    }

    /**
     * Todas las cartas del repo agrupadas; si `$soloDl` no es null, filtra
     * por esa delegacion.
     *
     * @return array<mixed>
     */
    private function cartasRepoAgrupadas($repo, int $ordenar_dl, ?string $soloDl): array
    {
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $cCartas = $repo->getCartasPresentacion();
        $a_mega_tmp = [];
        foreach ($cCartas as $oCarta) {
            $id_ubi = $oCarta->getId_ubi();
            $oCentro = $CentroRepository->findById($id_ubi);
            if ($oCentro === null) {
                continue;
            }
            if ($soloDl !== null && $oCentro->getDl() !== $soloDl) {
                continue;
            }
            $a_mega_tmp[] = $this->megaArray($oCarta, $oCentro, $ordenar_dl);
        }
        return empty($a_mega_tmp) ? [] : array_merge_recursive(...$a_mega_tmp);
    }

    /**
     * Modo `get` del dispatcher original: filtra por poblacion/pais/region/dl.
     *
     * @param array<string,mixed> $input
     * @return array<mixed>
     */
    private function cartasFiltro(array $input, int $ordenar_dl): array
    {
        $Qpoblacion = (string)($input['poblacion'] ?? '');
        $Qpais = (string)($input['pais'] ?? '');
        $Qregion = (string)($input['region'] ?? '');
        $Qdl = (string)($input['dl'] ?? '');

        $repoCarta = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);

        $a_mega = [];

        if ($Qpais !== '' || $Qpoblacion !== '') {
            $aWhere = [];
            $aOperador = [];
            if ($Qpoblacion !== '') {
                $aWhere['poblacion'] = $Qpoblacion;
                $aOperador['poblacion'] = 'sin_acentos';
            }
            if ($Qpais !== '') {
                $aWhere['pais'] = $Qpais;
                $aOperador['pais'] = 'sin_acentos';
            }

            $DireccionCentroRepository = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
            $cDirecciones = $DireccionCentroRepository->getDirecciones($aWhere, $aOperador);
            $RelCtrDir = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);

            $a_mega_tmp = [];
            foreach ($cDirecciones as $oDireccion) {
                $id_direccion = $oDireccion->getId_direccion();
                $cId_ubis = $RelCtrDir->getUbisPorDireccion($id_direccion);
                foreach ($cId_ubis as $aUbi) {
                    $oCentro = $CentroRepository->findById($aUbi['id_ubi']);
                    if ($oCentro === null || !$oCentro->isActive()) {
                        continue;
                    }
                    $oCarta = $repoCarta->findById($oCentro->getId_ubi(), $id_direccion);
                    if ($oCarta !== null) {
                        $a_mega_tmp[] = $this->megaArray($oCarta, $oCentro, $ordenar_dl);
                    }
                }
            }
            if (!empty($a_mega_tmp)) {
                $a_mega = array_merge_recursive(...$a_mega_tmp);
            }

            // Extiendo la busqueda al campo `zona`.
            if ($Qpoblacion !== '') {
                $cCartasZona = $repoCarta->getCartasPresentacion(
                    ['zona' => $Qpoblacion],
                    ['zona' => 'sin_acentos']
                );
                $a_mega_tmp = [];
                foreach ($cCartasZona as $oCarta) {
                    $oCentro = $CentroRepository->findById($oCarta->getId_ubi());
                    if ($oCentro === null) {
                        continue;
                    }
                    $a_mega_tmp[] = $this->megaArray($oCarta, $oCentro, $ordenar_dl);
                }
                if (!empty($a_mega_tmp)) {
                    $a_mega = array_merge_recursive(...$a_mega_tmp);
                }
            }
        }

        if ($Qregion !== '') {
            $a_mega = array_merge_recursive($a_mega, $this->cartasPorCondicionCentro(
                ['region' => $Qregion],
                $ordenar_dl
            ));
        }

        if ($Qdl !== '') {
            $a_mega = array_merge_recursive($a_mega, $this->cartasPorCondicionCentro(
                ['dl' => $Qdl],
                $ordenar_dl
            ));
        }

        return $a_mega;
    }

    /**
     * @param array<string,string> $aWhere
     * @return array<mixed>
     */
    private function cartasPorCondicionCentro(array $aWhere, int $ordenar_dl): array
    {
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $repoCarta = $GLOBALS['container']->get(CartaPresentacionRepositoryInterface::class);

        $cCentros = $CentroRepository->getCentros($aWhere, []);
        $a_mega_tmp = [];
        foreach ($cCentros as $oCentro) {
            $cCartas = $repoCarta->getCartasPresentacion(['id_ubi' => $oCentro->getId_ubi()]);
            foreach ($cCartas as $oCarta) {
                $a_mega_tmp[] = $this->megaArray($oCarta, $oCentro, $ordenar_dl);
            }
        }
        return empty($a_mega_tmp) ? [] : array_merge_recursive(...$a_mega_tmp);
    }

    /**
     * Copia casi textual de `mega_array()` del controlador legacy.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    private function megaArray(CartaPresentacion $oPresentacion, $oCentro, int $ordenar_dl): array
    {
        $a_mega = [];
        $id_ubi = $oPresentacion->getId_ubi();
        $id_direccion = $oPresentacion->getId_direccion();
        $pres_nom = (string)$oPresentacion->getPres_nom();
        $pres_telf = (string)$oPresentacion->getPres_telf();
        $pres_mail = (string)$oPresentacion->getPres_mail();
        $zona = (string)$oPresentacion->getZona();

        $dl = $oCentro->getDl();
        $region = $oCentro->getRegion();
        $tipo_ctr = $oCentro->getTipo_ctr();
        $tipo_labor = $oCentro->getTipo_labor();
        $id_ctr_padre = $oCentro->getId_ctr_padre();

        $DireccionRepository = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);
        $oDireccion = $DireccionRepository->findById($id_direccion);
        $direccion = $oDireccion?->getDireccionVo()?->value() ?? '';
        $poblacion = (string)($oDireccion?->getPoblacion() ?? '');
        $c_p = (string)($oDireccion?->getC_p() ?? '');
        $pais = (string)($oDireccion?->getPais() ?? '');
        $nom_sede = (string)($oDireccion?->getNom_sede() ?? '');
        $a_p = '';

        $obj_pau_centro = $this->objPauFromCentro($oCentro);
        $telf = UbiTelecoService::texto($obj_pau_centro, (int)$id_ubi, 'telf', '*', ' / ');
        $fax = UbiTelecoService::texto($obj_pau_centro, (int)$id_ubi, 'fax', '*', ' / ');
        if (!empty($fax)) {
            $fax = self::formatTelf($fax);
            $telf .= ' fax:' . $fax;
        }
        // Si es una dl o r fuera de España, pongo el e-mail del centro.
        if ($region !== 'H' && (strpos($tipo_ctr, 'cr') !== false || strpos($tipo_ctr, 'dl') !== false)) {
            // 15 es el id para otros asuntos (20 es para asuntos de gobierno).
            $mail = UbiTelecoService::texto($obj_pau_centro, (int)$id_ubi, 'e-mail', '15', ' / ');
            if (!empty($mail)) {
                $pres_mail .= 'mail casa: ' . $mail;
            }
        }

        $a_direccion = [];
        if (!empty($id_ctr_padre)) {
            $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
            $oCentro1 = $CentroRepository->findById($id_ctr_padre);
            if ($oCentro1 !== null) {
                $cDirecciones1 = $oCentro1->getDirecciones();
                if (!empty($cDirecciones1)) {
                    $oDireccion1 = $cDirecciones1[0];
                    $obj_pau_ctr_padre = $this->objPauFromCentro($oCentro1);
                    $telf1 = UbiTelecoService::texto($obj_pau_ctr_padre, (int)$id_ctr_padre, 'telf', '*', ' / ');
                    $a_direccion[] = [
                        'direccion' => $oDireccion1->getDireccionVo()?->value() ?? '',
                        'a_p' => $oDireccion1->getA_p(),
                        'c_p' => $oDireccion1->getC_p(),
                        'poblacion' => $oDireccion1->getPoblacion(),
                        'telf' => $telf1,
                    ];
                }
            }
        }
        // Si hay una segunda direccion del centro que sea principal, se añade.
        $GesCtrxDireccion = $GLOBALS['container']->get(RelacionCentroDireccionRepositoryInterface::class);
        $cCtrxDirecciones = $GesCtrxDireccion->getDireccionesPorUbi($id_ubi);
        if (!empty($cCtrxDirecciones)) {
            foreach ($cCtrxDirecciones as $aCtrxDireccion) {
                if (($aCtrxDireccion['principal'] ?? false) === false) {
                    continue;
                }
                $id_dir = (int)$aCtrxDireccion['id_direccion'];
                if ($id_dir !== (int)$id_direccion) {
                    $oDireccion2 = $DireccionRepository->findById($id_dir);
                    if ($oDireccion2 === null) {
                        continue;
                    }
                    $a_direccion[] = [
                        'direccion' => $oDireccion2->getDireccionVo()?->value() ?? '',
                        'a_p' => $oDireccion2->getA_p(),
                        'c_p' => $oDireccion2->getC_p(),
                        'poblacion' => $oDireccion2->getPoblacion(),
                        'telf' => '',
                    ];
                }
            }
        }

        $a_texto = [
            'pres_nom' => $pres_nom,
            'pres_telf' => $pres_telf,
            'pres_mail' => $pres_mail,
            'direccion' => $direccion,
            'nom_sede' => $nom_sede,
            'a_p' => $a_p,
            'c_p' => $c_p,
            'poblacion' => $poblacion,
            'telf' => $telf,
            'a_direccion' => $a_direccion,
        ];

        $oTipoLabor = new CuadrosLabor();
        $aTiposLabor = $oTipoLabor->getTxtTiposLabor();
        $aTipo = [];
        $edad = '';
        if (!empty($tipo_labor)) {
            if (($tipo_labor & 128) === 128) { $aTipo[] = $aTiposLabor[128]; }
            if (($tipo_labor & 256) === 256) { $aTipo[] = $aTiposLabor[256]; }
            if (($tipo_labor & 64) === 64)   { $aTipo[] = $aTiposLabor[64];  }
            if (($tipo_labor & 32) === 32)   { $aTipo[] = $aTiposLabor[32];  }
            if ($tipo_ctr === 'dl' || $tipo_ctr === 'cr') {
                $aTipo[] = 'otras r';
            }
            if (($tipo_labor & 2) === 2) {
                $edad .= $aTiposLabor[2];
            }
            if (($tipo_labor & 1) === 1) {
                $edad .= $edad !== '' ? ', ' : '';
                $edad .= $aTiposLabor[1];
            }
            if (($tipo_labor & 4) === 4) {
                $edad .= $edad !== '' ? ', ' : '';
                $edad .= $aTiposLabor[4];
            }
            if (($tipo_labor & 8) === 8) {
                $edad .= $edad !== '' ? ', ' : '';
                $edad .= $aTiposLabor[8];
            }
        } else {
            $this->msgError .= $this->msgError !== '' ? ', ' : '';
            $this->msgError .= $oCentro->getNombre_ubi();
        }
        if ($zona !== '') {
            $edad .= "<br>$zona";
        }

        $poblacion .= $pais === '' ? '' : '<br>(' . $pais . ')';
        $celdas = $this->datosACeldas($a_texto);
        if ($ordenar_dl === 1) {
            foreach ($aTipo as $tipo) {
                $a_mega[$tipo][$dl][$poblacion][$edad] = $celdas;
            }
        } else {
            foreach ($aTipo as $tipo) {
                $a_mega[$tipo][$poblacion][$edad] = $celdas;
            }
        }

        return $a_mega;
    }

    private function objPauFromCentro($oCentro): string
    {
        if ($oCentro === null) {
            return 'Centro';
        }
        $tipo_ubi = method_exists($oCentro, 'getTipo_ubi') ? (string)$oCentro->getTipo_ubi() : '';
        $dl = method_exists($oCentro, 'getDl') ? (string)$oCentro->getDl() : '';
        if ($tipo_ubi === 'ctrex') {
            return 'CentroEx';
        }
        if ($tipo_ubi === 'ctrdl' || $tipo_ubi === 'ctrsf') {
            return ($dl === ConfigGlobal::mi_delef()) ? 'CentroDl' : 'Centro';
        }
        return 'Centro';
    }

    /**
     * @param array<string,mixed> $a_texto
     */
    private function datosACeldas(array $a_texto): string
    {
        $pres_nom = $a_texto['pres_nom'];
        $pres_telf = self::formatTelf($a_texto['pres_telf']);
        $pres_mail = $a_texto['pres_mail'];
        $direccion = $a_texto['direccion'];
        $nom_sede = $a_texto['nom_sede'];
        $a_p = $a_texto['a_p'];
        $c_p = $a_texto['c_p'];
        $poblacion = $a_texto['poblacion'];
        $telf = self::formatTelf($a_texto['telf']);
        $a_direccion = $a_texto['a_direccion'];

        $nom_sede = $nom_sede === '' ? '' : "$nom_sede<br>";
        $a_p = $a_p === '' ? '' : "$a_p<br>";
        $col1 = "$pres_nom<br>$nom_sede$direccion<br>$a_p$c_p $poblacion";
        $col2 = "$pres_telf<br>$pres_mail<br>$telf";
        if (!empty($a_direccion)) {
            foreach ($a_direccion as $aa_direccion) {
                $d1 = $aa_direccion['direccion'];
                $c1 = $aa_direccion['c_p'];
                $p1 = $aa_direccion['poblacion'];
                $tf1 = self::formatTelf($aa_direccion['telf']);

                $col1 .= "<br>---<br>$d1<br>$c1  $p1";
                $col2 .= "<br>---<br>$tf1";
            }
        }

        return "<td class=\"line-top\">$col1</td><td class=\"line-top\">$col2</td>";
    }

    /**
     * @param array<mixed> $a_mega
     */
    private function renderListaCartas(array $a_mega, int $ordenar_dl): string
    {
        $html = '';
        $class = 'class="line-top"';
        ksort($a_mega);

        if ($ordenar_dl === 1) {
            foreach ($a_mega as $tipo => $a_dl_pob_edad) {
                uksort($a_dl_pob_edad, 'core\\strsinacentocmp');
                $html .= '<h3>';
                $html .= sprintf(_("Cartas de presentación de %s"), $tipo);
                $html .= '</h3>';
                $dl_anterior = '';
                foreach ($a_dl_pob_edad as $dl => $a_pob_edad) {
                    uksort($a_pob_edad, 'core\\strsinacentocmp');
                    if ($dl !== $dl_anterior) {
                        $html .= "<h3>$dl - $tipo</h3>";
                    }
                    $html .= '<table>';
                    $html .= $this->renderPoblacionesTabla($a_pob_edad, $class);
                    $html .= '</table>';
                    $dl_anterior = $dl;
                }
            }
        } else {
            foreach ($a_mega as $tipo => $a_pob_edad) {
                uksort($a_pob_edad, 'core\\strsinacentocmp');
                $html .= '<h3>';
                $html .= sprintf(_("Cartas de presentación de %s"), $tipo);
                $html .= '</h3>';
                $html .= '<table>';
                $html .= $this->renderPoblacionesTabla($a_pob_edad, $class);
                $html .= '</table>';
            }
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $a_pob_edad
     */
    private function renderPoblacionesTabla(array $a_pob_edad, string $class): string
    {
        $html = '';
        $poblacion_anterior = '';
        foreach ($a_pob_edad as $poblacion => $a_edad) {
            krsort($a_edad); // primero m, despues j
            if ($poblacion !== $poblacion_anterior || $poblacion === '') {
                $html .= "<tr><td $class>" . strtoupper_dlb((string)$poblacion) . "</td>";
            }
            $f = 0;
            foreach ($a_edad as $edad => $texto) {
                $f++;
                if ($f > 1) {
                    $html .= '<tr><td></td>';
                }
                if (is_array($texto)) {
                    $ff = 0;
                    foreach ($texto as $txt) {
                        $ff++;
                        if ($ff > 1) {
                            $html .= "<tr><td></td><td $class></td>";
                        } else {
                            $html .= "<td $class>$edad</td>";
                        }
                        $html .= "$txt</tr>";
                    }
                } else {
                    $html .= "<td $class>$edad</td>$texto</tr>";
                }
            }
            $poblacion_anterior = $poblacion;
        }
        return $html;
    }

    public static function formatTelf(?string $number): string
    {
        if ($number === null || $number === '') {
            return '';
        }
        $regex = "/^(\(?\d{3}\)?)?[- .]?(\d{3})[- .]?(\d{3})[- .]?( \(?.*\)?)?$/";
        $a_telf = explode(' / ', $number);
        $formattedValue = [];
        foreach ($a_telf as $tel) {
            $formattedValue[] = preg_replace($regex, "\\1 \\2 \\3\\4", $tel);
        }
        return implode(' / ', $formattedValue);
    }
}
