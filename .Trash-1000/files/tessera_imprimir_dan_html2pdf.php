<?php
/**
 * HTML2PDF Librairy - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author      Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */
/*
    // for display the post information
    if (isset($_POST['test'])) {
        echo '<pre>';
        echo htmlentities(print_r($_POST, true));
        echo '</pre>';
        exit;
    }
*/

    // get the HTML
    ob_start();
    include(dirname(__FILE__).'/tessera_imprimir_pdf.php');
    $content = ob_get_clean();

/*
    $file = dirname(__FILE__).'/test_pdf.php';
	file_put_contents($file, $content);
	
	$filename = (dirname(__FILE__).'/test.php');
    $content =file_get_contents($filename);
*/	
    // convert to PDF
    require_once(dirname(__FILE__).'/../../../libs/html2pdf/html2pdf.class.php');
	//$html2pdf = new HTML2PDF('P', 'A4', 'es');
	$html2pdf = new HTML2PDF('P','A4','es', false, 'UTF-8', array('1cm','1cm','1cm','1cm')); 
	$html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->writeHTML($content);
	$html2pdf->Output('forms.pdf');

/*
catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }




$html = 'test';

require 'class/html2pdf/html2pdf.class.php';
$html2pdf = new HTML2PDF('P', 'A4', 'fr');
$html2pdf->writeHTML($html);

// generar fichero pdf y guardar fichero, asegúrate que la carpeta tiene permisos de escritura
$output_file = 'facturas/1.pdf';
$html2pdf->Output($output_file, 'F');

// generar fichero pdf sin guardar
$html2pdf->Output('factura.pdf');
*/
