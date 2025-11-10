<?php

namespace src\ubis\application\services;

use core\ConfigGlobal;
use src\ubis\application\repositories\DelegacionRepository;
use web\Desplegable;

/**
 * Helper para construir desplegables (select) de Delegaciones
 * replicando el comportamiento legacy de GestorDelegacion.
 *
 * Criterios generales:
 * - Muestra solo delegaciones activas (status = true)
 * - Orden por nombre_dl, salvo métodos con etiqueta compuesta donde se ordena por la propia etiqueta.
 * - value/label según cada método (ver docblocks).
 */
final class DelegacionDropdown
{
    /**
     * Imitación de GestorDelegacion::getListaDlURegionesFiltro()
     * value = "dl|dl{sf}", label = "nombre_dl (dl{sf})"
     * $sfsv: 1 para sv, 2 para sf. Si 0 toma ConfigGlobal::mi_sfsv().
     */
    public static function dlURegionesFiltro(int $sfsv = 0, string $nombre = 'filtro_lugar', bool $conBlanco = true): Desplegable
    {
        if ($sfsv === 0) { $sfsv = ConfigGlobal::mi_sfsv(); }
        $sf = ($sfsv == 2) ? 'f' : '';

        $repo = new DelegacionRepository();
        $delegaciones = $repo->getDelegaciones(['status' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $value = 'dl|' . ($dl->getDlVo()?->value() ?? '') . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getDlVo()?->value() ?? '') . $sf . ')';
            $opciones[$value] = $label;
        }
        // Orden por etiqueta (segunda columna en legacy)
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        $despl = new Desplegable();
        $despl->setNombre($nombre);
        if ($conBlanco) {
            $despl->setBlanco(true);
        }
        $despl->setOpciones($opciones);
        return $despl;
    }
    /**
     * Devuelve un Desplegable con las delegaciones activas ordenadas por nombre.
     * value = dl, label = nombre_dl
     */
    public static function activasOrdenNombre(string $nombre = 'dl', bool $conBlanco = true): Desplegable
    {
        $repo = new DelegacionRepository();
        $delegaciones = $repo->getDelegaciones(['status' => true, '_ordre' => 'nombre_dl']);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $opciones[$dl->getDlVo()?->value() ?? ''] = $dl->getNombreDlVo()?->value() ?? '';
        }

        $despl = new Desplegable();
        $despl->setNombre($nombre);
        if ($conBlanco) {
            $despl->setBlanco(true);
        }
        $despl->setOpciones($opciones);
        return $despl;
    }

    /**
     * Devuelve delegaciones activas filtradas por regiones dadas (array de siglas de región).
     * value = dl, label = nombre_dl
     */
    public static function byRegiones(array $regiones, string $nombre = 'dl', bool $conBlanco = true): Desplegable
    {
        $repo = new DelegacionRepository();
        $aWhere = ['status' => true, '_ordre' => 'nombre_dl'];
        $aOper = [];
        if (!empty($regiones)) {
            // operador IN de la infraestructura: usar 'region' => 'IN' con valor array si está soportado;
            // en este proyecto se usa patrón de aOperators. Emulamos con 'region' => 'IN'.
            $aWhere['region'] = "'" . implode('","', $regiones) . "'";
            $aOper['region'] = 'IN';
        }
        $delegaciones = $repo->getDelegaciones($aWhere, $aOper);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $opciones[$dl->getDlVo()?->value() ?? ''] = $dl->getNombreDlVo()?->value() ?? '';
        }

        $despl = new Desplegable();
        $despl->setNombre($nombre);
        if ($conBlanco) {
            $despl->setBlanco(true);
        }
        $despl->setOpciones($opciones);
        return $despl;
    }

    /**
     * Imitación de GestorDelegacion::getListaRegDele()
     * value = "region-dl{sf}", label = "nombre_dl (dl{sf})"
     * Donde {sf} es 'f' si sfsv=2, o '' en otro caso (como en legacy).
     */
    public static function listaRegDele(bool $incluirPropia = true, string $nombre = 'dl'): Desplegable
    {
        $sf = (ConfigGlobal::mi_sfsv() == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $repo = new DelegacionRepository();
        $delegaciones = $repo->getDelegaciones(['status' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            if (!$incluirPropia && ($dl->getDlVo()?->value() ?? '') === $propia) { continue; }
            $value = ($dl->getRegionVo()?->value() ?? '') . '-' . ($dl->getDlVo()?->value() ?? '') . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getDlVo()?->value() ?? '') . $sf . ')';
            $opciones[$value] = $label;
        }
        // Orden por etiqueta
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        $despl = new Desplegable();
        $despl->setNombre($nombre);
        $despl->setBlanco(true);
        $despl->setOpciones($opciones);
        return $despl;
    }

    /**
     * Imitación de GestorDelegacion::getListaDelegacionesURegiones()
     * value = "dl{sf}", label = "nombre_dl (region-dl{sf})"
     * $sfsv: 1 para sv, 2 para sf. Si 0 toma ConfigGlobal::mi_sfsv().
     */
    public static function delegacionesURegiones(int $sfsv = 0, bool $incluirPropia = true, string $nombre = 'dl'): Desplegable
    {
        if ($sfsv === 0) { $sfsv = ConfigGlobal::mi_sfsv(); }
        $sf = ($sfsv == 2) ? 'f' : '';
        $propia = ConfigGlobal::mi_dele();

        $repo = new DelegacionRepository();
        $delegaciones = $repo->getDelegaciones(['status' => true]);

        $opciones = [];
        foreach ($delegaciones as $dl) {
            $dlCode = $dl->getDlVo()?->value() ?? '';
            if (!$incluirPropia && $dlCode === $propia) { continue; }
            $value = $dlCode . $sf;
            $label = ($dl->getNombreDlVo()?->value() ?? '') . ' (' . ($dl->getRegionVo()?->value() ?? '') . '-' . $dlCode . $sf . ')';
            $opciones[$value] = $label;
        }
        // Orden por etiqueta
        asort($opciones, SORT_NATURAL | SORT_FLAG_CASE);

        $despl = new Desplegable();
        $despl->setNombre($nombre);
        $despl->setBlanco(true);
        $despl->setOpciones($opciones);
        return $despl;
    }
}
