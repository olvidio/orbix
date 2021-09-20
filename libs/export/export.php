<?php
/**
 * Funció per eliminar els & que no són html
 * 
 * @param type $html
 * @param type $offset
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

// --------------------- source_rtf.php ---------------------
// Measurements
// 1 inch = 1440 twips
// 1 cm = 567 twips
// 1 mm = 56.7 twips
$inch = 1440;
$cm = 567;
$mm = 56.7;

// Fonts
$fonts_array = array();
// Array structure - array(
//	"name"		=>	Name given to the font,
//	"family"	=>	[nil, roman, swiss, modern, script, decor, tech, bidi],
//	"charset"	=>	0
// );

$fonts_array[] = array(
	"name"		=>	"Arial",
	"family"	=>	"swiss",
	"charset"	=>	0
);

$fonts_array[] = array(
	"name"		=>	"Times New Roman",
	"family"	=>	"roman",
	"charset"	=>	0
);

$fonts_array[] = array(
	"name"		=>	"Verdana",
	"family"	=>	"swiss",
	"charset"	=>	0
);

$fonts_array[] = array(
	"name"		=>	"Symbol",
	"family"	=>	"roman",
	"charset"	=>	2
);

// Control Words
$control_array = array();
// FIN --------------------- source_rtf.php ---------------------

// RTF Generator Class
//
// Example of use:
// 	$rtf = new rtf("rtf_config.php");
// 	$rtf->setPaperSize(5);
// 	$rtf->setPaperOrientation(1);
// 	$rtf->setDefaultFontFace(0);
// 	$rtf->setDefaultFontSize(24);
// 	$rtf->setAuthor("noginn");
// 	$rtf->setOperator("me@noginn.com");
// 	$rtf->setTitle("RTF Document");
// 	$rtf->addColour("#000000");
// 	$rtf->addText($_POST['text']);
// 	$rtf->getDocument();
//


class rtf {
	var $colour_table = array();
	var $colour_rgb;
	var $font_table = array();
	var $font_face='';
	var $font_size=12;
	var $info_table = array();
	var $page_width;
	var $page_height;
	var $page_size=5;
	var $page_orientation=1;
	var $rtf_version=1;
	var $tab_width=8;
	
	var $document;
	var $buffer;
	
	function __construct($font_face='',$font_size=12,$paper_size=5,$paper_orientation=1,$rtf_version=0,$tab_width=8) {
		$this->setDefaultFontFace($font_face);
		$this->setDefaultFontSize($font_size);
		$this->setPaperSize($paper_size);
		$this->setPaperOrientation($paper_orientation);
		$this->rtf_version = $rtf_version;
		$this->tab_width = $tab_width;
	}
	
	function setDefaultFontFace($face) {
		$this->font_face = $face; // $font is interger
	}
	
	function setDefaultFontSize($size) {
		$this->font_size = $size;
	}
	
	function setTitle($title="") {
		$this->info_table["title"] = $title;
	}
	
	function setAuthor($author="") {
		$this->info_table["author"] = $author;
	}
	
	function setOperator($operator="") {
		$this->info_table["operator"] = $operator;
	}
	
	function setPaperSize($size=0) {
		global $inch, $cm, $mm;
		
		// 1 => Letter (8.5 x 11 inch)
		// 2 => Legal (8.5 x 14 inch)
		// 3 => Executive (7.25 x 10.5 inch)
		// 4 => A3 (297 x 420 mm)
		// 5 => A4 (210 x 297 mm)
		// 6 => A5 (148 x 210 mm)
		// Orientation considered as Portrait
		
		switch($size) {
			case 1:
				$this->page_width = floor(8.5*$inch);
				$this->page_height = floor(11*$inch);
				$this->page_size = 1;
				break;	
			case 2:
				$this->page_width = floor(8.5*$inch);
				$this->page_height = floor(14*$inch);
				$this->page_size = 5;
				break;	
			case 3:
				$this->page_width = floor(7.25*$inch);
				$this->page_height = floor(10.5*$inch);
				$this->page_size = 7;
				break;	
			case 4:
				$this->page_width = floor(297*$mm);
				$this->page_height = floor(420*$mm);
				$this->page_size = 8;
				break;	
			case 5:
			default:
				$this->page_width = floor(210*$mm);
				$this->page_height = floor(297*$mm);
				$this->page_size = 9;
				break;	
			case 6:
				$this->page_width = floor(148*$mm);
				$this->page_height = floor(210*$mm);
				$this->page_size = 10;
				break;	
		}
	}
	
	function setPaperOrientation($orientation=0) {
		// 1 => Portrait
		// 2 => Landscape
		
		switch($orientation) {
			case 1:
			default:
				$this->page_orientation = 1;
				break;
			case 2:
				$this->page_orientation = 2;
				break;	
		}
	}
	function setPaperMargin($top=1.5,$rigth=1.5,$left=1.5,$bottom=1.5) {
		global $cm;

		$this->top=$top*$cm;
		$this->rigth=$rigth*$cm;
		$this->left=$left*$cm;
		$this->bottom=$bottom*$cm;
		
	}
	
	function setTabWidth($tab_width=2) {
		global $cm;

		$this->tab_width=$tab_width*$cm;
	}
	
	function addColour($hexcode) {
		// Get the RGB values
		$this->hex2rgb($hexcode);
		
		// Register in the colour table array
		$this->colour_table[] = array(
			"red"	=>	$this->colour_rgb["red"],
			"green"	=>	$this->colour_rgb["green"],
			"blue"	=>	$this->colour_rgb["blue"]
		);
	}
	
	// Convert HEX to RGB (#FFFFFF => r255 g255 b255)
	function hex2rgb($hexcode) {
		$hexcode = str_replace("#", "", $hexcode); 
		$rgb = array();
		$rgb["red"] = hexdec(substr($hexcode, 0, 2));
		$rgb["green"] = hexdec(substr($hexcode, 2, 2));
		$rgb["blue"] = hexdec(substr($hexcode, 4, 2));
		
		$this->colour_rgb = $rgb;
	}
	
	// Convert newlines into \par
	function nl2par($text) {
		$text = str_replace("\n", "\\par ", $text);
		
		return $text;
	}
	
	// Add a text string to the document buffer
	function addText($text) {
		$text = str_replace("\n", "", $text);
		$text = str_replace("\t", "", $text);
		$text = str_replace("\r", "", $text);
		
		$this->document .= $text;
	}
	
	// Ouput the RTF file
	function getDocument() {
		global $nom;
		$this->buffer .= "{";
		// Header
		$this->buffer .= $this->getHeader();
		// Font table
		$this->buffer .= $this->getFontTable();
		// Colour table
		$this->buffer .= $this->getColourTable();
		// File Information
		$this->buffer .= $this->getInformation();
		// Default font values
		$this->buffer .= $this->getDefaultFont();
		// Page display settings
		$this->buffer .= $this->getPageSettings();
		// Parse the text into RTF
		$this->buffer .= $this->parseDocument();
		$this->buffer .= "}";
		
		header("Content-Type: text/enriched\n");
		header("Content-Disposition: attachment; filename=$nom");
		echo $this->buffer;
	}
	
	// Header
	function getHeader() {
		$header_buffer = "\\rtf{$this->rtf_version}\\ansi\\deff0\\deftab{$this->tab_width}\n\n";
		//$header_buffer = "\\rtf{$this->rtf_version}\\uN\\deff0\\deftab{$this->tab_width}\n\n";
		
		return $header_buffer;
	}
	
	// Font table
	function getFontTable() {
		global $fonts_array;
		
		$font_buffer = "{\\fonttbl\n";
		foreach($fonts_array AS $fnum => $farray) {
			$font_buffer .= "{\\f{$fnum}\\f{$farray['family']}\\fcharset{$farray['charset']} {$farray['name']};}\n";
		}
		$font_buffer .= "}\n\n";
		
		return $font_buffer;
	}
	
	// Colour table
	function getColourTable() {
		$colour_buffer = "";
		if(sizeof($this->colour_table) > 0) {
			$colour_buffer = "{\\colortbl;\n";
			foreach($this->colour_table AS $cnum => $carray) {
				$colour_buffer .= "\\red{$carray['red']}\\green{$carray['green']}\\blue{$carray['blue']};\n";	
			}
			$colour_buffer .= "}\n\n";
		}
		
		return $colour_buffer;
	}
	
	// Information
	function getInformation() {
		$info_buffer = "";
		if(sizeof($this->info_table) > 0) {
			$info_buffer = "{\\info\n";
			foreach($this->info_table AS $name => $value) {
				$info_buffer .= "{\\{$name} {$value}}";
			}
			$info_buffer .= "}\n\n";
		}
		
		return $info_buffer;
	}
	
	// Default font settings
	function getDefaultFont() {
		$font_buffer = "\\f{$this->font_face}\\fs{$this->font_size}\n";
		
		return $font_buffer;
	}
	
	// Page display settings
	function getPageSettings() {
		if($this->page_orientation == 1)
			$page_buffer = "\\paperw{$this->page_width}\\paperh{$this->page_height}\n";
		else
			$page_buffer = "\\paperw{$this->page_height}\\paperh{$this->page_width}\\landscape\n";
			
		$page_buffer .= "\\margtsxn{$this->top}\\margbsxn{$this->bottom}\\marglsxn{$this->left}\\margrsxn{$this->rigth}\n";
		$page_buffer .= "\\pgncont\\pgndec\\pgnstarts1\\pgnrestart\n";
		
		return $page_buffer;
	}
	
	// Convert special characters to ASCII
	function specialCharacters($text) {
		$text_buffer = "";
		for($i = 0; $i < strlen($text); $i++)
			$text_buffer .= $this->escapeCharacter($text[$i]);
		
		return $text_buffer;
	}
	
	// Convert special characters to ASCII
	function escapeCharacter($character) {
		$escaped = "";
		if(ord($character) >= 0x00 && ord($character) < 0x20)
			$escaped = "\\'".dechex(ord($character));
		
		if ((ord($character) >= 0x20 && ord($character) < 0x80) || ord($character) == 0x09 || ord($character) == 0x0A)
			$escaped = $character;
		
		if (ord($character) >= 0x80 and ord($character) < 0xFF)
			$escaped = "\\'".dechex(ord($character));

		switch(ord($character)) {
			case 0x5C:
			case 0x7B:
			case 0x7D:
				$escaped = "\\".$character;
				break;
		}
		
		return $escaped;
	}
	
	// Parse the text input to RTF
	function parseDocument() {
		//$doc_buffer = $this->specialCharacters($this->document);
		$doc_buffer = $this->document;
		
		$doc_buffer = neteja($doc_buffer);
		
		if(preg_match("/<ul>(.*?)<\/UL>/mi", $doc_buffer)) {
			$doc_buffer = str_replace("<ul>", "", $doc_buffer);
			$doc_buffer = str_replace("</ul>", "", $doc_buffer);
			$doc_buffer = preg_replace("/<li>(.*?)<\/LI>/mi", "\\f3\\'B7\\tab\\f{$this->font_face} \\1\\par", $doc_buffer);
		}
		
		$doc_buffer = preg_replace("/<p>(.*?)<\/p>/mi", "\\1\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<strong>(.*?)<\/strong>/mi", "\\b \\1\\b0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<em>(.*?)<\/em>/mi", "\\i \\1\\i0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<u>(.*?)<\/u>/mi", "\\ul \\1\\ul0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<strike>(.*?)<\/strike>/mi", "\\strike \\1\\strike0 ", $doc_buffer);
		$doc_buffer = preg_replace("/<sub>(.*?)<\/sub>/mi", "{\\sub \\1}", $doc_buffer);
		$doc_buffer = preg_replace("/<sup>(.*?)<\/sup>/mi", "{\\super \\1}", $doc_buffer);
		
		//$doc_buffer = preg_replace("/<h1>(.*?)<\/H1>/mi", "\\pard\\qc\\fs40 \\1\\par\\pard\\fs{$this->font_size} ", $doc_buffer);
		//$doc_buffer = preg_replace("/<h2>(.*?)<\/H2>/mi", "\\pard\\qc\\fs32 \\1\\par\\pard\\fs{$this->font_size} ", $doc_buffer);
		
		$doc_buffer = preg_replace("/<h1>(.*?)<\/H1>/mi", "\\fs48\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<h2>(.*?)<\/H2>/mi", "\\fs36\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
		$doc_buffer = preg_replace("/<h3>(.*?)<\/H3>/mi", "\\fs27\\b \\1\\b0\\fs{$this->font_size}\\par ", $doc_buffer);
		
		
		$doc_buffer = preg_replace("/<hr(.*?)>/i", "\\brdrb\\brdrs\\brdrw30\\brsp20 \\pard\\par ", $doc_buffer);
		$doc_buffer = str_replace("<br>", "\\par ", $doc_buffer);
		$doc_buffer = str_replace("<tab>", "\\tab ", $doc_buffer);
		
	
		$doc_buffer=strip_tags($doc_buffer);
		$doc_buffer = $this->nl2par($doc_buffer);
		
		return $doc_buffer;
	}
}
// FIN ----------------------- class_rtf.php ----------------------------------------------------


// ---------------- més coses -----------------
function neteja($txt){
	$txt=stripslashes($txt);		
	// eliminar style
	$pos1 = strpos($txt, '<style>');
	$pos2 = strpos($txt, '</style>');
	$len=$pos2-$pos1+strlen('</style>');
	//$txt=substr($txt,$pos1,$len); 
	if ($pos1) {	$txt=substr_replace($txt, '', $pos1, $len); }
	// introducir tab
	$txt=str_replace('</th>',"</th>\t",$txt);
	$txt=str_replace('</td>',"</td>\t",$txt);
	$txt=str_replace('</tr>',"</tr>\n",$txt);
	$txt=str_replace("</th>\t</tr>","</th></tr>",$txt);
	$txt=str_replace("</td>\t</tr>","</td></tr>",$txt);
	// eliminar tags
	//$txt=strip_tags($txt);
	// remplazar posibles caracteres tipicos html
	$txt=htmlspecialchars_decode($txt);
	$txt=str_replace('&nbsp;','',$txt);
	
	//return "dani: p1=$pos1, p2=$pos2, len=$len \n".$txt;
	// passar a latin-1
	$txt=iconv("utf-8","iso-8859-1//IGNORE",$txt);
	return $txt;
}
function quitar_doble_espacio($txt){
	$len1=strlen($txt);
	/*
	if ($n==1) {
		$espacios = array(" ", "\t", "\n", "\r", "\0", "\x0B");
		$txt = str_replace($espacios, " ", $txt);
	}
	*/
	$txt = str_replace("  ", " ", $txt); //cambio dos espacios por uno.
	/*
	$txt = str_replace("\t\t", "\t", $txt); 
	$txt = str_replace("\r\r", "\r", $txt); 
	$txt = str_replace("\r\n", "\n", $txt); 
	$txt = str_replace("\n\n", "\n", $txt); 
	*/
	$len2=strlen($txt);
	//echo "len1: $len1, len2: $len2<br>";
	if ($len2 != $len1) {
		quitar_doble_espacio($txt);
	} else {
		return $txt;
	}
}

switch ($_POST['frm_export_tipo']) {
	case "html":
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export";
		}
		// quito las imágenes
		$documento=$_POST['frm_export_ex'];
		$documento= preg_replace("/<img(.*?)>/mi", "", $documento);
		$documento=stripslashes($documento);		
		@header("Content-type: application/octet-stream");
		@header("Content-Disposition: attachment; filename=\"$nom.html\"");
		echo "<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
		echo $documento;
	break;
	case "html_zip": // igual que el anterior más comprimir
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="planning";
		}
		// quito las imágenes
		$documento=$_POST['frm_export_ex'];
		$documento= preg_replace("/<img(.*?)>/mi", "", $documento);
		$documento=stripslashes($documento);		
		//guardo el fichreo html
		$filename = "/tmp/$nom.html";
		$documento= '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>'. $documento;

		if (!$handle = fopen($filename, 'w+')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		// Write $somecontent to our opened file.
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
	case "pdf":
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export".uniqid(); // per evitar emoblics si accedeixen varies persones a l'hora.
		}
		$documento=$_POST['frm_export_ex'];
		fixAmps($documento, 0);
		$html=preg_replace('/web-width/', 'width', $documento); 
		if ($_POST['frm_export_orientation']=='v') {
			$orientation='P';
		} else {
			$orientation='L';
		}
			$orientation='';
		/**
		 * Creates an example PDF TEST document using TCPDF   example_061
		 * @package com.tecnick.tcpdf
		 * @abstract TCPDF - Example: XHTML + CSS
		 * @author Nicola Asuni
		 * @since 2010-05-25
		 */
		//require_once('tcpdf/config/lang/cat.php');
		//require_once('tcpdf/tcpdf.php');

		// create new PDF document
		//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 061');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('helvetica', '', 10);

		// add a page
		$pdf->AddPage();

		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');

		// reset pointer to the last page
		$pdf->lastPage();

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output("$nom.pdf", 'I');
	break;
	case "excel":
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export";
		}
		$documento=$_POST['frm_export_ex'];
		fixAmps($documento, 0);
		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=\"$nom.xls\"");
		// quito las imágenes
		$documento= preg_replace("/<img(.*?)>/mi", "", $documento);
		$documento=stripslashes($documento);		
		$htmltodoc= new HTML_TO_DOC();
		$htmltodoc->createDoc("$documento","$nom.xls",'true');
	break;
	case "rtf":
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export";
		}
		$documento=$_POST['frm_export_ex'];
		fixAmps($documento, 0);
		//echo "dd: $frm_export_ex<br>";
		header('Content-type: application/vnd.ms-word');
		header("Content-Disposition: attachment; filename=\"$nom.rtf\"");
		$rtf = new rtf();
		$rtf->setPaperSize(5);
		$rtf->setTabWidth(2); // en cm.
		$rtf->setPaperOrientation(2); //horizontal
		$rtf->setPaperMargin(1,1,1,1); // (en cm) top,rigth,left,bottom
		$rtf->setDefaultFontFace(0);
		$rtf->setDefaultFontSize(24);
		$rtf->setAuthor("noginn");
		$rtf->setOperator("me@noginn.com");
		$rtf->setTitle("RTF Document");
		$rtf->addColour("#000000");
		$rtf->addText($documento);
		$rtf->getDocument();
	break;
	case "odfc":
	    $return_var = 0;
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export".uniqid(); // per evitar emoblics si accedeixen varies persones a l'hora.
		}
		$documento=$_POST['frm_export_ex'];
		fixAmps($documento, 0);
		echo "<pre>";
		$doc_type="spreadsheet";
		require_once("odf.php");
		
		$documento=html_entity_decode($documento,ENT_NOQUOTES,'UTF-8');		
		$documento=stripslashes($documento);		
		$documento= '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>'. $documento;
		//quitar los forms 
		$documento=preg_replace('/<form.*>/', '', $documento); 
		$documento=preg_replace('/<\/form>/', '', $documento); 
		
		$file_txt = "/tmp/$nom.txt";
		$file_xml = "/tmp/$nom.xml";
		
		if (!$handle = fopen($file_txt, 'w+')) {
			 echo "Cannot open file ($file_txt)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, $documento) === FALSE) {
			echo "Cannot write to file ($file_txt)";
			exit;
		}
		fclose($handle);
		$conv_style="1";
		
		$cmd = "xsltproc --html ".getcwd()."/ODF/xslt/html2odfcalc.xslt $file_txt > $file_xml";
		$a_output =array();
		exec($cmd, $a_output, $return_var);
		if ($return_var != 0) {
			echo "cmd: $cmd <br>error: $return_var<br>";
			print_r($a_output);
			exit();
		}
		$content_xml = file_get_contents($file_xml);
		
		$object = newOds(); //create a new ods file
		$file_ods="/tmp/$nom.ods";
		saveOds($object,$file_ods,$content_xml,$conv_style,$doc_type); //save the object to a ods file
		if (file_exists($file_ods)) {
			$file_size = (int) filesize($file_ods);
			header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
			header("Content-Disposition: attachment; filename=\"$file_ods\"");
			header("Content-Length: $file_size");
			header('Content-Description: File Transfer');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($file_ods);
			unlink($file_ods);
		}
		unlink($file_txt);
		unlink($file_xml);
		break;
	case "odft":
		if ($_POST['frm_export_titulo']) {
			$nom=$_POST['frm_export_titulo'];
		} else {
			$nom="export".uniqid(); // per evitar emoblics si accedeixen varies persones a l'hora.
		}
		$documento=$_POST['frm_export_ex'];
		//echo "$documento";
		fixAmps($documento, 0);
		$doc_type="text";
		require_once("odf.php");
		
		$documento=html_entity_decode($documento,ENT_NOQUOTES,'UTF-8');		
		$documento=stripslashes($documento);		
		$documento= '<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>'. $documento;
		//quitar los forms 
		$documento=preg_replace('/<form.*>/', '', $documento); 
		$documento=preg_replace('/<\/form>/', '', $documento); 

		$file_txt = "/tmp/$nom.txt";
		$file_xml = "/tmp/$nom.xml";

		if (!$handle = fopen($file_txt, 'w+')) {
			 echo "Cannot open file ($file_txt)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, $documento) === FALSE) {
			echo "Cannot write to file ($file_txt)";
			exit;
		}
		fclose($handle);
		
		if ($_POST['frm_export_ref']) {
			$conv_ref=$_POST['frm_export_ref'];
			switch ($conv_ref) {
				case "des/tareas/list_com_ctr.php":
					$xslt="html2ootext_list_com_ctr.xslt";
					$conv_style="3";
					break;
				case "encargossacd/view/lista_com_sacd.phtml":
					$xslt="html2ootext_list_com_sacd.xslt";
					$conv_style="3";
					break;
				case "actividadessacd/view/com_sacd_activ_print.phtml":
					$xslt="html2ootext_com_sacd.xslt";
					$conv_style="2";
					break;
				default:
					$xslt="html2odftext.xslt";
					$conv_style="1";
			}
		} else {
			$xslt="html2odftext.xslt";
			$conv_style="1";
		}
		
		$cmd = "xsltproc --html ".getcwd()."/ODF/xslt/$xslt $file_txt > $file_xml";
		$a_output =array();
		exec($cmd, $a_output, $return_var);
		if ($return_var != 0) {
			echo "cmd: $cmd <br>error: $return_var<br>";
			print_r($a_output);
			exit();
		}
		$content_xml = file_get_contents($file_xml);
		
		$object = newOds(); //create a new ods file
		$file_odt="/tmp/$nom.odt";
		saveOds($object,$file_odt,$content_xml,$conv_style,$doc_type); //save the object to a ods file
		if (file_exists($file_odt)) {
			$file_size = (int) filesize($file_odt);
			header('Content-type: application/vnd.oasis.opendocument.text');
			header("Content-Disposition: attachment; filename=\"$file_odt\"");
			header("Content-Length: $file_size");
			header('Content-Description: File Transfer');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($file_odt);
			unlink($file_odt);
		}
		unlink($file_txt);
		unlink($file_xml);
		break;
}
?>
