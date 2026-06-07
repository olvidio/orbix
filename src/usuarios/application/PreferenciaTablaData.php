<?php

namespace src\usuarios\application;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

/**
 * Devuelve las preferencias de usuario necesarias para renderizar una tabla
 * (HTML simple o SlickGrid) en el front.
 */
final class PreferenciaTablaData
{
    public function __construct(
        private PreferenciaRepositoryInterface $preferenciaRepository,
    ) {
    }

    /**
     * @return array{formato_tabla: string, slickgrid: array<string, mixed>|null}
     */
    public function execute(string $id_tabla = ''): array
    {
        $id_usuario = ConfigGlobal::mi_id_usuario();

        $formato_tabla = '';
        $oPref = $this->preferenciaRepository->findById($id_usuario, 'tabla_presentacion');
        if ($oPref !== null) {
            $formato_tabla = (string) ($oPref->getPreferenciaVo()?->value() ?? '');
        }

        $slickgrid = null;
        if ($id_tabla !== '') {
            $idioma = ConfigGlobal::mi_Idioma();
            $tipo = 'slickGrid_' . $id_tabla . '_' . $idioma;
            foreach ([$id_usuario, 44] as $uid) {
                $oPref = $this->preferenciaRepository->findById((int)$uid, $tipo);
                if ($oPref === null) {
                    continue;
                }
                $sPrefs = (string) ($oPref->getPreferenciaVo()?->value() ?? '');
                if ($sPrefs === '') {
                    continue;
                }
                $aPrefs = json_decode($sPrefs, true);
                if (is_array($aPrefs)) {
                    $slickgrid = $aPrefs;
                    break;
                }
            }
        }

        return [
            'formato_tabla' => $formato_tabla,
            'slickgrid' => $slickgrid,
        ];
    }
}
