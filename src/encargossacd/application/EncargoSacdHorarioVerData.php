<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\services\EncargoDominioService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Datos del formulario horario sacd (ficha tareas).
 */
final class EncargoSacdHorarioVerData
{
    /**
     * @return array<string, mixed>
     */
    public static function cargar(
        int $id_nom,
        int $id_enc,
        int $id_item_post,
        string $desc_enc_post,
    ): array {
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $oPersona = $PersonaDlRepository->findById($id_nom);
        $ap_nom = $oPersona === null ? '' : $oPersona->getPrefApellidosNombre();

        $hur = $GLOBALS['container']->get(EncargoSacdHorarioRepositoryInterface::class);
        $encRepo = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $encSacdRepo = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);

        $id_item = $id_item_post > 0 ? $id_item_post : 0;
        if ($id_item === 0) {
            $cands = $hur->getEncargoSacdHorarios(
                [
                    'id_nom' => $id_nom,
                    'id_enc' => $id_enc,
                    '_ordre' => 'id_item DESC',
                    '_limit' => '1',
                ]
            );
            if (is_array($cands) && count($cands) > 0) {
                $id_item = $cands[0]->getId_item();
            }
        }

        $emptyDia = [
            'dia_ref' => '',
            'dia_num' => '',
            'mas_menos' => '',
            'dia_inc' => '',
            'h_ini' => '',
            'h_fin' => '',
        ];

        if ($id_item > 0) {
            $h = $hur->findById($id_item);
            if ($h !== null) {
                $enc = $encRepo->findById($id_enc);
                $desc_enc = $enc !== null ? (string)($enc->getDesc_enc() ?? '') : $desc_enc_post;
                $tiene = $hur->countExcepcionesByHorarioId($id_item) > 0;

                return self::conOpciones(array_merge(
                    [
                        'ap_nom' => $ap_nom,
                        'titulo' => _('horario de') . ': ' . $desc_enc,
                        'id_item' => $id_item,
                        'desc_enc' => $desc_enc_post !== '' ? $desc_enc_post : $desc_enc,
                        'f_ini_iso' => self::fmtDate($h->getF_ini()),
                        'f_fin_iso' => self::fmtDate($h->getF_fin()),
                        'tiene_excepciones' => $tiene,
                    ],
                    self::serializeHorarioCampos($h),
                ));
            }
        }

        $cSacd = $encSacdRepo->getEncargosSacd(
            ['id_nom' => $id_nom, 'id_enc' => $id_enc, '_limit' => '1']
        );
        $f_ini = '';
        $f_fin = '';
        if (is_array($cSacd) && count($cSacd) > 0) {
            $dos = $cSacd[0];
            $f_ini = self::fmtDate($dos->getF_ini());
            $f_fin = self::fmtDate($dos->getF_fin());
        }

        $enc = $encRepo->findById($id_enc);
        $desc_default = $enc !== null ? (string)($enc->getDesc_enc() ?? '') : '';

        return self::conOpciones(array_merge(
            [
                'ap_nom' => $ap_nom,
                'titulo' => _('horario de') . ': ' . ($desc_enc_post !== '' ? $desc_enc_post : $desc_default),
                'id_item' => 0,
                'desc_enc' => $desc_enc_post !== '' ? $desc_enc_post : $desc_default,
                'f_ini_iso' => $f_ini,
                'f_fin_iso' => $f_fin,
                'tiene_excepciones' => false,
            ],
            $emptyDia,
            ['encabezado_desc' => $desc_default !== '' ? $desc_default : $desc_enc_post],
        ));
    }

    /**
     * Completa el payload con `dia` calculado y las listas de opciones de los
     * desplegables para que el frontend no tenga que importar `EncargoConstants`
     * ni `EncargoFunciones`.
     *
     * @param array<string, mixed> $base
     * @return array<string, mixed>
     */
    private static function conOpciones(array $base): array
    {
        $oDominio = new EncargoDominioService();
        $dia = $oDominio->calcular_dia(
            (string)($base['mas_menos'] ?? ''),
            (string)($base['dia_ref'] ?? ''),
            (string)($base['dia_inc'] ?? ''),
        );

        return array_merge($base, [
            'dia' => (string)$dia,
            'opciones_dia_semana' => EncargoConstants::OPCIONES_DIA_SEMANA,
            'opciones_dia_ref' => EncargoConstants::OPCIONES_DIA_REF,
            'opciones_ordinales' => EncargoConstants::OPCIONES_ORDINALES,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function serializeHorarioCampos(EncargoSacdHorario $h): array
    {
        return [
            'dia_ref' => (string)($h->getDia_ref() ?? ''),
            'dia_num' => (string)($h->getDia_num() ?? ''),
            'mas_menos' => (string)($h->getMas_menos() ?? ''),
            'dia_inc' => (string)($h->getDia_inc() ?? ''),
            'h_ini' => self::fmtTime($h->getH_ini()),
            'h_fin' => self::fmtTime($h->getH_fin()),
        ];
    }

    private static function fmtDate(mixed $d): string
    {
        if ($d === null) {
            return '';
        }
        if ($d instanceof DateTimeLocal) {
            return $d->getFromLocal();
        }
        if ($d instanceof NullDateTimeLocal) {
            return '';
        }

        return '';
    }

    private static function fmtTime(mixed $t): string
    {
        if ($t === null) {
            return '';
        }
        if ($t instanceof TimeLocal) {
            return $t->toDatabaseString();
        }
        if ($t instanceof NullTimeLocal) {
            return '';
        }

        return '';
    }
}
