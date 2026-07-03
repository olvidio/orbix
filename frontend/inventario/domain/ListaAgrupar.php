<?php

namespace frontend\inventario\domain;

use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;


class ListaAgrupar
{
    private string $texto = '';

    /** @var array<int|string, string> */
    private array $aOpciones = [];

    /**
     * @param list<array{
     *     nombre: string,
     *     identificador: string,
     *     carta: string,
     *     coleccion: string,
     *     ejemplares: string,
     *     lugar: string,
     * }> $a_valores
     */
    public function listaAgrupar(array $a_valores, int $id_grupo = 0): string
    {
        $pencil = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/images/pencil.png';

        if ($id_grupo > 0) {
            $html_g = "<span id='docs_grupo_$id_grupo' >";
        } else {
            $html_g = '';
        }
        $count = 1;
        $id_old = '';
        $id_tipo_old = '';
        $agrupar_old = '';
        $coleccion_old = '';
        $ejemplares_old = '';
        $lugar_old = '';
        $ident_num = '';
        $ident_txt = '';
        $que_cuento = '';
        $id_coleccion = '';
        foreach ($a_valores as $row) {
            if (($id_tipo_old === $row['nombre'])) {
                $que_cuento = 'tipo_doc';
                if ($ident_num !== '' && $row['identificador'] !== '') {
                    $ident_num .= ',' . $row['identificador'];
                }
                $ident_txt = $ident_num === '' ? '' : " ($ident_num)";
                $count++;
                continue;
            }
            if ($row['carta'] !== '' && ($agrupar_old == $row['carta']) && ($coleccion_old == $row['coleccion'])) {
                $que_cuento = 'coleccion';
                $id_coleccion = $row['coleccion'] !== '' ? $row['coleccion'] : '';
                if ($ident_num !== '' && $row['identificador'] !== '') {
                    $ident_num .= ',' . $row['identificador'];
                }
                $ident_txt = $ident_num === '' ? '' : " ($ident_num)";
                $count++;
                continue;
            }
            if ($id_tipo_old !== '') {
                $html_g .= $this->escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_coleccion, $ejemplares_old);
            }
            if ($row['lugar'] !== '' && $lugar_old != $row['lugar']) {
                $html_g .= '<u>' . $row['lugar'] . '</u><br>';
                $lugar_old = $row['lugar'];
            }
            $id_tipo_old = $row['nombre'];
            $id_old = $row['identificador'];
            $agrupar_old = $row['carta'] === '' ? '' : $row['carta'];
            $coleccion_old = $row['coleccion'] === '' ? '' : $row['coleccion'];
            $ejemplares_old = $row['ejemplares'];
            $count = 1;
            $ident_num = $id_old;
            $ident_txt = '';
            $id_coleccion = '';
        }
        $html_g .= $this->escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_coleccion, $ejemplares_old);

        if ($id_grupo > 0) {
            $html_g .= "<div class=\"no_print\" style=\"margin-bottom: 10px\">";
            $html_g .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
                title='" . _("modificar texto") . "''
                alt='" . _("modificar texto") . "'
                onClick=\"fnjs_mod_texto_equipaje('docs_grupo_$id_grupo')\" >";
            $html_g .= $this->texto === '' ? _("introducir texto") : '';
            $html_g .= "</div>";
            $html_g .= $this->texto;
            $html_g .= "</span>";
        } else {
            $html_g .= $this->texto;
        }

        return $html_g;
    }

    private function escribir(
        string $ident_num,
        int $count,
        string $id_old,
        string $ident_txt,
        string $agrupar_old,
        string $id_tipo_old,
        string $id_coleccion,
        string $ejemplares_old
    ): string {
        $aColecciones = $this->getColecciones();
        $html_g = '';
        if ($ident_num !== '' && $count > 1) {
            if ($ejemplares_old !== '') {
                $html_g .= $ejemplares_old . ' x ';
            }
            $html_g .= $count . ' ' . _("ejemplares") . ' ';
        } else {
            $html_g .= $id_old !== '' ? '-' . $id_old . '- ' : '';
        }
        if ($ident_txt !== '') {
            $ident_txt2 = trim($ident_txt);
            $ident_txt2 = trim($ident_txt2, '()');
            $a_ident_txt = explode(',', $ident_txt2);
            $first = reset($a_ident_txt);
            $last = end($a_ident_txt);
            if (((int) $last - (int) $first) === ($count - 1)) {
                $ident_txt = sprintf(_(" (del %d al %d)"), $first, $last);
            }
        }
        if ($agrupar_old !== '') {
            $txt_cartas = $aColecciones[$id_coleccion] ?? '????';
            $html_g .= $txt_cartas . $ident_txt;
        } else {
            $html_g .= $id_tipo_old . $ident_txt;
        }
        $html_g .= $html_g === '' ? '' : '<br />';

        return $html_g;
    }

    /**
     * @return array<int|string, string>
     */
    private function getColecciones(): array
    {
        if ($this->aOpciones === []) {
            $url_backend = '/src/inventario/lista_colecciones';
            $data = PostRequest::getDataFromUrl($url_backend);
            $payload = InventarioPayload::postPayload($data);
            $this->aOpciones = InventarioPayload::coleccionesOpciones($payload['a_opciones'] ?? []);
        }

        return $this->aOpciones;
    }

    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }
}
