<?php

declare(strict_types=1);

return [
    'aliases' => [
        'profesores_docencia' => 'profesores',
    ],
    'modules' => [
        'profesores' => [
            'tables' => [
                ['table' => 'd_profesor_latin', 'entity' => \src\profesores\domain\entity\ProfesorLatin::class],
                ['table' => 'd_congresos', 'entity' => \src\profesores\domain\entity\ProfesorCongreso::class],
                ['table' => 'd_profesor_director', 'entity' => \src\profesores\domain\entity\ProfesorDirector::class],
                ['table' => 'd_publicaciones', 'entity' => \src\profesores\domain\entity\ProfesorPublicacion::class],
                ['table' => 'd_profesor_stgr', 'entity' => \src\profesores\domain\entity\ProfesorStgr::class],
                ['table' => 'd_titulo_est', 'entity' => \src\profesores\domain\entity\ProfesorTituloEst::class],
                ['table' => 'd_profesor_ampliacion', 'entity' => \src\profesores\domain\entity\ProfesorAmpliacion::class],
                ['table' => 'd_docencia_stgr', 'entity' => \src\profesores\domain\entity\ProfesorDocenciaStgr::class],
                ['table' => 'd_profesor_juramento', 'entity' => \src\profesores\domain\entity\ProfesorJuramento::class],
            ],
        ],
    ],
];
