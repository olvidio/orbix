<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\shared\helpers\PayloadCoercion;

final class CertificadosMpdfRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array{
     *     id_nom: int,
     *     nom: string,
     *     certificado: string,
     *     lugar_fecha: string,
     *     vstgr: string,
     *     dir_stgr: string,
     *     replace: array<string, string>,
     *     txt_superavit: string,
     *     curso_filosofia: string,
     *     any_I: string,
     *     ECTS: string,
     *     iudicium: string,
     *     curso_teologia: string,
     *     pie_ects: string,
     *     any_II: string,
     *     any_III: string,
     *     any_IV: string,
     *     titulo_1: string,
     *     titulo_2: string,
     *     titulo_3: string,
     *     infra: string,
     *     sello: string,
     *     fidem: string,
     *     reg_num: string,
     *     cAsignaturas: list<array{id_asignatura: int, id_nivel: int, nombre_asignatura: string, creditos: float}>,
     *     aAprobadas: array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}>,
     * }
     */
    public static function fromPayload(array $payload): array
    {
        return [
            'id_nom' => PayloadCoercion::int($payload['id_nom'] ?? 0),
            'nom' => PayloadCoercion::string($payload['nom'] ?? ''),
            'certificado' => PayloadCoercion::string($payload['certificado'] ?? ''),
            'lugar_fecha' => PayloadCoercion::string($payload['lugar_fecha'] ?? ''),
            'vstgr' => PayloadCoercion::string($payload['vstgr'] ?? ''),
            'dir_stgr' => PayloadCoercion::string($payload['dir_stgr'] ?? ''),
            'replace' => CertificadosPayload::latinReplaceMap($payload['replace'] ?? []),
            'txt_superavit' => PayloadCoercion::string($payload['txt_superavit'] ?? ''),
            'curso_filosofia' => PayloadCoercion::string($payload['curso_filosofia'] ?? ''),
            'any_I' => PayloadCoercion::string($payload['any_I'] ?? ''),
            'ECTS' => PayloadCoercion::string($payload['ECTS'] ?? ''),
            'iudicium' => PayloadCoercion::string($payload['iudicium'] ?? ''),
            'curso_teologia' => PayloadCoercion::string($payload['curso_teologia'] ?? ''),
            'pie_ects' => PayloadCoercion::string($payload['pie_ects'] ?? ''),
            'any_II' => PayloadCoercion::string($payload['any_II'] ?? ''),
            'any_III' => PayloadCoercion::string($payload['any_III'] ?? ''),
            'any_IV' => PayloadCoercion::string($payload['any_IV'] ?? ''),
            'titulo_1' => PayloadCoercion::string($payload['titulo_1'] ?? ''),
            'titulo_2' => PayloadCoercion::string($payload['titulo_2'] ?? ''),
            'titulo_3' => PayloadCoercion::string($payload['titulo_3'] ?? ''),
            'infra' => PayloadCoercion::string($payload['infra'] ?? ''),
            'sello' => PayloadCoercion::string($payload['sello'] ?? ''),
            'fidem' => PayloadCoercion::string($payload['fidem'] ?? ''),
            'reg_num' => PayloadCoercion::string($payload['reg_num'] ?? ''),
            'cAsignaturas' => CertificadosPayload::asignaturasFromJson($payload['cAsignaturas'] ?? ''),
            'aAprobadas' => CertificadosPayload::aprobadasFromPayload($payload['aAprobadas'] ?? []),
        ];
    }

    /**
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
     */
    public static function emptyAprobadaRow(): array
    {
        return CertificadosPayload::aprobadaRow([]);
    }

    /**
     * @param array<int, array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}> $aAprobadas
     * @param array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string} $rowEmpty
     * @return array{id_nivel_asig: int, id_nivel: int, id_asignatura: int, nombre_asignatura: string, creditos: string, nota_txt: string}
     */
    public static function currentAprobadaRow(array $aAprobadas, array $rowEmpty): array
    {
        if ($aAprobadas === []) {
            return $rowEmpty;
        }

        return CertificadosPayload::aprobadaRow(current($aAprobadas));
    }

    /**
     * @param array{
     *     curso_filosofia: string,
     *     curso_teologia: string,
     *     any_I: string,
     *     any_II: string,
     *     any_III: string,
     *     any_IV: string,
     *     ECTS: string,
     *     iudicium: string,
     *     pie_ects: string,
     * } $labels
     */
    public static function titulo(int $id_asignatura, array $labels): void
    {
        switch ($id_asignatura) {
            case 1101:
                ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $labels['curso_filosofia'] ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_I'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
                <?php
                break;
            case 1201:
                ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_II'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
                <?php
                break;
            case 2101:
                ?>
    <tr>
        <td class="space_doble"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="7" class="curso"><?= $labels['curso_teologia'] ?></td>
    </tr>
    <tr>
        <td class="space"></td>
    </tr>
    <tr>
        <td></td>
        <td class="any"><?= $labels['any_I'] ?></td>
        <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
        <td class="cabecera"><?= $labels['iudicium'] ?></td>
    </tr>
                <?php
                break;
            case 2201:
                ?>
</table>
<br>
</div>
<div class="ects"><?= $labels['pie_ects'] ?>
</div>
<div class="A4">
    <table>
        <col style="width: 7%">
        <col style="width: 45%">
        <col style="width: 5%">
        <col style="width: 36%">
        <col style="width: 7%">
        <tr>
            <td class="space_doble"></td>
        </tr>
        <tr>
            <td></td>
            <td class="any"><?= $labels['any_II'] ?></td>
            <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
            <td class="cabecera"><?= $labels['iudicium'] ?></td>
        </tr>
                <?php
                break;
            case 2301:
                ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $labels['any_III'] ?></td>
                <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
                <td class="cabecera"><?= $labels['iudicium'] ?></td>
            </tr>
            <?php
                break;
            case 2401:
                ?>
            <tr>
                <td class="space_doble"></td>
            </tr>
            <tr>
                <td></td>
                <td class="any"><?= $labels['any_IV'] ?></td>
                <td class="cabecera"><?= $labels['ECTS'] ?><sup>1</sup></td>
                <td class="cabecera"><?= $labels['iudicium'] ?></td>
            </tr>
            <?php
                break;
        }
    }
}
