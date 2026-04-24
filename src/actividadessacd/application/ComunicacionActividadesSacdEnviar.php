<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

/**
 * Mutacion: encola los mails de comunicacion de actividades a los sacd.
 *
 * Reutiliza `ComunicacionActividadesSacdData::resolverContexto()` para
 * interpretar los mismos campos de entrada que el endpoint de lectura
 * (`que`, `id_nom`, `propuesta`, `periodo`, ...). Luego reconstruye el
 * listado por sacd y llama a `ComunicarActividadesSacdService::enviarMails`.
 *
 * Sucesor de la rama `Qmail === 'si'` del dispatcher legacy.
 */
final class ComunicacionActividadesSacdEnviar
{
    public static function execute(array $input): string
    {
        $ctx = ComunicacionActividadesSacdData::resolverContexto($input);
        if ($ctx['inicioIso'] === '' || $ctx['finIso'] === '') {
            return _("falta determinar un periodo");
        }

        $mi_dele = (string)ConfigGlobal::mi_delef();

        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        switch ($ctx['que']) {
            case 'nagd':
                $cPersonas = $PersonaSacdRepository->getPersonas(
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
                $cPersonas = $PersonaSacdRepository->getPersonas([
                    'id_tabla' => 'sssc',
                    'situacion' => 'A',
                    'sacd' => 't',
                    'dl' => $mi_dele,
                    '_ordre' => 'apellido1,apellido2,nom',
                ]);
                break;
            case 'un_sacd':
                $oPersona = $PersonaSacdRepository->findById($ctx['id_nom']);
                $cPersonas = $oPersona === null ? [] : [$oPersona];
                break;
            default:
                $cPersonas = [];
        }

        $service = new ComunicarActividadesSacdService();
        $service->setInicioIso($ctx['inicioIso']);
        $service->setFinIso($ctx['finIso']);
        $service->setPropuesta($ctx['propuesta']);
        $service->setPersonas($cPersonas);
        $sacds = $service->getArrayComunicacion();
        $error = $service->enviarMails($sacds);
        if ($error !== '') {
            return $error;
        }

        // "Sacd de paso": se incluyen tambien cuando el flujo no es un_sacd,
        // igual que hacia el legacy al ir a `Qmail=si` con `Qque !== un_sacd`.
        // Nota: el legacy solo invocaba `envairMails($array_actividades)`
        // (el array principal), no los de paso. Se mantiene ese contrato.
        return '';
    }
}
