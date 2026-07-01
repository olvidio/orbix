<?php

namespace src\encargossacd\application\services;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

/**
 * HTML de encargos SACD por ubicación (editable y solo lectura).
 */
final class PropuestasEncargosUbiHtml
{
    public function __construct(
        private EncargoRepositoryInterface $encargoRepository,
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private PropuestasHorarioTexto $horarioTexto,
    ) {
    }

    public function editable(int $id_ubi): string
    {
        $html = '';
        foreach ($this->datosPorUbi($id_ubi, 'modo,id_nom,f_ini') as $fila) {
            $html .= $this->renderEditable($fila['encargo'], $fila['slots']);
        }

        return $html;
    }

    public function simple(int $id_ubi): string
    {
        $html = '';
        foreach ($this->datosPorUbi($id_ubi, 'id_nom,f_ini') as $fila) {
            $html .= $this->renderSimple($fila['encargo'], $fila['slots']);
        }

        return $html;
    }

    /**
     * @return list<array{encargo: Encargo, slots: array<string, mixed>}>
     */
    private function datosPorUbi(int $id_ubi, string $ordenPropuestas): array
    {
        $cEncargos = $this->encargoRepository->getEncargos(
            ['id_ubi' => $id_ubi, 'id_tipo_enc' => '(1|2|3).0'],
            ['id_tipo_enc' => '~'],
        );
        $out = [];
        foreach ($cEncargos as $oEncargo) {
            $propuestas = $this->propuestaEncargoSacdRepository->getPropuestasEncargoSacd(
                ['id_enc' => $oEncargo->getId_enc(), 'f_fin' => 'x'],
                ['f_fin' => 'IS NULL', '_ordre' => $ordenPropuestas],
            );
            $out[] = [
                'encargo' => $oEncargo,
                'slots' => $this->slotsDesdePropuestas($propuestas),
            ];
        }

        return $out;
    }

    /**
     * @param list<PropuestaEncargoSacd> $propuestas
     * @return array{
     *   titular: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   suplente: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   colaboradores: list<array{id_sacd: int, id_sacd_new: int, id_item: int}>
     * }
     */
    private function slotsDesdePropuestas(array $propuestas): array
    {
        $titular = ['id_sacd' => 0, 'id_sacd_new' => 0, 'id_item' => 0];
        $suplente = ['id_sacd' => 0, 'id_sacd_new' => 0, 'id_item' => 0];
        $colaboradores = [];

        foreach ($propuestas as $oPropuesta) {
            $modo = $oPropuesta->getModo();
            $slot = [
                'id_sacd' => $oPropuesta->getId_nom(),
                'id_sacd_new' => (int) ($oPropuesta->getId_nom_new() ?? 0),
                'id_item' => $oPropuesta->getId_item(),
            ];
            if ($modo === 2 || $modo === 3) {
                $titular = $slot;
            } elseif ($modo === 4) {
                $suplente = $slot;
            } elseif ($modo === 5) {
                $colaboradores[] = $slot;
            }
        }

        return [
            'titular' => $titular,
            'suplente' => $suplente,
            'colaboradores' => $colaboradores,
        ];
    }

    /**
     * @param array{
     *   titular: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   suplente: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   colaboradores: list<array{id_sacd: int, id_sacd_new: int, id_item: int}>
     * } $slots
     */
    private function renderEditable(Encargo $oEncargo, array $slots): string
    {
        $desc_encargo = $oEncargo->getDesc_enc();
        $id_enc = $oEncargo->getId_enc();

        $html = '<table><tr><td>';
        $html .= _('nombre del encargo');
        $html .= '</td><td colspan=4>';
        $html .= $desc_encargo;
        $html .= '</td></tr>';

        $html .= $this->filaEditable('titular', $slots['titular'], $id_enc, _('titular'), true);
        $html .= $this->filaEditable('suplente', $slots['suplente'], $id_enc, _('suplente'), false);

        $s = 0;
        foreach ($slots['colaboradores'] as $col) {
            $s++;
            $html .= $this->filaEditableColaborador($col, $id_enc, $s < 2);
        }

        $id_item = $id_enc;
        $id_sacd = 1;
        $id_sacd_new = 1;
        $nom_col = '';
        $nom_sacd_new = _('nuevo');
        $class = '';
        if ($s < 1) {
            $html .= "<tr id=\"tr_colaborador$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
            $html .= _('colaboradores');
        } else {
            $html .= "<tr id=\"tr_colaborador$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
        }
        $html .= '</td><td>';
        $html .= $nom_col;
        $html .= "</td><td>";
        $html .= "<span class=\"link\" id=\"colaborador_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('colaborador',$id_item,$id_enc)\">";
        $html .= "$nom_sacd_new</span>";
        $html .= '</td><td>';
        $html .= "<span class=\"link\" onClick=\"fnjs_info('colaborador',$id_item)\">" . _('+ info') . '</span>';
        $html .= '</td><td>';
        $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('colaborador',$id_item,$id_enc)\">";
        $html .= $this->horarioTexto->propuestaTxt($id_enc, $id_sacd_new);
        $html .= "</td><td id=\"td_$id_item\">";
        $html .= '</td></tr>';

        $html .= '</table>';

        return $html;
    }

    /**
     * @param array{id_sacd: int, id_sacd_new: int, id_item: int} $slot
     */
    private function filaEditable(string $tipo, array $slot, int $id_enc, string $etiqueta, bool $conHorarioActual): string
    {
        $id_sacd = $slot['id_sacd'];
        $id_sacd_new = $slot['id_sacd_new'];
        $id_item = $slot['id_item'] > 0 ? $slot['id_item'] : $id_enc;

        $nom_actual = $this->nombreSacd($id_sacd);
        $nom_actual = $nom_actual === '' ? '-' : $nom_actual;
        $nom_nuevo = $this->nombreSacd($id_sacd_new);
        $nom_nuevo = $nom_nuevo === '' ? _('nuevo') : $nom_nuevo;

        $class = ($id_sacd !== $id_sacd_new) ? 'sf' : '';
        $html = "<tr id=\"tr_{$tipo}$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
        $html .= $etiqueta;
        $html .= '</td><td>';
        $html .= $nom_actual;
        if ($conHorarioActual) {
            $html .= '  ' . $this->horarioTexto->actualTxt($id_enc, $id_sacd);
        }
        $html .= '</td><td>';
        $html .= "<span class=\"link\" id=\"{$tipo}_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('$tipo',$id_item,$id_enc)\">";
        $html .= "$nom_nuevo</span>";
        $html .= '</td><td>';
        $html .= "<span class=\"link\" onClick=\"fnjs_info('$tipo',$id_item)\">" . _('+ info') . '</span>';
        $html .= '</td><td>';
        if ($conHorarioActual) {
            $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('$tipo',$id_item,$id_enc)\">";
            $html .= $this->horarioTexto->propuestaTxt($id_enc, $id_sacd_new);
            $html .= '</span>';
        }
        $html .= "</td><td id=\"td_$id_item\">";
        $html .= '</td></tr>';

        return $html;
    }

    /**
     * @param array{id_sacd: int, id_sacd_new: int, id_item: int} $slot
     */
    private function filaEditableColaborador(array $slot, int $id_enc, bool $mostrarEtiqueta): string
    {
        $id_sacd = $slot['id_sacd'];
        $id_sacd_new = $slot['id_sacd_new'];
        $id_item = $slot['id_item'];

        $nom_col = $this->nombreSacd($id_sacd);
        $nom_sacd_new = $this->nombreSacd($id_sacd_new);
        $nom_sacd_new = $nom_sacd_new === '' ? _('nuevo') : $nom_sacd_new;

        $class = ($id_sacd !== $id_sacd_new) ? 'sf' : '';
        $html = "<tr id=\"tr_colaborador$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
        if ($mostrarEtiqueta) {
            $html .= _('colaboradores');
        }
        $html .= '</td><td>';
        $html .= $nom_col;
        if ($nom_col !== '') {
            $html .= '  ' . $this->horarioTexto->actualTxt($id_enc, $id_sacd);
        }
        $html .= '</td><td>';
        $html .= "<span class=\"link\" id=\"colaborador_$id_item\" title=\"$id_sacd_new\" onClick=\"fnjs_ver_sacd_posibles('colaborador',$id_item,$id_enc)\">";
        $html .= "$nom_sacd_new</span>";
        $html .= '</td><td>';
        $html .= "<span class=\"link\" onClick=\"fnjs_info('colaborador',$id_item)\">" . _('+ info') . '</span>';
        $html .= '</td><td>';
        $html .= "<span class=\"link\" onClick=\"fnjs_dedicacion('colaborador',$id_item,$id_enc)\">";
        $html .= $this->horarioTexto->propuestaTxt($id_enc, $id_sacd_new);
        $html .= "</td><td id=\"td_$id_item\">";
        $html .= '</td></tr>';

        return $html;
    }

    /**
     * @param array{
     *   titular: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   suplente: array{id_sacd: int, id_sacd_new: int, id_item: int},
     *   colaboradores: list<array{id_sacd: int, id_sacd_new: int, id_item: int}>
     * } $slots
     */
    private function renderSimple(Encargo $oEncargo, array $slots): string
    {
        $desc_encargo = $oEncargo->getDesc_enc();
        $id_enc = $oEncargo->getId_enc();

        $html = '<table>';
        $html .= '<tr>';
        $html .= '<th web-width="70" >';
        $html .= '<th web-width="250" >';
        $html .= '<th web-width="250" >';
        $html .= '</tr>';
        $html .= '<tr><td colspan=5>';
        $html .= "<b>$desc_encargo</b>";
        $html .= '</td></tr>';

        $html .= $this->filaSimple('titular', $slots['titular'], $id_enc, _('titular'), 'warning');
        $html .= $this->filaSimple('suplente', $slots['suplente'], $id_enc, _('suplente'), 'sf');

        $s = 0;
        foreach ($slots['colaboradores'] as $col) {
            $s++;
            $html .= $this->filaSimpleColaborador($col, $id_enc, $s < 2);
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * @param array{id_sacd: int, id_sacd_new: int, id_item: int} $slot
     */
    private function filaSimple(string $tipo, array $slot, int $id_enc, string $etiqueta, string $classCambio): string
    {
        $id_sacd = $slot['id_sacd'];
        $id_sacd_new = $slot['id_sacd_new'];
        $id_item = $slot['id_item'] > 0 ? $slot['id_item'] : $id_enc;

        $nom_actual = $this->nombreSacd($id_sacd);
        $nom_nuevo = $this->nombreSacd($id_sacd_new);
        $nom_nuevo = $nom_nuevo === '' ? '-' : $nom_nuevo;

        $class = ($id_sacd !== $id_sacd_new) ? $classCambio : '';
        $html = "<tr id=\"tr_{$tipo}$id_item\" class=\"$class\" title=\"$id_sacd\">";
        $html .= '<td>';
        $html .= $etiqueta;
        $html .= "</td><td class=\"$class\" >";
        $html .= $nom_actual;
        if ($tipo === 'titular') {
            $html .= '  (' . $this->horarioTexto->actualTxt($id_enc, $id_sacd) . ')';
        }
        $html .= "</td><td class=\"$class\" >";
        $html .= $nom_nuevo;
        if ($tipo === 'titular') {
            $html .= '  (' . $this->horarioTexto->propuestaTxt($id_enc, $id_sacd_new) . ')';
        }
        $html .= '</td>';
        $html .= '</td></tr>';

        return $html;
    }

    /**
     * @param array{id_sacd: int, id_sacd_new: int, id_item: int} $slot
     */
    private function filaSimpleColaborador(array $slot, int $id_enc, bool $mostrarEtiqueta): string
    {
        $id_sacd = $slot['id_sacd'];
        $id_sacd_new = $slot['id_sacd_new'];
        $id_item = $slot['id_item'];

        $nom_col = $this->nombreSacd($id_sacd);
        $nom_nuevo = $this->nombreSacd($id_sacd_new);
        $nom_nuevo = $nom_nuevo === '' ? '-' : $nom_nuevo;

        $class = ($id_sacd !== $id_sacd_new) ? 'sf' : '';
        $html = "<tr id=\"tr_colaborador$id_item\" class=\"$class\" title=\"$id_sacd\"><td>";
        if ($mostrarEtiqueta) {
            $html .= _('colaboradores');
        }
        $html .= '</td><td>';
        $html .= $nom_col;
        if ($nom_col !== '') {
            $html .= '  (' . $this->horarioTexto->actualTxt($id_enc, $id_sacd) . ')';
        }
        $html .= '</td><td>';
        $html .= $nom_nuevo;
        $html .= '  (' . $this->horarioTexto->propuestaTxt($id_enc, $id_sacd_new) . ')';
        $html .= '</td></tr>';

        return $html;
    }

    private function nombreSacd(int $id_sacd): string
    {
        if ($id_sacd <= 0) {
            return '';
        }
        $persona = $this->personaSacdRepository->findById($id_sacd);

        return $persona?->getApellidosNombre() ?? '';
    }
}
