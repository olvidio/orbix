<?php

declare(strict_types=1);

/**
 * Fragmento HTML: desplegable `dl` según `region` (POST), para AJAX en db_que / db_cambiar_nombre_que.
 */

use frontend\shared\web\Desplegable;
use src\devel_db_admin\application\DbLugarDropdown;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;


/** @var DelegacionRepositoryInterface $repoDl */
$repoDl = DependencyResolver::get(DelegacionRepositoryInterface::class);

$region = (string) filter_input(INPUT_POST, 'region');
if ($region === '') {
    exit;
}

$aOpciones = DbLugarDropdown::opcionesPorRegion($region, $repoDl);

$oDesplDelegaciones = new Desplegable();
$oDesplDelegaciones->setOpciones($aOpciones);
$oDesplDelegaciones->setNombre('dl');
echo $oDesplDelegaciones->desplegable();
