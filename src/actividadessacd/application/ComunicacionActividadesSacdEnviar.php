<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

/**
 * Mutacion: encola los mails de comunicacion de actividades a los sacd.
 */
final class ComunicacionActividadesSacdEnviar
{
    public function __construct(
        private ComunicacionActividadesSacdData $comunicacionActividadesSacdData,
        private ComunicarActividadesSacdService $comunicarActividadesSacdService,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $ctx = $this->comunicacionActividadesSacdData->resolverContexto($input);
        if ($ctx['inicioIso'] === '' || $ctx['finIso'] === '') {
            return _("falta determinar un periodo");
        }

        $mi_dele = (string)ConfigGlobal::mi_delef();
        $que = $ctx['que'];

        switch ($que) {
            case 'nagd':
                $cPersonas = $this->personaSacdRepository->getPersonas(
                    [
                        'id_tabla' => "'n','a'",
                        'situacion' => 'A',
                        'sacd' => 't',
                        'dl' => $mi_dele,
                        '_ordre' => 'apellido1,apellido2,nom',
                    ],
                    ['id_tabla' => 'IN']
                );
                break;
            case 'sssc':
                $cPersonas = $this->personaSacdRepository->getPersonas([
                    'id_tabla' => 'sssc',
                    'situacion' => 'A',
                    'sacd' => 't',
                    'dl' => $mi_dele,
                    '_ordre' => 'apellido1,apellido2,nom',
                ]);
                break;
            case 'un_sacd':
                $oPersona = $this->personaSacdRepository->findById($ctx['id_nom']);
                $cPersonas = $oPersona === null ? [] : [$oPersona];
                break;
            default:
                $cPersonas = [];
        }

        $service = clone $this->comunicarActividadesSacdService;
        $service->setInicioIso($ctx['inicioIso']);
        $service->setFinIso($ctx['finIso']);
        $service->setPropuesta($ctx['propuesta']);
        $service->setPersonas($cPersonas);
        $sacds = $service->getArrayComunicacion();
        $error = $service->enviarMails($sacds);
        if ($error !== '') {
            return $error;
        }

        return '';
    }
}
