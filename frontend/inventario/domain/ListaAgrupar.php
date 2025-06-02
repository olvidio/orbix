<?php

namespace frontend\inventario\domain;

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;

class ListaAgrupar
{
    private string $texto = '';
    private array $aOpciones = [];

    public function listaAgrupar($a_valores, $id_grupo = 0)
    {
        $pencil = ConfigGlobal::getWeb_icons() . '/pencil.png';

        // para el grupo == 0, no añado la opción de modificar el texto.
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
        $lugar_old = '';
        $ident_num = '';
        $ident_txt = '';
        $que_cuento = '';
        $id_col = '';
        foreach ($a_valores as $row) {
            //si son iguales; contarlos. OJO hay que distinguir si es por colección o por documento
            if (($id_tipo_old == $row[1])) {
                $que_cuento = 'tipo_doc';
                if (!empty($ident_num) && !empty($row[2])) {
                    $ident_num .= ',' . $row[2];
                }
                $ident_txt = empty($ident_num) ? "" : " ($ident_num)";
                $count++;
                continue;
            }
            if (!empty($row[5]) && ($agrupar_old == $row[5]) && ($coleccion_old == $row[4])) {
                $id_col = !empty($row[4]) ? $row[4] : '';
                if (!empty($ident_num) && !empty($row[2])) {
                    $ident_num .= ',' . $row[2];
                }
                $ident_txt = empty($ident_num) ? "" : " ($ident_num)";
                if ($que_cuento === 'tipo_doc') {
                    $html_g .= $this->escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_col);
                    $id_tipo_old = $row[1];
                    $id_old = $row[2];
                    $agrupar_old = $row[5];
                    $coleccion_old = $row[4];
                    $count = 1;
                    $ident_num = $id_old;
                    $ident_txt = '';
                }
                $que_cuento = 'coleccion';
                $count++;
                continue;
            }
            if (!empty($id_tipo_old)) {
                $html_g .= $this->escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_col);
            }
            if ((!empty($row[3]) && $lugar_old != $row[3])) {
                $html_g .= '<u>' . $row[3] . '</u><br>';
                $lugar_old = $row[3];
            }
            $id_tipo_old = $row[1];
            $id_old = $row[2];
            $agrupar_old = empty($row[5]) ? '' : $row[5];
            $coleccion_old = empty($row[4]) ? '' : $row[4];
            $count = 1;
            $ident_num = $id_old;
            $ident_txt = '';
            $id_col = '';
        }
        // para el último.
        $html_g .= $this->escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_col);

        // para el grupo == 0, no añado la opción de modificar el texto.
        if ($id_grupo > 0) {
            $html_g .= "<div class=\"no_print\" style=\"margin-bottom: 10px\">";
            $html_g .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
                title='" . _("modificar texto") . "''
                alt='" . _("modificar texto") . "'
                onClick=\"fnjs_mod_texto_equipaje('docs_grupo_$id_grupo')\" >";
            $html_g .= empty($this->texto) ? _("introducir texto") : '';
            $html_g .= "</div>";
            $html_g .= $this->texto;
            $html_g .= "</span>"; // id='docs_grupo_$id_grupo'
        } else {
            $html_g .= $this->texto;
        }

        return $html_g;
    }

    private function escribir($ident_num, $count, $id_old, $ident_txt, $agrupar_old, $id_tipo_old, $id_col)
    {
        $aColecciones = $this->getColecciones();
        $html_g = '';
        if (!empty($ident_num) && $count > 1) {
            $html_g .= !empty($count) ? $count . ' ' . _("ejemplares") . ' ' : '';
        } else {
            $html_g .= !empty($id_old) ? "-" . $id_old . "- " : '';
        }
        if (!empty($ident_txt)) {
            $ident_txt2 = trim($ident_txt);
            $ident_txt2 = trim($ident_txt2, "()");
            $a_ident_txt = explode(',', $ident_txt2);
            $first = reset($a_ident_txt);
            $last = end($a_ident_txt);
            if (($last - $first) === ($count - 1)) {
                $ident_txt = sprintf(_(" (del %d al %d)"), $first, $last);
            }
        }
        if (!empty($agrupar_old)) {
            $txt_cartas = $aColecciones[$id_col] ?? '????';
            $html_g .= $txt_cartas . $ident_txt;
        } else {
            $html_g .= $id_tipo_old . $ident_txt;
        }
        $html_g .= empty($html_g) ? '' : "<br />";
        return $html_g;

    }

    private function getColecciones()
    {
        if (empty($this->aOpciones)) {
            //-------- listado de colecciones -----------------------------------
            $url_lista_backend = Hash::link(ConfigGlobal::getWeb()
                . '/src/inventario/infrastructure/controllers/lista_colecciones.php'
            );
            $oHash = new Hash();
            $oHash->setUrl($url_lista_backend);
            $hash_params = $oHash->getArrayCampos();

            $data = PostRequest::getData($url_lista_backend, $hash_params);

            $this->aOpciones = $data['a_opciones'];
        }
        return $this->aOpciones;
    }

    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

}