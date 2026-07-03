<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: opciones del desplegable de poblaciones segun el
 * filtro elegido (`get_H`, `get_r`, `get_dl`).
 *
 * El payload sigue el contrato estandar de desplegables dinamicos
 * (ver `refactor.md`), de modo que el frontend lo transforma con el
 * helper JS `fnjs_construir_desplegable`.
 */

use src\cartaspresentacion\application\CartasPresentacionPoblacionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = ['filtro' => FuncTablasSupport::inputString($_POST, 'filtro')];

/** @var CartasPresentacionPoblacionesData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionPoblacionesData::class);
ContestarJson::enviar('', $useCase->execute($input));
