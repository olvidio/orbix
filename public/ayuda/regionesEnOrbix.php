<?php

use core\ConfigGlobal;
use web\Hash;

$url = Hash::cmdSinParametros(ConfigGlobal::getWeb()
        . 'frontend/usuarios/controller/mails_contactos_region.php'
);

$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('region');
$hash_params = $oHash->getParamAjaxEnArray();

?>
<div id="lista_regiones">
<br>
<h3>Regiones o delegaciones que usan este programa actualmente</h3>
<table style="width:50%">
   <tr><th>sigla</th><th>región o dl</th><th>usuarios</th></tr>
   <tr><td>crAes</td><td>Asia Este y Sur</td><td><span class="link" onclick="fnjs_mostrar_modal('Aes-crAes')">ver contactos</span></td></tr>
   <tr><td>crAmc</td><td>América Central</td><td><span class="link" onclick="fnjs_mostrar_modal('Amc-crAmc')">ver contactos</span></td></tr>
   <tr><td>crAut</td><td>Australia</td><td><span class="link" onclick="fnjs_mostrar_modal('Aut-crAut')">ver contactos</span></td></tr>
   <tr><td>crCeb</td><td>Costa de Marfil</td><td><span class="link" onclick="fnjs_mostrar_modal('Ceb-crCeb')">ver contactos</span></td></tr>
   <tr><td>crCh</td><td>Chile</td><td><span class="link" onclick="fnjs_mostrar_modal('Ch-crCh')">ver contactos</span></td></tr>
   <tr><td>crCong</td><td>Congo</td><td><span class="link" onclick="fnjs_mostrar_modal('Cong-crCong')">ver contactos</span></td></tr>
   <tr><td>crEcs</td><td>Europa Central Norte</td><td><span class="link" onclick="fnjs_mostrar_modal('Ecs-crEcs')">ver contactos</span></td></tr>
   <tr><td>crEso</td><td>Europa del Noroeste</td><td><span class="link" onclick="fnjs_mostrar_modal('Eso-crEso')">ver contactos</span></td></tr>
   <tr><td>crEuc</td><td>Centroeuropa</td><td><span class="link" onclick="fnjs_mostrar_modal('Euc-crEuc')">ver contactos</span></td></tr>
   <tr><td>crGalbel</td><td>Francia y Bélgica</td><td><span class="link" onclick="fnjs_mostrar_modal('Galbel-crGalbel')">ver contactos</span></td></tr>
   <tr><td>crH</td><td>España</td><td><span class="link" onclick="fnjs_mostrar_modal('H-crH')">ver contactos</span></td></tr>
   <tr><td>crI</td><td>Italia</td><td><span class="link" onclick="fnjs_mostrar_modal('I-crI')">ver contactos</span></td></tr>
   <tr><td>crL</td><td>Portugal</td><td><span class="link" onclick="fnjs_mostrar_modal('L-crL')">ver contactos</span></td></tr>
   <tr><td>crM</td><td>México</td><td><span class="link" onclick="fnjs_mostrar_modal('M-crM')">ver contactos</span></td></tr>
   <tr><td>crNig</td><td>Nigeria</td><td><span class="link" onclick="fnjs_mostrar_modal('Nig-crNig')">ver contactos</span></td></tr>
   <tr><td>crP</td><td>Perú</td><td><span class="link" onclick="fnjs_mostrar_modal('P-crP')">ver contactos</span></td></tr>
   <tr><td>crPl</td><td>Filipinas</td><td><span class="link" onclick="fnjs_mostrar_modal('Pl-crPl')">ver contactos</span></td></tr>
   <tr><td>crPla</td><td>región del Plata</td><td><span class="link" onclick="fnjs_mostrar_modal('Pla-crPla')">ver contactos</span></td></tr>
   <tr><td>--</td>
   <tr><td>dlal</td><td>Aragón y Levante</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlal')">ver contactos</span></td></tr>
   <tr><td>dlb</td><td>Barcelona</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlb')">ver contactos</span></td></tr>
   <tr><td>dlg</td><td>Guadalajara</td><td><span class="link" onclick="fnjs_mostrar_modal('M-dlg')">ver contactos</span></td></tr>
   <tr><td>dlgr</td><td>Granada</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlgr')">ver contactos</span></td></tr>
   <tr><td>dlmE</td><td>Madrid Este</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlmE')">ver contactos</span></td></tr>
   <tr><td>dlmO</td><td>Madrid Oeste</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlmO')">ver contactos</span></td></tr>
   <tr><td>dln</td><td>Noroeste</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dln')">ver contactos</span></td></tr>
   <tr><td>dlp</td><td>Pamplona</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dlp')">ver contactos</span></td></tr>
   <tr><td>dls</td><td>Sevilla</td><td><span class="link" onclick="fnjs_mostrar_modal('H-dls')">ver contactos</span></td></tr>
   <tr><td>dly</td><td>Monterrey</td><td><span class="link" onclick="fnjs_mostrar_modal('M-dly')">ver contactos</span></td></tr>
</table>

    </div>

<div id='div_modal' style="display: none">
    <div id='div_cerrar'>
        <span class="link" onclick="fnjs_cerrar()" style="float: right; margin-bottom: 5px;">
            Cerrar [x]
        </span>
    </div>
    <div id='div_modificar5'></div>
</div>
<div id='overlay'></div>

<script type="application/javascript">

    fnjs_cerrar = function () {
        $('#div_modificar5').html('');
        $('#div_modal').width('0');
        $('#div_modal').height('0');
        $('#div_modal').removeClass('ventana');
        document.getElementById("overlay").style.display = "none";
        document.getElementById("div_modal").style.display = "none";
    }

    fnjs_mostrar_modal = function (region) {
          let request = $.ajax({
            url: 'frontend/usuarios/controller/mails_contactos_region.php',
            type: 'post',
            dataType: 'html',
            data: {
              <?= $hash_params ?>,
              region: region
            }
        });
        request.done(function (rta_html) {
            $('#div_modificar5').html(rta_html);
        });


        $('#div_modal').addClass('ventana');
        $('#div_modal').width('auto');
        $('#div_modal').height('auto');
        document.getElementById("overlay").style.display = "block";
        document.getElementById("div_modal").style.display = "block";
    }

</script>
