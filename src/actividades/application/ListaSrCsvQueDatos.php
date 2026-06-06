<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;

/**
 * Devuelve los valores por defecto del formulario `lista_sr_csv_que`,
 * a partir de la preferencia guardada del usuario (tipo 'busqueda_activ_sr').
 *
 * Concentra el acceso a `PreferenciaRepository` y evita que el controlador
 * frontend toque `src/`.
 */
final class ListaSrCsvQueDatos
{
    public function __construct(
        private PreferenciaRepositoryInterface $preferenciaRepository,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function ejecutar(): array
    {
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $tipo = 'busqueda_activ_sr';
        $PreferenciaRepository = $this->preferenciaRepository;
        $oPreferencia = $PreferenciaRepository->findById($id_usuario, $tipo);
        $json_busqueda = $oPreferencia !== null ? ($oPreferencia->getPreferencia() ?? '') : '';
        if ($json_busqueda === '') {
            $a_status = [1, 2];
            $periodo = 'curso_ca';
            $a_tipo_activ = [1, 3];
            $sel_ubis = '';
        } else {
            $oBusqueda = json_decode($json_busqueda, true);
            if (!is_array($oBusqueda)) {
                $a_status = [1, 2];
                $periodo = 'curso_ca';
                $a_tipo_activ = [1, 3];
                $sel_ubis = '';
            } else {
                $busqueda = [];
                foreach ($oBusqueda as $k => $v) {
                    if (is_string($k)) {
                        $busqueda[$k] = $v;
                    }
                }
                $a_status = $this->decodeIntList($this->scalarField($busqueda, 'status'));
                $periodo = $this->scalarField($busqueda, 'periodo', 'curso_ca');
                $a_tipo_activ = $this->decodeIntList($this->scalarField($busqueda, 'tipo_activ'));
                $a_ubis = $this->decodeIntList($this->scalarField($busqueda, 'ubis_compartidos'));
                $sel_ubis = implode(',', array_map(static fn (int $id) => (string) $id, $a_ubis));
            }
        }

        $chk_status_1 = '';
        $chk_status_2 = '';
        foreach ($a_status as $val) {
            if ($val === 1) {
                $chk_status_1 = 'checked';
            }
            if ($val === 2) {
                $chk_status_2 = 'checked';
            }
        }

        $chk_activ_crt = '';
        $chk_activ_cv = '';
        foreach ($a_tipo_activ as $tipo_activ) {
            if ($tipo_activ === 1) {
                $chk_activ_crt = 'checked';
            }
            if ($tipo_activ === 3) {
                $chk_activ_cv = 'checked';
            }
        }

        return [
            'periodo' => $periodo,
            'sel_ubis' => $sel_ubis,
            'chk_status_1' => $chk_status_1,
            'chk_status_2' => $chk_status_2,
            'chk_activ_crt' => $chk_activ_crt,
            'chk_activ_cv' => $chk_activ_cv,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function scalarField(array $data, string $key, string $default = '[]'): string
    {
        $val = $data[$key] ?? $default;
        return is_scalar($val) ? (string) $val : $default;
    }

    /**
     * @return list<int>
     */
    private function decodeIntList(string $json): array
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }
        $result = [];
        foreach ($decoded as $item) {
            if (is_int($item)) {
                $result[] = $item;
            } elseif (is_string($item) && is_numeric($item)) {
                $result[] = (int) $item;
            }
        }
        return $result;
    }
}
