<?php

namespace src\cartaspresentacion\application;

use src\shared\config\ConfigGlobal;

/**
 * Datos para la shell `cartas_presentacion.php`: delegación y paths relativos.
 * URLs absolutas y fragment Hash: {@see \frontend\cartaspresentacion\helpers\CartasPresentacionShellRender}.
 */
final class CartasPresentacionShellData
{
    /**
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        return [
            'mi_dele' => ConfigGlobal::mi_delef(),
            'paths' => [
                'ctr' => 'frontend/ubis/controller/home_ubis.php',
                'lista' => 'frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php',
                'form' => 'frontend/cartaspresentacion/controller/cartas_presentacion_form.php',
                'poblaciones' => 'src/cartaspresentacion/poblaciones_data',
                'update' => 'src/cartaspresentacion/carta_presentacion_update',
                'eliminar' => 'src/cartaspresentacion/carta_presentacion_eliminar',
            ],
            'hash_ctr' => [
                'campos_form' => 'bloque!pau!id_ubi',
            ],
            'hash_lista' => [
                'campos_form' => 'tipo_lista',
                'campos_no' => 'scroll_id!sel!poblacion_sel',
            ],
            'hash_form' => [
                'campos_form' => 'id_direccion!id_ubi',
            ],
            'hash_poblaciones' => [
                'campos_form' => 'filtro',
            ],
            'hash_eliminar' => [
                'campos_form' => 'id_ubi!id_direccion',
            ],
        ];
    }
}
