<?php

namespace src\cambios\application;

use src\cambios\domain\entity\CambioUsuarioPropiedadPref;

/**
 * Operacion de calculo (sin persistencia): construye el texto de preview
 * de la condicion y el array serializado con los mismos datos. Se usa para
 * refrescar la celda de condicion tras configurarla en el modal antes de
 * guardar el conjunto con `CambioUsuarioPropiedadPrefGuardarTodas`.
 *
 * Sucesor de la rama `guardar_cond` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioPropiedadPrefPreview
{
    /**
     * @param array<string, mixed> $input
     * @return array{
     *   error: string,
     *   id_item: int,
     *   objeto: string,
     *   propiedad: string,
     *   condicion: string,
     *   cambio_prop: string,
     * }
     */
    public function execute(array $input): array
    {
        $id_item = isset($input['id_item']) && is_numeric($input['id_item']) ? (int) $input['id_item'] : 0;
        $objeto = isset($input['objeto']) && is_string($input['objeto']) ? $input['objeto'] : '';
        $propiedad = isset($input['propiedad']) && is_string($input['propiedad']) ? $input['propiedad'] : '';
        $operador = isset($input['operador']) && is_string($input['operador']) ? $input['operador'] : '';
        $valor = isset($input['valor']) && is_string($input['valor']) ? $input['valor'] : '';
        $valor_old = isset($input['valor_old']) && is_string($input['valor_old']) ? $input['valor_old'] : '';
        $valor_new = isset($input['valor_new']) && is_string($input['valor_new']) ? $input['valor_new'] : '';
        $a_id_ubi = $input['id_ubi'] ?? [];

        if ($propiedad === 'id_ubi' && is_array($a_id_ubi) && $a_id_ubi !== []) {
            $valor = implode(',', array_map(
                static fn ($v) => is_scalar($v) ? (string) $v : '',
                $a_id_ubi
            ));
        }

        $oProp = new CambioUsuarioPropiedadPref();
        if ($propiedad !== '') {
            $oProp->setPropiedad($propiedad);
        }
        if ($operador !== '') {
            $oProp->setOperador($operador);
        }
        if ($valor !== '') {
            $oProp->setValor($valor);
        }
        $oProp->setValor_old($valor_old !== '');
        $oProp->setValor_new($valor_new !== '');

        $condicion = (string) $oProp->getTextCambio();

        $cambio_prop = json_encode([
            'iid_item' => $id_item,
            'spropiedad' => $propiedad,
            'soperador' => $operador,
            'svalor' => $valor,
            'bvalor_old' => $valor_old !== '' ? 't' : 'f',
            'bvalor_new' => $valor_new !== '' ? 't' : 'f',
        ]);

        return [
            'error' => '',
            'id_item' => $id_item,
            'objeto' => $objeto,
            'propiedad' => $propiedad,
            'condicion' => $condicion,
            'cambio_prop' => is_string($cambio_prop) ? $cambio_prop : '',
        ];
    }
}
