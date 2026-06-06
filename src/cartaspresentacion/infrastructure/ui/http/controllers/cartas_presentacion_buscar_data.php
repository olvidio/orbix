<?php
/**
 * Endpoint backend: opciones del formulario de busqueda de cartas de
 * presentacion (region, pais, delegacion).
 */

use src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CartasPresentacionBuscarOpcionesData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionBuscarOpcionesData::class);
ContestarJson::enviar('', $useCase->execute());
