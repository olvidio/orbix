<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = input_string($_POST, 'id_tipo_doc');
$Qnumerado = input_string($_POST, 'numerado');
$Qstr_selected_id = input_string($_POST, 'str_selected_id');
$Qf_recibido = input_string($_POST, 'f_recibido');
$Qf_asignado = input_string($_POST, 'f_asignado');

$selected_id = json_decode(rawurldecode($Qstr_selected_id), true);
if (!is_array($selected_id)) {
    $selected_id = [];
}
$error_txt = '';

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
$i = 0;
foreach ($selected_id as $id_ubi_raw) {
    if (!is_numeric($id_ubi_raw)) {
        continue;
    }
    $id_ubi = (int) $id_ubi_raw;
    $var_num = 'num_' . $id_ubi;
    $numRaw = $_POST[$var_num] ?? 0;
    $num = is_numeric($numRaw) ? (int) $numRaw : 0;
    $cDocumentos = $DocumentoRepository->getDocumentos(['id_ubi' => $id_ubi, 'id_tipo_doc' => $Qid_tipo_doc]);
    if (empty($cDocumentos)) {
        /** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
        $id_new = $DocumentoRepository->getNewId();
        $oDocumento = new Documento();
        $oDocumento->setId_doc($id_new);
        $oDocumento->setIdUbiVo($id_ubi);
        $oDocumento->setId_tipo_doc((int) $Qid_tipo_doc);
    } else {
        $oDocumento = $cDocumentos[0];
    }

    if (!empty($Qnumerado)) {
        $oDocumento->setNum_reg($num);
    } else {
        $oDocumento->setNum_ejemplares($num);
    }

    // Si está vacío no hago nada (dejo lo que esté)
    if (!empty($Qf_recibido)) {
        $rawF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);
        $oDocumento->setF_recibido($rawF_recibido instanceof DateTimeLocal ? $rawF_recibido : null);
    }

    if (!empty($Qf_asignado)) {
        $rawF_asignado = DateTimeLocal::createFromLocal($Qf_asignado);
        $oDocumento->setF_asignado($rawF_asignado instanceof DateTimeLocal ? $rawF_asignado : null);
    }

    if ($DocumentoRepository->Guardar($oDocumento) === false) {
        $error_txt .= _("hay un error, no se ha guardado");
        $error_txt .= "\n" . $DocumentoRepository->getErrorTxt();
    }
}

ContestarJson::enviar($error_txt, 'ok');

