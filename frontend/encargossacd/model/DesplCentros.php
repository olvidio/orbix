<?php

namespace frontend\encargossacd\model;

use frontend\shared\PostRequest;
use web\Desplegable;

/**
 * Monta el {@see Desplegable} de centros; las opciones se obtienen del backend en
 * {@see \src\encargossacd\application\EncargoCtrSelectData} mediante HTTP interno (PostRequest).
 */
class DesplCentros
{
    private ?int $id_zona = null;

    public function setIdZona(?int $id_zona): void
    {
        $this->id_zona = $id_zona;
    }

    /**
     * @return array<string, mixed> respuesta JSON decodificada (payload `data`) de `/src/encargossacd/ctr_get_select_data`
     */
    private static function opcionesDataDesdeBackend(int $filtro_ctr, int $id_zona, int $id_ubi): array
    {
        /** @var array<string, mixed> $data */
        $data = PostRequest::getDataFromUrl('/src/encargossacd/ctr_get_select_data', [
            'id_ubi' => $id_ubi,
            'filtro_ctr' => $filtro_ctr,
            'id_zona' => $id_zona,
        ]);

        return $data;
    }

    /**
     * Construye el desplegable con las mismas convenciones que la respuesta del endpoint (nombre, opción en blanco si aplica, selección).
     */
    public function getDesplPorFiltro(int $filtro_ctr, int $id_ubi = 0): Desplegable
    {
        $id_zona = (int)($this->id_zona ?? 0);
        $data = self::opcionesDataDesdeBackend($filtro_ctr, $id_zona, $id_ubi);

        $oDesplCtr = new Desplegable();
        $oDesplCtr->setNombre($data['id'] ?? 'lst_ctrs');
        $oDesplCtr->setOpciones($data['opciones'] ?? []);
        if (!empty($data['blanco'])) {
            $oDesplCtr->setBlanco(true);
        }
        if (array_key_exists('val_blanco', $data)) {
            $oDesplCtr->setValBlanco((string)$data['val_blanco']);
        }
        $oDesplCtr->setOpcion_sel($data['selected'] ?? '');
        $oDesplCtr->setAction($data['action'] ?? '');

        return $oDesplCtr;
    }
}
