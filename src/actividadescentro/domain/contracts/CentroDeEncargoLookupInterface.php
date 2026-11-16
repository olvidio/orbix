<?php

declare(strict_types=1);

namespace src\actividadescentro\domain\contracts;

use src\shared\domain\contracts\DatosLookupRepositoryInterface;

/**
 * Busca por su id el centro de un `CentroEncargado`, sea de sv o de sf, para mostrar su nombre en la
 * lista del dossier 3010 ({@see \src\actividadescentro\domain\entity\CentroEncargado::getDatosCampos()}).
 */
interface CentroDeEncargoLookupInterface extends DatosLookupRepositoryInterface
{
}
