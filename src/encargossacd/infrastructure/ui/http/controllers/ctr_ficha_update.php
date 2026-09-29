<?php

use src\encargossacd\application\CtrFichaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'ctr_ficha_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$e = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'e');
$input = $_POST;
$input['id_ubi_' . $e] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');

/** @var CtrFichaUpdate $useCase */
$useCase = DependencyResolver::get(CtrFichaUpdate::class);

$resultado = $useCase->execute($input);

// La mutacion devuelve ['error' => '']. Mapeamos al contrato JSON estandar
// ({success, mensaje, data}) del refactor; el proxy legacy en
// `frontend/encargossacd/controller/ctr_ficha_update.php` re-emite `mensaje`
// como texto plano para mantener el contrato `alert(rta_txt)` del JS.
ContestarJson::enviar((string)$resultado['error'], '');
