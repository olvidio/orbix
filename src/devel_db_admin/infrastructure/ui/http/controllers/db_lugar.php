<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Payload JSON: opciones del desplegable `dl` según `region` (POST), para AJAX en pantallas devel_db_admin.
 */

use src\devel_db_admin\application\DbLugarDropdown;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/** @var DelegacionRepositoryInterface $repoDl */
$repoDl = DependencyResolver::get(DelegacionRepositoryInterface::class);

$region = (string) FilterPostGet::post('region');

ContestarJson::enviar('', DbLugarDropdown::getData($region, $repoDl));
