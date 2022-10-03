<?php
/**
 * Esta es la página de presentación de datos públicos de la ficha de n o agd.
 *
 * Esta presentación se incluye dentro del programa de visualización de
 * las fichas.
 * Existen 2 tipos de textos:
 *        td.etiqueta
 *        td.contenido
 *
 *
 *
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author Josep Companys
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;

require_once("global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$query_ctr = "select u.nombre_ubi AS nom from u_centros_dl u WHERE u.id_ubi=$id_ctr";
$oDBSt_q_ctr = $oDB->query($query_ctr);
$nom_ctr = $oDBSt_q_ctr->fetchColumn();
?>
<!--------------------- DATOS PERSONALES -------------------------->
<table border=<?= $border; ?>>
    <tr>
        <td class=titulo colspan="5">
            <?php print(strtoupper(_("datos personales"))); ?>
        </td>
    </tr>
    <tr>
        <?php
        switch ($tabla[0]) {
            case "p_agregados":
                $dir_foto = "agd";
                break;
            case "p_numerarios":
                $dir_foto = "sm";
                break;
            case "p_supernumerarios":
                $dir_foto = "s";
                break;
        }
        $file_foto = ConfigGlobal::$dir_fotos . '/' . $dir_foto . '/' . $id_nom . '.jpg';
        $foto = ConfigGlobal::$web_fotos . '/' . $dir_foto . '/' . $id_nom . '.jpg';
        if (file_exists($file_foto)) {
            echo "<td rowspan=4 align=center valign=top><table style='border: 0; width:55px;'><tr><td>
			<img  src='$foto' width=60 height=80 border=0 align='rigth' alt='foto carnet'>
			</td></tr><tr><td>$f_foto</td></tr></table>";
        }
        echo "</td>";
        echo dibujar_campo("trato", 7, 1, 0);
        echo dibujar_campo("nom", 20, 1, 2);
        echo "</tr><tr><td></td>";
        echo dibujar_campo("apel_fam", 20, 1, 2);
        echo "</tr><tr>";
        echo dibujar_campo("nx1", 7, 1, 0);
        echo dibujar_campo("apellido1", 25, 1, 2);
        echo "</tr><tr>";
        echo dibujar_campo("nx2", 7, 1, 0);
        echo dibujar_campo("apellido2", 25, 1, 2);
        echo "</tr><tr>";
        echo "<td>";
        echo ucfirst($dl_etiqueta) . ":&nbsp;<b class=contenido >$dl</b>";
        echo "</td>";
        echo dibujar_campo("lengua", 3, 1, 1);
        echo "</tr><tr>";
        echo dibujar_campo("f_nacimiento", 11, 1, 1);
        echo dibujar_campo("ciudad_n", 30, 1, 3);
        echo "</tr><tr>";
        echo dibujar_campo("provincia_n", 30, 1, 1);
        echo dibujar_campo("nacionalidad", 40, 1, 1);
        echo "</tr><tr>";

        if ($tabla[0] == "p_agregados") {
            /*datos personales propios de agd */
            echo dibujar_campo("c_p_p", 6, 1, 1);
            echo dibujar_campo("direccion_p", 30, 1, 1);
            echo "</tr><tr>";
            echo dibujar_campo("poblacion_p", 15, 1, 1);
            echo dibujar_campo("provincia_p", 30, 1, 1);
            echo "</tr><tr>";
        }

        echo dibujar_campo("santo", 4, 1, 1);
        echo dibujar_campo("celebra", 2, 1, 1);
        echo "</tr><tr>";
        echo dibujar_campo("f_fichero", 11, 1, 1);
        echo dibujar_campo("fichero", 2, 1, 1);
        echo "</tr><tr>";
        echo dibujar_campo("estudios", 35, 1, 3);
        echo "</tr><tr>";
        if ($tabla[0] == "p_agregados" || $tabla[0] == "p_numerarios") {
            /* datos personales comunes a n y agd*/
            echo dibujar_campo("stgr", 2, 1, 1);
            echo dibujar_campo("cgi_pa", 35, 1, 3);
            echo "</tr><tr>";
        }

        echo "<td>" . _("centro-sede") . ":</td><td class=contenido>$nom_ctr</td>";
        echo dibujar_campo("f_ctr", 11, 2, 0);
        ?>
</TABLE>
</body>
</html>
