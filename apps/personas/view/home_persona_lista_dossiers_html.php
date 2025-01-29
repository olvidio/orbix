<?php use core\ConfigGlobal;
use dossiers\model\PermDossier; ?>
    <h2 class=titulo><?= ucfirst(_("relación de dossiers")); ?></h2>
<?php
$lista_doss = "SELECT * 
			FROM  d_tipos_dossiers dt 
			WHERE tabla_from='$pau' 
			ORDER BY dt.id_tipo_dossier ";
$oDBSt_d_lista = $oDB->query($lista_doss);

echo "<table border=1>";
$i = 0;
foreach ($oDBSt_d_lista->fetchAll() as $row) {
    extract($row);
    $id_dossier = $id_tipo_dossier;
    $q_doss = "SELECT status_dossier 
				FROM  d_dossiers_abiertos da
				WHERE da.id_pau=$id_pau AND id_tipo_dossier=$id_tipo_dossier
				ORDER BY da.id_tipo_dossier ";
    //echo "sql: $q_doss<br>";
    $oDBSt_d = $oDB->query($q_doss);
    if ($oDBSt_d->rowCount()) {
        $status_dossier = $oDBSt_d->fetchColumn();
    } else {
        $status_dossier = "";
    }
    switch ($status_dossier) {
        case "t":
            $imagen = ConfigGlobal::getWeb_icons() . '/images/folder.open.gif';
            break;
        case "f":
            $imagen = ConfigGlobal::getWeb_icons() . '/images/folder.gif';
            break;
        default:
            $imagen = ConfigGlobal::getWeb_icons() . '/images/generic.sec.gif';
            break;
    }

    $clase = "imp";
    $i % 2 ? 0 : $clase = "par";
    $i++;
    $perm_a = PermDossier::permiso($permiso_lectura, $permiso_escritura, $depende_modificar, $pau, $id_pau);

    $href_ver = ConfigGlobal::getWeb() . '/programas/dossiers/dossiers_ver.php?pau=' . $pau . '&id_pau=' . $id_pau . '&tabla_pau=' . $tabla_pau . '&id_dossier=' . $id_dossier . '&permiso=' . $perm_a . '&depende=' . $depende_modificar;
    $href_abrir = ConfigGlobal::getWeb() . '/programas/dossiers/dossier_abrir.php?pau=' . $pau . '&id_pau=' . $id_pau . '&tabla_pau=' . $tabla_pau . '&id_dossier=' . $id_dossier . '&tabla_to=' . $tabla_to . '&permiso=' . $perm_a;

    echo "<tr class=$clase>";
    switch ($perm_a) {
        case 1: //no tiene permisos
            $imagen = ConfigGlobal::getWeb_icons() . '/images/folder.sec.gif';
            ?>
            <td></td>
            <td><?= $descripcion; ?></td>
            <?php break;
        case 2: // sólo lectura
            ?>
            <td><img class="dossier" src='<?= $imagen ?>'></td>
            <td><span class="link" onclick="fnjs_update_div('#main','<?= $href_ver ?>');"><font
                            color="green"><?= $descripcion ?></font></span></td>
            <?php break;
        case 3: //lectura y escritura
            ?>
            <td><img class="dossier" src='<?= $imagen ?>'></td>
            <td link="red"><span class="link"
                                 onclick="fnjs_update_div('#main','<?= $href_ver ?>');"><?= $descripcion ?></span></td>
            <?php break;
    }
    echo "</tr>";
}
echo "</table>";
