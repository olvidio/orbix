<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;

class DesplegableEncargosData
{

    public function __construct(
        private readonly EncargoTipoRepositoryInterface $encargoTipoRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }
    /**
     * Payload JSON para el desplegable dinamico de encargos de una zona.
     *
     * Sigue el contrato de desplegables de `refactor.md`:
     *   - `id`        : id del `<select>` que montara `fnjs_construir_desplegable`.
     *   - `opciones`  : map id_enc => desc_enc de los encargos con
     *                   `id_tipo_enc >= 8100` de la zona.
     *   - `selected`  : id_enc preseleccionado (`''` si no aplica).
     *   - `blanco`    : true si se quiere opcion en blanco.
     *   - `val_blanco`: valor de la opcion en blanco.
     *   - `action`    : handler `onchange` opcional (vacio por defecto).
     *
     * @param int      $id_zona       Zona de la que sacar los encargos.
     * @param int|null $id_enc_sel    Encargo preseleccionado (opcional). Si
     *                                no aparece entre las opciones de la
     *                                zona, se anade igualmente para que el
     *                                valor se pueda mantener en la UI.
     * @return array<string, mixed>
     */
    public function getData(int $id_zona, ?int $id_enc_sel = null): array
    {
        
        $cEncargoTipos = $this->encargoTipoRepository->getEncargoTipos(
            ['id_tipo_enc' => '^8...'],
            ['id_tipo_enc' => '~'],
        );
        $a_tipo_enc = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
                $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
            }
        }

        $opciones = [];

        // Si hay encargo preseleccionado lo anadimos siempre (aunque no sea
        // de la zona), para no perder el valor al recargar el desplegable.
        if (!empty($id_enc_sel)) {
            $oEncSel = $this->encargoRepository->findById($id_enc_sel);
            if ($oEncSel !== null) {
                $opciones[(string)$id_enc_sel] = $oEncSel->getDesc_enc();
            }
        }

        if (!empty($a_tipo_enc)) {
            $cond_tipo_enc = '{' . implode(', ', $a_tipo_enc) . '}';
            $cEncargos = $this->encargoRepository->getEncargos(
                [
                    'id_tipo_enc' => $cond_tipo_enc,
                    'id_zona' => $id_zona,
                ],
                ['id_tipo_enc' => 'ANY'],
            );
            foreach ($cEncargos as $oEncargo) {
                $opciones[(string)$oEncargo->getId_enc()] = $oEncargo->getDesc_enc();
            }
        }

        return [
            'id' => 'id_enc',
            'opciones' => $opciones,
            'selected' => $id_enc_sel !== null ? (string)$id_enc_sel : '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => 'fnjs_prepara_select_centro()',
        ];
    }
}
