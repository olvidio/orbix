<?php

use src\encargossacd\application\PropuestasAjaxDispatch;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$que = (string) (\src\shared\domain\helpers\FilterPostGet::post('que') ?? \src\shared\domain\helpers\FilterPostGet::get('que') ?? '');

$mutating = [
    'crear_tabla' => ['ctx_crear_tabla', 'propuestas_ajax_crear_tabla'],
    'cmb_sacd' => ['ctx_cmb_sacd', 'propuestas_ajax_cmb_sacd'],
    'dedicacion_update' => ['ctx_dedicacion_update', 'propuestas_ajax_dedicacion_update'],
];

if (isset($mutating[$que])) {
    [$field, $action] = $mutating[$que];
    try {
        $ctx = HashB::open(
            \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, $field),
            $action
        );
    } catch (HashBInvalidException $e) {
        ContestarJson::enviar(_("Operación no autorizada"), ['success' => false, 'mensaje' => _("Operación no autorizada")]);
        return;
    }
    if ($que === 'cmb_sacd' || $que === 'dedicacion_update') {
        $_POST['id_enc'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_enc');
        $_POST['id_item'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_item');
    }
}

/** @var PropuestasAjaxDispatch $useCase */
$useCase = DependencyResolver::get(PropuestasAjaxDispatch::class);
ContestarJson::enviar('', $useCase->execute($que));
