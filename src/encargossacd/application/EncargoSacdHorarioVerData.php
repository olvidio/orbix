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

    public function __construct(
        private EncargoDominioService $dominioService,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function cargar(
        int $id_nom,
        int $id_enc,
        int $id_item_post,
        string $desc_enc_post,
    ): array {
        $oPersona = $this->personaDlRepository->findById($id_nom);
        $ap_nom = $oPersona === null ? '' : $oPersona->getPrefApellidosNombre();

        $id_item = $id_item_post > 0 ? $id_item_post : 0;
        if ($id_item === 0) {
            $cands = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios(
                [
                    'id_nom' => $id_nom,
                    'id_enc' => $id_enc,
                    '_ordre' => 'id_item DESC',
                    '_limit' => '1',
                ]
            );
            if ($cands !== []) {
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
            $h = $this->encargoSacdHorarioRepository->findById($id_item);
            if ($h !== null) {
                $enc = $this->encargoRepository->findById($id_enc);
                $desc_enc = $enc !== null ? (string)($enc->getDesc_enc() ?? '') : $desc_enc_post;
                $tiene = $this->encargoSacdHorarioRepository->countExcepcionesByHorarioId($id_item) > 0;

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

        $cSacd = $this->encargoSacdRepository->getEncargosSacd(
            ['id_nom' => $id_nom, 'id_enc' => $id_enc, '_limit' => '1']
        );
        $f_ini = '';
        $f_fin = '';
        if ($cSacd !== []) {
            $dos = $cSacd[0];
            $f_ini = self::fmtDate($dos->getF_ini());
            $f_fin = self::fmtDate($dos->getF_fin());
        }

        $enc = $this->encargoRepository->findById($id_enc);
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
    private function conOpciones(array $base): array
    {
        $oDominio = $this->dominioService;
        $dia = $oDominio->calcular_dia(
            is_scalar($base['mas_menos'] ?? null) ? (string) $base['mas_menos'] : '',
            is_scalar($base['dia_ref'] ?? null) ? (string) $base['dia_ref'] : '',
            is_scalar($base['dia_inc'] ?? null) ? (string) $base['dia_inc'] : '',
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
    private function serializeHorarioCampos(EncargoSacdHorario $h): array
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

    private function fmtDate(mixed $d): string
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

    private function fmtTime(mixed $t): string
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
