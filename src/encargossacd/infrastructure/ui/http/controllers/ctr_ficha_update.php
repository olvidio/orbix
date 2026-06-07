<?php

use src\encargossacd\application\CtrFichaUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CtrFichaUpdate $useCase */
$useCase = DependencyResolver::get(CtrFichaUpdate::class);


$resultado = $useCase->execute($_POST);

// La mutacion devuelve ['error' => '']. Mapeamos al contrato JSON estandar
// ({success, mensaje, data}) del refactor; el proxy legacy en
// `frontend/encargossacd/controller/ctr_ficha_update.php` re-emite `mensaje`
// como texto plano para mantener el contrato `alert(rta_txt)` del JS.
ContestarJson::enviar((string)$resultado['error'], '');
