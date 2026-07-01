<?php

namespace src\personas\application;

use function src\shared\domain\helpers\input_string;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\application\support\PersonaSeleccionInput;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\RegionStgrAviso;

/**
 * Caso de uso detras del endpoint `/src/personas/home_persona_data`.
 *
 * Devuelve los datos para la pantalla de cabecera de persona
 * (`home_persona.phtml`): datos basicos, telecos, centro, nivel_stgr traducido
 * y la normalizacion de `Qobj_pau` cuando la entidad es un `PersonaDl` generico.
 */
final class HomePersonaData
{
    public function __construct(
        private PersonaRepositoryResolver $personaRepositoryResolver,
        private PersonaPubRepositoryInterface $personaPubRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private TelecoPersonaService $telecoPersonaService,
    ) {
    }

    /**
     * @param array<string,mixed> $input habitualmente `$_POST`
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $a_sel = self::normalizeSel($input['sel'] ?? null);
        if (!empty($a_sel)) {
            $id_nom = (int)strtok((string)$a_sel[0], '#');
            $id_tabla = (string)strtok('#');
        } else {
            $seleccion = PersonaSeleccionInput::idNomYTabla($input);
            $id_nom = $seleccion['id_nom'];
            $id_tabla = $seleccion['id_tabla'];
        }

        $Qobj_pau = input_string($input, 'obj_pau');

        $problemasRegionStgr = [];
        try {
            if ($Qobj_pau === 'PersonaEx') {
                $marcaAviso = false;
                $oPersona = $this->personaPubRepository
                    ->findByIdParaListado($id_nom, $problemasRegionStgr, $marcaAviso);
            } else {
                $repoPersona = $this->personaRepositoryResolver->repositorio($Qobj_pau);
                $oPersona = $repoPersona->findById($id_nom);
            }
        } catch (\InvalidArgumentException) {
            return ['error' => _("No existe la clase de la persona")];
        }
        if ($oPersona === null) {
            if ($id_nom <= 0) {
                return ['aviso' => RegionStgrAviso::mensajePersonaNoValida()];
            }

            return ['error' => _("No se encuentra la persona")];
        }

        $titulo = (string)$oPersona->getNombreApellidos();
        $dl = (string)($oPersona->getDl() ?? '');
        $f_nacimiento = (string)($oPersona->getF_nacimiento()?->getFromLocal() ?? '');
        $situacion = $oPersona->getSituacion();
        $f_situacion = (string)($oPersona->getF_situacion()?->getFromLocal() ?? '');
        $profesion = (string)($oPersona->getProfesion() ?? '');
        $id_nivel_stgr = $oPersona->getNivel_stgr();
        $a_niveles_stgr = NivelStgrId::getArrayNivelStgr();
        $stgr = (string)($a_niveles_stgr[$id_nivel_stgr ?? ''] ?? '');
        $observ = (string)($oPersona->getObserv() ?? '');

        // PersonaDl generico => subclase real segun id_tabla.
        if ($oPersona instanceof PersonaDl) {
            $map = [
                'n' => 'PersonaN',
                'a' => 'PersonaAgd',
                's' => 'PersonaS',
                'sssc' => 'PersonaSSSC',
            ];
            $Qobj_pau = $map[$oPersona->getId_tabla()] ?? $Qobj_pau;
        }

        $ctr = '';
        if ($Qobj_pau !== 'PersonaEx' && $Qobj_pau !== 'PersonaIn') {
            $id_ctr = $oPersona->getId_ctr();
            if (!empty($id_ctr)) {
                $oCentroDl = $this->centroDlRepository->findById($id_ctr);
                $ctr = (string)($oCentroDl?->getNombre_ubi() ?? '');
            }
        }

        $telfs_fijo = $this->telecoPersonaService->getTelecosPorTipo($id_nom, 'telf', " / ", "*");
        $telfs_movil = $this->telecoPersonaService->getTelecosPorTipo($id_nom, 'móvil', " / ", "*");
        if ($telfs_fijo !== '' && $telfs_movil !== '') {
            $telfs = $telfs_fijo . " / " . $telfs_movil;
        } else {
            $telfs = $telfs_fijo . $telfs_movil;
        }
        $mails = (string)$this->telecoPersonaService->getTelecosPorTipo($id_nom, 'e-mail', " / ", "*");

        $result = [
            'Qobj_pau' => $Qobj_pau,
            'id_nom' => $id_nom,
            'id_tabla' => $id_tabla,
            'titulo' => $titulo,
            'dl' => $dl,
            'f_nacimiento' => $f_nacimiento,
            'situacion' => $situacion,
            'f_situacion' => $f_situacion,
            'profesion' => $profesion,
            'stgr' => $stgr,
            'observ' => $observ,
            'ctr' => $ctr,
            'telfs' => (string)$telfs,
            'mails' => $mails,
        ];
        if ($problemasRegionStgr !== []) {
            $result['aviso'] = RegionStgrAviso::formatear($problemasRegionStgr);
        }

        return $result;
    }

    /**
     * @param mixed $sel
     * @return array<int,string>
     */
    private static function normalizeSel(mixed $sel): array
    {
        if (is_array($sel)) {
            return array_values(array_filter(array_map(static fn(mixed $v): string => is_scalar($v) ? (string)$v : '', $sel), static fn(string $v): bool => $v !== ''));
        }
        if (is_string($sel) && $sel !== '') {
            return [$sel];
        }
        return [];
    }
}
