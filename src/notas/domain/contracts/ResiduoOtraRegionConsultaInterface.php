<?php

declare(strict_types=1);

namespace src\notas\domain\contracts;

/**
 * Lectura del residuo en `{esquema}.e_notas_otra_region_stgr` de esta base.
 */
interface ResiduoOtraRegionConsultaInterface
{
    /**
     * @return list<array{
     *   esquema: string,
     *   id_nom: int,
     *   id_asignatura: int,
     *   id_situacion: int,
     *   tipo_acta: int,
     *   acta: string,
     *   f_acta: string,
     *   detalle: string,
     *   nombre: string,
     *   hay_acta_dl: bool,
     *   certificados: list<string>,
     *   certificado_en_modulo: bool
     * }>
     */
    public function listar(): array;

    /**
     * Esquemas que tienen la tabla y el usuario de la sesión no puede leer.
     *
     * @return list<string>
     */
    public function esquemasSinPermiso(): array;
}
