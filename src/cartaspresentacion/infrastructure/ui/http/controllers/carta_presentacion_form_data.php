<?php
/**
 * Endpoint backend: datos del formulario de modificacion de una
 * `CartaPresentacion` (valida permisos: solo dl propia o `cr`).
 */

use src\cartaspresentacion\application\CartaPresentacionFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;


$input = [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'id_direccion' => input_int($_POST, 'id_direccion'),
];

/** @var CartaPresentacionFormData $useCase */
$useCase = DependencyResolver::get(CartaPresentacionFormData::class);
$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
