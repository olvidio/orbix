<?php

/**
 * Función para eliminar los "&" que no son html
 *
 * @param string $html
 * @param integer $offset
 */
function fixAmps(&$html, $offset) {
    $positionAmp = strpos($html, '&', $offset);
    $positionSemiColumn = strpos($html, ';', $positionAmp+1);

    $string = substr($html, $positionAmp, $positionSemiColumn-$positionAmp+1);

    if ($positionAmp !== false) { // If an '&' can be found.
        if ($positionSemiColumn === false) { // If no ';' can be found.
//            $html = substr_replace($html, '&amp;', $positionAmp, 1); // Replace straight away.
            $html = substr_replace($html, '', $positionAmp, 1); // Lo elimino
        } else if (preg_match('/&(#[0-9]+|[A-Z|a-z|0-9]+);/', $string) === 0) { // If a standard escape cannot be found.
            $html = substr_replace($html, '&amp;', $positionAmp, 1); // This mean we need to escapa the '&' sign.
            fixAmps($html, $positionAmp+5); // Recursive call from the new position.
        } else {
            fixAmps($html, $positionAmp+1); // Recursive call from the new position.
        }
    }
}

switch ($_POST['frm_export_tipo']) {
    case "html":
        if ($_POST['frm_export_titulo']) {
            $nom = $_POST['frm_export_titulo'];
        } else {
            $nom = "export";
        }
        // quito las imágenes
        $documento = $_POST['frm_export_ex'];
        $documento = preg_replace("/<img(.*?)>/mi", "", $documento);
        $documento = stripslashes($documento);
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"$nom.html\"");
        echo "<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
        echo $documento;
        break;
    case "html_zip": // igual que el anterior más comprimir
        if ($_POST['frm_export_titulo']) {
            $nom = $_POST['frm_export_titulo'];
        } else {
            $nom = "planning";
        }
        // quito las imágenes
        $documento = $_POST['frm_export_ex'];
        $documento = preg_replace("/<img(.*?)>/mi", "", $documento);
        $documento = stripslashes($documento);
        //guardo el fichero html
        $filename = "/tmp/$nom.html";
        $documento = '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>' . $documento;

        if (!$handle = fopen($filename, 'wb+')) {
            echo "Cannot open file ($filename)";
            exit;
        }
        // Write $some content to our opened file.
        if (fwrite($handle, $documento) === FALSE) {
            echo "Cannot write to file ($filename)";
            exit;
        }
        fclose($handle);
        // lo convierto a zip
        $content_xml = shell_exec("zip -j \"/tmp/$nom.zip\" \"$filename\"");

        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"$nom.zip\"");
        //echo "<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
        echo file_get_contents("/tmp/$nom.zip");
        // elimino el fichero
        unlink("/tmp/$nom.zip");
        break;
    case "odfc":
        $return_var = 0;
        if ($_POST['frm_export_titulo']) {
            $nom = $_POST['frm_export_titulo'];
        } else {
            $nom = "export" . uniqid('', true); // per evitar emoblics si accedeixen varies persones a l'hora.
        }
        $documento = $_POST['frm_export_ex'];
        fixAmps($documento, 0);
        $doc_type = "spreadsheet";
        require_once("odf.php");

        $documento = html_entity_decode($documento, ENT_NOQUOTES, 'UTF-8');
        $documento = stripslashes($documento);
        $documento = '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>' . $documento;
        //quitar los forms
        $documento = preg_replace('/<form.*>/', '', $documento);
        $documento = str_replace('/<\/form>/', '', $documento);

        $file_txt = "/tmp/$nom.txt";
        $file_xml = "/tmp/$nom.xml";

        if (!$handle = fopen($file_txt, 'wb+')) {
            echo "Cannot open file ($file_txt)";
            exit;
        }
        // Write $somecontent to our opened file.
        if (fwrite($handle, $documento) === FALSE) {
            echo "Cannot write to file ($file_txt)";
            exit;
        }
        fclose($handle);
        $conv_style = "1";

        $cmd = "xsltproc --html " . getcwd() . "/ODF/xslt/html2odfcalc.xslt $file_txt > $file_xml";
        $a_output = array();
        exec($cmd, $a_output, $return_var);
        if ($return_var != 0) {
            echo "cmd: $cmd <br>error: $return_var<br>";
            print_r($a_output);
            exit();
        }
        $content_xml = file_get_contents($file_xml);

        $object = newOds(); //create a new ods file
        $file_ods = "/tmp/$nom.ods";
        saveOds($object, $file_ods, $content_xml, $conv_style, $doc_type); //save the object to a ods file
        if (file_exists($file_ods)) {
            $file_size = (int)filesize($file_ods);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
            header("Content-Disposition: attachment; filename=\"$file_ods\"");
            header("Content-Length: $file_size");
            header('Content-Description: File Transfer');
            header("Content-Transfer-Encoding: binary ");
            //in case of more output buffers was opened.
            while (ob_get_level()) {
                ob_end_clean();
            }
            flush();
            readfile($file_ods);
            unlink($file_ods);
        }
        unlink($file_txt);
        unlink($file_xml);
        break;
    case "odft":
        if ($_POST['frm_export_titulo']) {
            $nom = $_POST['frm_export_titulo'];
        } else {
            $nom = "export" . uniqid('', true); // per evitar emoblics si accedeixen varies persones a l'hora.
        }
        $documento = $_POST['frm_export_ex'];
        fixAmps($documento, 0);
        $doc_type = "text";
        require_once("odf.php");

        $documento = html_entity_decode($documento, ENT_NOQUOTES, 'UTF-8');
        $documento = stripslashes($documento);
        $documento = '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>' . $documento;
        //quitar los forms
        $documento = preg_replace('/<form.*>/', '', $documento);
        $documento = str_replace('/<\/form>/', '', $documento);

        $file_txt = "/tmp/$nom.txt";
        $file_xml = "/tmp/$nom.xml";

        if (!$handle = fopen($file_txt, 'wb+')) {
            echo "Cannot open file ($file_txt)";
            exit;
        }
        // Write $some content to our opened file.
        if (fwrite($handle, $documento) === FALSE) {
            echo "Cannot write to file ($file_txt)";
            exit;
        }
        fclose($handle);

        if ($_POST['frm_export_ref']) {
            $conv_ref = $_POST['frm_export_ref'];
            switch ($conv_ref) {
                case "encargossacd/view/lista_com_ctr.phtml":
                    $xslt = "html2ootext_list_com_ctr.xslt";
                    $conv_style = "3";
                    break;
                case "encargossacd/view/lista_com_sacd.phtml":
                    $xslt = "html2ootext_list_com_sacd.xslt";
                    $conv_style = "3";
                    break;
                case "actividadessacd/view/com_sacd_activ_print.phtml":
                    $xslt = "html2ootext_com_sacd.xslt";
                    $conv_style = "2";
                    break;
                default:
                    $xslt = "html2odftext.xslt";
                    $conv_style = "1";
            }
        } else {
            $xslt = "html2odftext.xslt";
            $conv_style = "1";
        }

        $cmd = "xsltproc --html " . getcwd() . "/ODF/xslt/$xslt $file_txt > $file_xml";
        $a_output = array();
        exec($cmd, $a_output, $return_var);
        if ($return_var != 0) {
            echo "cmd: $cmd <br>error: $return_var<br>";
            print_r($a_output);
            exit();
        }
        $content_xml = file_get_contents($file_xml);

        $object = newOds(); //create a new ods file
        $file_odt = "/tmp/$nom.odt";
        saveOds($object, $file_odt, $content_xml, $conv_style, $doc_type); //save the object to a ods file
        if (file_exists($file_odt)) {
            $file_size = (int)filesize($file_odt);
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            header('Content-type: application/vnd.oasis.opendocument.text');
            header("Content-Disposition: attachment; filename=\"$file_odt\"");
            header("Content-Length: $file_size");
            header('Content-Description: File Transfer');
            header("Content-Transfer-Encoding: binary ");
            //in case of more output buffers was opened.
            while (ob_get_level()) {
                ob_end_clean();
            }
            flush();
            readfile($file_odt);
            unlink($file_odt);
        }
        unlink($file_txt);
        unlink($file_xml);
        break;
}
