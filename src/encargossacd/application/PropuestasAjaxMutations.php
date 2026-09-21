<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\encargossacd\domain\value_objects\EncargoModoId;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\helpers\FilterPostGet;

/**
 * Mutaciones AJAX de la pantalla de propuestas (`que` distinto de get_lista / crear_tabla).
 */
final class PropuestasAjaxMutations
{
    public function __construct(
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PropuestaEncargoSacdHorarioRepositoryInterface $propuestaHorarioRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private EncargoRepositoryInterface $encargoRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $que): array
    {
        return match ($que) {
            'lista_sacd' => $this->listaSacd(),
            'dedicacion_update' => $this->dedicacionUpdate(),
            'dedicacion' => $this->dedicacion(),
            'info' => $this->info(),
            'cmb_sacd' => $this->cmbSacd(),
            default => ['success' => false, 'mensaje' => _('Operación no reconocida')],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function listaSacd(): array
    {
        $id_sacd = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_sacd') ?? 0);
        $id_item = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_item') ?? 0);
        $id_enc = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? 0);
        $tipo = (string) (\src\shared\domain\helpers\FilterPostGet::post('tipo') ?? '');

        return [
            'success' => true,
            'popup' => 'lista_sacd',
            'opciones' => $this->personaSacdRepository->getArraySacd("AND id_tabla ~ '^(a|n|sss)$'"),
            'id_sacd' => $id_sacd,
            'id_item' => $id_item,
            'id_enc' => $id_enc,
            'tipo' => $tipo,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function dedicacionUpdate(): array
    {
        $id_sacd = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_sacd') ?? 0);
        $id_item = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_item') ?? 0);
        $id_enc = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? 0);
        $dedic_m = (int) (\src\shared\domain\helpers\FilterPostGet::post('dedic_m') ?? 0);
        $dedic_t = (int) (\src\shared\domain\helpers\FilterPostGet::post('dedic_t') ?? 0);
        $dedic_v = (int) (\src\shared\domain\helpers\FilterPostGet::post('dedic_v') ?? 0);

        if ($id_item === $id_enc) {
            $cEncargosSacd = $this->propuestaEncargoSacdRepository->getPropuestasEncargoSacd(
                ['id_nom_new' => $id_sacd, 'id_enc' => $id_enc, 'f_fin' => 'x'],
                ['f_fin' => 'IS NULL'],
            );
            if (count($cEncargosSacd) === 1) {
                $id_item = $cEncargosSacd[0]->getId_item();
            } else {
                return ['success' => false, 'mensaje' => _('No se puede guardar. Vuelva a cargar la vista')];
            }
        }

        $f_ini = new DateTimeLocal();
        $error_txt = '';

        foreach (['m' => $dedic_m, 't' => $dedic_t, 'v' => $dedic_v] as $modulo => $dedicacion) {
            $cHorarios = $this->propuestaHorarioRepository->getEncargoSacdHorarios([
                'id_enc' => $id_enc,
                'id_nom' => $id_sacd,
                'id_item_tarea_sacd' => $id_item,
                'dia_ref' => $modulo,
            ]);

            if ($cHorarios !== []) {
                $oHorario = $cHorarios[0];
                $oHorario->setF_fin(null);
            } else {
                $oHorario = new EncargoSacdHorario();
                $oHorario->setId_enc($id_enc);
                $oHorario->setId_nom($id_sacd);
                $oHorario->setF_ini($f_ini);
                $oHorario->setF_fin(null);
                $oHorario->setDia_ref($modulo);
                $oHorario->setId_item_tarea_sacd($id_item);
            }
            $oHorario->setDia_inc($dedicacion);
            if ($this->propuestaHorarioRepository->Guardar($oHorario) === false) {
                $error_txt = $this->propuestaHorarioRepository->getErrorTxt();
            }
        }

        if ($error_txt !== '') {
            return ['success' => false, 'mensaje' => $error_txt];
        }

        return ['success' => true];
    }

    /**
     * @return array<string, mixed>
     */
    private function dedicacion(): array
    {
        $id_sacd = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_sacd') ?? 0);
        $id_item = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_item') ?? 0);
        $id_enc = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? 0);

        $apellidos_nombre = $this->apellidosNombre($id_sacd);
        $cHorarios = $this->propuestaHorarioRepository->getEncargoSacdHorarios(
            ['id_nom' => $id_sacd, 'id_item_tarea_sacd' => $id_item, 'f_fin' => 'x'],
            ['f_fin' => 'IS NULL'],
        );

        $dedic_m = $dedic_t = $dedic_v = '';
        foreach ($cHorarios as $oHorario) {
            match ($oHorario->getDia_ref()) {
                'm' => $dedic_m = (string) ($oHorario->getDia_inc() ?? ''),
                't' => $dedic_t = (string) ($oHorario->getDia_inc() ?? ''),
                'v' => $dedic_v = (string) ($oHorario->getDia_inc() ?? ''),
                default => null,
            };
        }

        $encargo = $this->encargoRepository->findById($id_enc);
        $desc_enc = (string) ($encargo?->getDesc_enc() ?? '');

        return [
            'success' => true,
            'popup' => 'dedicacion',
            'apellidos_nombre' => $apellidos_nombre,
            'desc_enc' => $desc_enc,
            'id_sacd' => $id_sacd,
            'id_item' => $id_item,
            'id_enc' => $id_enc,
            'dedic_m' => $dedic_m,
            'dedic_t' => $dedic_t,
            'dedic_v' => $dedic_v,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function info(): array
    {
        $id_sacd = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_sacd') ?? 0);
        $apellidos_nombre = $this->apellidosNombre($id_sacd);
        $cEncargosSacd = $this->propuestaEncargoSacdRepository->getPropuestasEncargoSacd(['id_nom_new' => $id_sacd]);

        $encargos = [];
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $encargo = $this->encargoRepository->findById($oEncargoSacd->getId_enc());
            $encargos[] = (string) ($encargo?->getDesc_enc() ?? '');
        }

        return [
            'success' => true,
            'popup' => 'info',
            'apellidos_nombre' => $apellidos_nombre,
            'encargos' => $encargos,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cmbSacd(): array
    {
        $tipo = (string) (\src\shared\domain\helpers\FilterPostGet::post('tipo') ?? '');
        $id_item = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_item') ?? 0);
        $id_enc = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? 0);
        $id_sacd = (int) (\src\shared\domain\helpers\FilterPostGet::post('id_sacd') ?? 0);

        $row = null;
        $id_sacd_old = 0;
        $id_sacd_prop = 0;
        $error_txt = '';
        $id_item_new = $id_item;

        if ($id_item === $id_enc) {
            $modo = match ($tipo) {
                'titular' => 2,
                'suplente' => 4,
                'colaborador' => 5,
                default => 0,
            };
            $oPropuesta = new PropuestaEncargoSacd();
            $oPropuesta->setId_enc($id_enc);
            $oPropuesta->setId_nom(0);
            $oPropuesta->setModoVo(new EncargoModoId($modo));
            $oPropuesta->setF_ini(new DateTimeLocal());
            $oPropuesta->setId_nom_new($id_sacd > 0 ? $id_sacd : null);
            if ($this->propuestaEncargoSacdRepository->Guardar($oPropuesta) === false) {
                $error_txt .= $this->propuestaEncargoSacdRepository->getErrorTxt();
            }
            $id_item_new = $oPropuesta->getId_item();
        } else {
            $oPropuesta = $this->propuestaEncargoSacdRepository->findById($id_item);
            if ($oPropuesta === null) {
                return ['success' => false, 'mensaje' => _('Registro no encontrado')];
            }
            $id_sacd_old = $oPropuesta->getId_nom();
            $id_sacd_prop = (int) ($oPropuesta->getId_nom_new() ?? 0);
            if ($id_sacd_old === 0 && $id_sacd === 0) {
                $nombre = _('nuevo');
                if ($tipo === 'colaborador') {
                    $row = ['tipo' => 'borrar'];
                    $this->propuestaEncargoSacdRepository->Eliminar($oPropuesta);
                } else {
                    $nom_tipo = $tipo === 'titular' ? _('titular') : _('suplente');
                    $row = [
                        'tipo' => 'celdas',
                        'encargo_tipo' => $tipo,
                        'id_item' => $id_item,
                        'id_enc' => $id_enc,
                        'id_sacd' => $id_sacd,
                        'nombre' => $nombre,
                        'nom_tipo' => $nom_tipo,
                    ];
                    $oPropuesta->setId_nom_new(null);
                    $this->propuestaEncargoSacdRepository->Guardar($oPropuesta);
                }
            } else {
                $oPropuesta->setId_nom_new($id_sacd > 0 ? $id_sacd : null);
                if ($this->propuestaEncargoSacdRepository->Guardar($oPropuesta) === false) {
                    $error_txt .= $this->propuestaEncargoSacdRepository->getErrorTxt();
                }
                $id_item_new = $oPropuesta->getId_item();
            }
        }

        if ($id_sacd_old !== 0 || $id_sacd_prop !== 0) {
            $id_sacd_ref = $id_sacd_prop === 0 ? $id_sacd_old : $id_sacd_prop;
            $this->propuestaHorarioRepository->cambiarSacd($id_enc, $id_sacd_ref, $id_sacd);
        }

        $nombre = $this->apellidosNombre($id_sacd);
        $nombre = $nombre === '' ? _('nuevo') : $nombre;

        if ($row === null && $id_item === $id_enc) {
            $row = [
                'tipo' => 'fila_colaborador',
                'id_item' => $id_item_new,
                'id_enc' => $id_enc,
                'id_sacd' => $id_sacd,
                'nombre' => $nombre,
            ];
        }

        if ($error_txt !== '') {
            return ['success' => false, 'mensaje' => $error_txt];
        }

        return [
            'success' => true,
            'nombre' => $nombre,
            'id_sacd' => $id_sacd,
            'row' => $row,
        ];
    }

    private function apellidosNombre(int $id_sacd): string
    {
        if ($id_sacd <= 0) {
            return '';
        }
        $persona = $this->personaSacdRepository->findById($id_sacd);

        return $persona?->getApellidosNombre() ?? '';
    }

}
