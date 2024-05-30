<?php

use core\ConfigGlobal;
use dbextern\model\entity\GestorIdMatchPersona;
use dbextern\model\entity\GestorPersonaListas;
use dbextern\model\entity\PersonaListas;
use dbextern\model\SincroDB;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo');
$mi_dl = ConfigGlobal::mi_delef();
$region = ConfigGlobal::mi_region();

$oSincroDB = new \dbextern\model\SincroDB();
$dl_listas = $oSincroDB->dlOrbix2Listas($mi_dl);

$id_tipo = 0;
switch ($tipo_persona) {
    case 'n':
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) {
            $id_tipo = 1;
            $obj_pau = 'GestorPersonaN';
        }
        break;
    case 'a':
        if ($_SESSION['oPerm']->have_perm_oficina('agd')) {
            $id_tipo = 2;
            $obj_pau = 'GestorPersonaAgd';
        }
        break;
    case 's':
        if ($_SESSION['oPerm']->have_perm_oficina('sg')) {
            $id_tipo = 3;
            $obj_pau = 'GestorPersonaS';
        }
        break;
    case 'sssc':
        if ($_SESSION['oPerm']->have_perm_oficina('des')) {
            $id_tipo = 4;
            $obj_pau = 'GestorPersonaSSSC';
        }
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}

if (empty($id_tipo)) {
    exit(_("no tiene permisos"));
}

//Orbix
$obj = 'personas\\model\\entity\\' . $obj_pau;
$GesPersonas = new $obj();

//listas
$Query = "SELECT * FROM dbo.q_dl_Estudios_b
          WHERE Identif LIKE '$id_tipo%' AND  Dl='$dl_listas'
               AND (pertenece_r='$region' OR compartida_con_r='$region') ";
// todos los de listas
$oGesListas = new GestorPersonaListas();
$cPersonasListas = $oGesListas->getPersonaListasQuery($Query);

// Añadir las delegaciones dependientes de la región (que no tienen esquema propio)
if (array_key_exists($region, ConfigGlobal::REGIONES_CON_DL)) {
    $cPersonasListas_n = [];
    foreach (ConfigGlobal::REGIONES_CON_DL[$region] as $dl_n) {
        $Query = "SELECT * FROM dbo.q_dl_Estudios_b
          WHERE Identif LIKE '$id_tipo%' AND  Dl='$dl_n'
               AND (pertenece_r='$region' OR compartida_con_r='$region') ";
        // todos los de listas
        $oGesListas = new GestorPersonaListas();
        $cPersonasListas_n[] = $oGesListas->getPersonaListasQuery($Query);

    }
    $cPersonasListas = array_merge($cPersonasListas, ...array_values($cPersonasListas_n));
}

$p1_unidas_dl = 0;
$p2_unidas_otradl = 0;
$p3_unidas_desaparecidas = 0;
$p456_listas_no_unidas = 0;

$a_ids_traslados = array();
$a_ids_desaparecidos_de_orbix = array();

$oSincroDB = new SincroDB();
$oSincroDB->setTipo_persona($tipo_persona);
foreach ($cPersonasListas as $oPersonaListas) {
    $id_nom_listas = $oPersonaListas->getIdentif();

    $oGesMatch = new GestorIdMatchPersona();
    $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_listas' => $id_nom_listas));
    // unidas (1,2,3)
    if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
        //comprobar situación = 'A'
        $id_orbix = $cIdMatch[0]->getId_orbix();
        $cPersonas = $GesPersonas->getPersonasDl(array('id_nom' => $id_orbix));
        if (!empty($cPersonas) && count($cPersonas) > 0) {
            $situacion = $cPersonas[0]->getSituacion();
            if ($situacion === 'A') {
                //1
                $p1_unidas_dl++;
            } else { //seguramente está en otra dl
                $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
                if (!empty($dl_orbix)) {
                    $p2_unidas_otradl++;
                    $a_ids_traslados[] = $id_orbix;
                }
            }
        } else { // Unida pero no en la dl
            //2. està en otra dl?
            $dl_orbix = $oSincroDB->buscarEnOrbix($id_orbix);
            if (!empty($dl_orbix)) {
                $p2_unidas_otradl++;
                $a_ids_traslados[] = $id_orbix;
            } else {
                // 3. en ninguna dl
                $p3_unidas_desaparecidas++;
                $a_ids_desaparecidos_de_orbix[] = $id_nom_listas;
            }
        }
    } else { // no unidas (4,5,6)
        // Buscar por apellidos, nombre
        //SUMA
        $p456_listas_no_unidas++;
    }
}

$ids_traslados = json_encode($a_ids_traslados);
$ids_desaparecidos_de_orbix = json_encode($a_ids_desaparecidos_de_orbix);

// todos los de orbix
$cPersonasOrbix = $GesPersonas->getPersonasDl(array('situacion' => 'A'));
$p7_orbix_unidas_otra_dl = 0;
$p8_orbix_unidas_desaparecidas = 0;
$p910_orbix_no_unidas = 0;

$a_ids_traslados_A = array();
$a_ids_desaparecidos_de_listas = array();

foreach ($cPersonasOrbix as $oPersonaOrbix) {
    $id_nom_orbix = $oPersonaOrbix->getId_nom();

    $oGesMatch = new GestorIdMatchPersona();
    $cIdMatch = $oGesMatch->getIdMatchPersonas(array('id_orbix' => $id_nom_orbix));
    // Unidas a listas 7 y 8
    if (!empty($cIdMatch[0]) && count($cIdMatch) > 0) {
        $id_nom_listas = $cIdMatch[0]->getId_listas();
        $oPersonaListas = new PersonaListas($id_nom_listas);
        //8. No listas
        if (empty($oPersonaListas->getApeNom())) {
            $p8_orbix_unidas_desaparecidas++;
            $a_ids_desaparecidos_de_listas[] = $id_nom_orbix;
        } else {
            $dl_persona = $oPersonaListas->getDl();
            //7. En otra dl en listas
            if ($dl_persona !== $dl_listas) {
                $p7_orbix_unidas_otra_dl++;
                $a_ids_traslados_A[] = $id_nom_listas;
            }
        }
    } else { // sin unir 9 y 10
        $p910_orbix_no_unidas++;
    }
}
$ids_traslados_A = json_encode($a_ids_traslados_A);
$ids_desaparecidos_de_listas = json_encode($a_ids_desaparecidos_de_listas);

$ver_2 = Hash::link('apps/dbextern/controller/ver_traslados.php?' . http_build_query(array('region' => $region, 'tipo_persona' => $tipo_persona, 'ids_traslados' => $ids_traslados)));
$ver_3 = Hash::link('apps/dbextern/controller/ver_desaparecidos_de_orbix.php?' . http_build_query(array('region' => $region, 'tipo_persona' => $tipo_persona, 'ids_desaparecidos_de_orbix' => $ids_desaparecidos_de_orbix)));
$ver_456 = Hash::link('apps/dbextern/controller/ver_listas.php?' . http_build_query(array('region' => $region, 'dl' => $dl_listas, 'tipo_persona' => $tipo_persona)));

$ver_7 = Hash::link('apps/dbextern/controller/ver_orbix_otradl.php?' . http_build_query(array('region' => $region, 'tipo_persona' => $tipo_persona, 'ids_traslados_A' => $ids_traslados_A)));
$ver_8 = Hash::link('apps/dbextern/controller/ver_desaparecidos_de_listas.php?' . http_build_query(array('region' => $region, 'tipo_persona' => $tipo_persona, 'ids_desaparecidos_de_listas' => $ids_desaparecidos_de_listas)));
$ver_910 = Hash::link('apps/dbextern/controller/ver_orbix.php?' . http_build_query(array('region' => $region, 'tipo_persona' => $tipo_persona)));

$url_sincro_ajax = ConfigGlobal::getWeb() . '/apps/dbextern/controller/sincro_ajax.php';
$oHash1 = new Hash();
$oHash1->setUrl($url_sincro_ajax);
$oHash1->setCamposForm('que!region!dl_listas!tipo_persona');
$h1 = $oHash1->linkSinVal();

$explicacion_txt = _("en situación normal bastaría hacer click en 'ejecutar' del punto 1.");
$explicacion_txt .= "<br>";
$explicacion_txt .= _("el resto de puntos deberían tener valor 0. En situaciones especiales pueden tener otro valor, pero deben ser casos controlados.");
$explicacion_txt .= "<br>";
$explicacion_txt .= _("al efectuar alguna acción dentro de las listas, las personas cambian de situación (no se arregla directamente). Es posible que se tengan que hacer varias pasadas antes de tener a todos en el punto 1");

?>
<script>
    fnjs_sincronizar = function () {
        var url = '<?= $url_sincro_ajax ?>';
        var parametros = 'que=syncro&region=<?= $region ?>&dl_listas=<?= $dl_listas ?>&tipo_persona=<?= $tipo_persona ?><?= $h1 ?>';

        $.ajax({
            url: url,
            type: 'post',
            data: parametros
        })
            .done(function (rta_txt) {
                if (rta_txt !== '' && rta_txt !== '\\n') {
                    alert('<?= _("respuesta") ?>: ' + rta_txt);
                }
            });
    }
</script>
<p>
    <?= $explicacion_txt ?>
</p>
<br>
<table>
    <tr>
        <th colspan="4"><?= _("personas en la BDU") ?></th>
    <tr>
        <td>1.</td>
        <td><?= _("personas a sincronizar") ?></td>
        <td><?= $p1_unidas_dl ?></td>
        <td><span class=link onclick="fnjs_sincronizar()"><?= _("ejecutar") ?></span></td>
    </tr>
    <tr>
        <td>2.</td>
        <td> <?= _("posiblemente estén en otra dl") ?></td>
        <td><?= $p2_unidas_otradl ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_2 ?>')"><?= _("ver") ?></span></td>
    </tr>
    <tr>
        <td>3.</td>
        <td><?= _("personas en la BDU desaparecidas en aquinate") ?></td>
        <td><?= $p3_unidas_desaparecidas ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_3 ?>')"><?= _("ver") ?></span></td>
    </tr>
    <tr>
        <td>4.</td>
        <td><?= _("personas no estan unidas a aquinate") ?></td>
        <td><?= $p456_listas_no_unidas ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_456 ?>')"><?= _("ver") ?></span></td>
    </tr>
    <tr>
        <th colspan="4"><?= _("personas en aquinate") ?></th>
    <tr>
        <td>7.</td>
        <td><?= _("personas en aquinate que deberían trasladarse a otra dl") ?></td>
        <td><?= $p7_orbix_unidas_otra_dl ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_7 ?>')"><?= _("ver") ?></span></td>
    </tr>
    <tr>
        <td>8.</td>
        <td><?= _("personas en aquinate con correspondencia en la BDU y desaparecidos de la BDU") ?></td>
        <td><?= $p8_orbix_unidas_desaparecidas ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_8 ?>')"><?= _("ver") ?></span></td>
    </tr>
    <tr>
        <td>9.</td>
        <td><?= _("personas en aquinate sin correspondencia en la BDU") ?></td>
        <td><?= $p910_orbix_no_unidas ?></td>
        <td><span class=link onclick="fnjs_update_div('#main','<?= $ver_910 ?>')"><?= _("ver") ?></span></td>
</table>
