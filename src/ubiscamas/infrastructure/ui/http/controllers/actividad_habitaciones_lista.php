<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;
use src\ubiscamas\application\HabitacionesCamaLista;
$Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_activ');

/** @var HabitacionesCamaLista $habitacionCamaLista */
$habitacionCamaLista = DependencyResolver::get(HabitacionesCamaLista::class);
$data = $habitacionCamaLista($Qid_activ);

if (!empty($data['success'])) {
    $id_activ = $Qid_activ;

    $data['reload_main_link_spec'] = [
        'path' => 'frontend/ubiscamas/controller/lista_habitaciones.php',
        'query' => [
            'id_activ' => $id_activ,
            'refresh' => 1,
        ],
    ];

    $data['ctx_update_solo_vip'] = HashB::sign('update_solo_vip', ['id_activ' => $id_activ]);

    $data['distribucion_open_link_spec'] = [
        'path' => 'frontend/ubiscamas/controller/lista_habitaciones_distribucion.php',
        'query' => ['id_activ' => $id_activ],
    ];

    $data['nombres_open_link_spec'] = [
        'path' => 'frontend/ubiscamas/controller/lista_habitaciones_nombres.php',
        'query' => ['id_activ' => $id_activ],
    ];
}

ContestarJson::enviar('', $data);
