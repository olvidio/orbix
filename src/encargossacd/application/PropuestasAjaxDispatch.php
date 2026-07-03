<?php

namespace src\encargossacd\application;

use src\shared\domain\helpers\FilterPostGet;

/**
 * Despacho único de `propuestas_ajax` (`que` → caso de uso).
 */
final class PropuestasAjaxDispatch
{
    public function __construct(
        private PropuestasAjaxGetLista $getLista,
        private PropuestasCrearTabla $crearTabla,
        private PropuestasAjaxMutations $mutations,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $que): array
    {
        return match ($que) {
            'get_lista' => $this->getLista->execute((int) (FilterPostGet::post('filtro_ctr') ?? FilterPostGet::get('filtro_ctr') ?? 0)),
            'crear_tabla' => $this->crearTabla->execute(),
            'lista_sacd', 'dedicacion_update', 'dedicacion', 'info', 'cmb_sacd' => $this->mutations->execute($que),
            default => ['success' => false, 'mensaje' => _('Operación no soportada')],
        };
    }
}
