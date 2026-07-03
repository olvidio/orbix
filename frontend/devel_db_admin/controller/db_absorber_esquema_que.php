<?php

namespace frontend\devel_db_admin\controller;

use frontend\devel_db_admin\helpers\DevelDbAdminPayload;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$dbProps = PostRequest::getDataFromUrl('/src/devel_db_admin/db_propiedades_data', [
    'op' => 'db_absorber_esquema_que',
]);
$a_posibles_esquemas = DevelDbAdminPayload::desplegableOpciones($dbProps['a_posibles_esquemas'] ?? []);

$oDesplMatriz = new Desplegable();
$oDesplMatriz->setNombre('esquema_matriz');
$oDesplMatriz->setBlanco(true);
$oDesplMatriz->setOpciones($a_posibles_esquemas);

$oDesplDel = new Desplegable();
$oDesplDel->setNombre('esquema_del');
$oDesplDel->setBlanco(true);
$oDesplDel->setOpciones($a_posibles_esquemas);

$oHashAbsorber = new HashFront();
$oHashAbsorber->setCamposForm('esquema_matriz!esquema_del');

$msg_falta_esquemas = _("Debe elegir el esquema matriz y el esquema a disolver.");

$a_campos = [
    'oDesplMatriz' => $oDesplMatriz,
    'oDesplDel' => $oDesplDel,
    'oHashAbsorber' => $oHashAbsorber,
    'msg_falta_esquemas' => $msg_falta_esquemas,
];

$oView = new ViewNewPhtml('frontend\devel_db_admin\controller');
$oView->renderizar('db_absorber_esquema_que.phtml', $a_campos);
