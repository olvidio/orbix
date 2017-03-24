<?php
/*

odf-php a library to read and write ods files from php.

This library has been forked from eyeOS project and licended under the LGPL3
terms available at: http://www.gnu.org/licenses/lgpl-3.0.txt (relicenced
with permission of the copyright holders)

Copyright: Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008 

https://sourceforge.net/projects/ods-php/

*/
// INICIO Cabecera global de URL de controlador *********************************
	//require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	//require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

class odf {
	var $fonts;
	var $styles;
	var $sheets;
	var $lastElement;
	var $fods;
	var $currentSheet;
	var $currentRow;
	var $currentCell;
	var $lastRowAtt;
	var $repeat;
	
	function __construct() {
		$this->styles = array();
		$this->fonts = array();
		$this->sheets = array();
		$this->currentRow = 0;
		$this->currentSheet = 0;
		$this->currentCell = 0;
		$this->repeat = 0;
	}
	
	function parse($data) {
		$xml_parser = xml_parser_create(); 
		xml_set_object ( $xml_parser, $this );
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

		xml_parse($xml_parser, $data, strlen($data));

		xml_parser_free($xml_parser);
	}
	
	
	function startElement($parser, $tagName, $attrs) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'style:font-face') {
			//$this->fonts[$attrs['STYLE:NAME']] = $attrs;
		} elseif($cTagName == 'style:style') {
			//$this->lastElement = $attrs['STYLE:NAME'];
			$this->styles[$this->lastElement]['attrs'] = $attrs;
		} elseif($cTagName == 'style:table-column-properties' || $cTagName == 'style:table-row-properties' 
			|| $cTagName == 'style:table-properties' || $cTagName == 'style:text-properties') {
			$this->styles[$this->lastElement]['styles'][$cTagName] = $attrs;
		} elseif($cTagName == 'table:table-cell') {
			$this->lastElement = $cTagName;
			$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['attrs'] = $attrs;
			if(isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED'])) {
				$times = intval($attrs['TABLE:NUMBER-COLUMNS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->currentCell+$i;
					$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['attrs'] = $attrs;
				}
				$this->currentCell += $times;
				$this->repeat = $times;
			}
			if(isset($this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED'])) {
				$times = intval($this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED']);
				$times--;
				for($i=1;$i<=$times;$i++) {
					$cnum = $this->currentRow+$i;
					$this->sheets[$this->currentSheet]['rows'][$cnum][$i-1]['attrs'] = $attrs;
				}
				$this->currentRow += $times;
			}
		} elseif($cTagName == 'table:table-row') {
			$this->lastRowAtt = $attrs;
		}
	}
	
	function endElement($parser, $tagName) {
		$cTagName = strtolower($tagName);
		if($cTagName == 'table:table') {
			$this->currentSheet++;
			$this->currentRow = 0;
		} elseif($cTagName == 'table:table-row') {
			$this->currentRow++;
			$this->currentCell = 0;
		} elseif($cTagName == 'table:table-cell') {
			$this->currentCell++;
			$this->repeat = 0;
		}
	}
	
	function characterData($parser, $data) {
		if($this->lastElement == 'table:table-cell') {
			$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['value'] = $data;
			if($this->repeat > 0) {
				for($i=0;$i<$this->repeat;$i++) {
					$cnum = $this->currentCell - ($i+1);
					$this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['value'] = $data;
				}
			}
		}
	}
	
	function getMeta($lang) {
		$myDate = date('Y-m-j\TH:i:s');
		$meta = '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
			<office:meta>
				<meta:generator>odf-php</meta:generator>
				<meta:creation-date>'.$myDate.'</meta:creation-date>
				<dc:date>'.$myDate.'</dc:date>
				<dc:language>'.$lang.'</dc:language>
				<meta:editing-cycles>2</meta:editing-cycles>
				<meta:editing-duration>PT15S</meta:editing-duration>
				<meta:user-defined meta:name="Info 1"/>
				<meta:user-defined meta:name="Info 2"/>
				<meta:user-defined meta:name="Info 3"/>
				<meta:user-defined meta:name="Info 4"/>
			</office:meta>
		</office:document-meta>';
		return $meta;
	}
	
	function getStyle($estilo_propio) {
		if ($estilo_propio) {
			if ($rta=file_get_contents($estilo_propio)){
				return $rta;
			} 
		} 
	}
	
	function getSettings($settings_propio) {
		if ($settings_propio) {
			if ($rta=file_get_contents($settings_propio)) {
				return $rta; 
			}
		}
	}
	
	function getManifest($doc_type) {
		switch ($doc_type){
			case "text":
				$rta_1='<manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.text" manifest:full-path="/"/>';
				break;
			case "spreadsheet":
				$rta_1='<manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>';
				break;
		}
		$rta= '<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">';
		$rta.=$rta_1;
 $rta.='<manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/statusbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/accelerator/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/floater/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/popupmenu/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/progressbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/menubar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/toolbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/Bitmaps/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/"/>
 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
</manifest:manifest>';
		return $rta;
	}
	
	function addCell($sheet,$row,$cell,$value,$type) {
		$this->sheets[$sheet]['rows'][$row][$cell]['attrs'] = array('OFFICE:VALUE-TYPE'=>$type,'OFFICE:VALUE'=>$value);
		$this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
	}
	
	function editCell($sheet,$row,$cell,$value) {
		$this->sheets[$sheet]['rows'][$row][$cell]['attrs']['OFFICE:VALUE'] = $value;
		$this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
	}
}

function parseOds($file) {
	$tmp = get_tmp_dir();
	copy($file,$tmp.'/'.basename($file));
	$path = $tmp.'/'.basename($file);
	$uid = uniqid();
	mkdir($tmp.'/'.$uid);
	shell_exec('unzip '.escapeshellarg($path).' -d '.escapeshellarg($tmp.'/'.$uid));
	$obj = new odf();
	$obj->parse(file_get_contents($tmp.'/'.$uid.'/content.xml'));
	return $obj;
}

function saveOds($obj,$file,$txt_html,$conv_style,$doc_type) {
	$dir_estilos=ConfigGlobal::$dir_estilos;
	if ($conv_style){
		$estilo_propio=$dir_estilos."/ODF/".$conv_style."_styles.xml";
		$settings_propio=$dir_estilos."/ODF/".$conv_style."_settings.xml";
		if(!file_exists($estilo_propio)) $estilo_propio=$dir_estilos."/ODF/styles.xml";
		if(!file_exists($settings_propio)) $settings_propio=$dir_estilos."/ODF/settings.xml";
	} else {
		$estilo_propio=$dir_estilos."/ODF/styles.xml";
		$settings_propio=$dir_estilos."/ODF/settings.xml";
	}
	$charset = ini_get('default_charset');
	ini_set('default_charset', 'UTF-8');

	$zip = new ZipArchive();
	if ($zip->open($file, ZIPARCHIVE::CREATE)!==TRUE) {
		exit("cannot open <$file>\n");
	}
	switch ($doc_type){
		case "text":
			$zip->addFromString('mimetype','application/vnd.oasis.opendocument.text');
			break;
		case "spreadsheet":
			$zip->addFromString('mimetype','application/vnd.oasis.opendocument.spreadsheet');
			break;
	}
	// si uso xsltproc el texto ya tiene las cabeceras.
	$zip->addFromString("content.xml",$txt_html);
	$zip->addFromString("meta.xml",$obj->getMeta('es-ES'));
	$zip->addFromString("styles.xml",$obj->getStyle($estilo_propio));
	$zip->addFromString("settings.xml",$obj->getSettings($settings_propio));
	$zip->addEmptyDir("META-INF");
	$zip->addFromString("META-INF/manifest.xml",$obj->getManifest($doc_type));
	$zip->addEmptyDir("Configurations2");
	$zip->addEmptyDir("Configurations2/acceleator");
	$zip->addEmptyDir("Configurations2/images");
	$zip->addEmptyDir("Configurations2/popupmenu");
	$zip->addEmptyDir("Configurations2/statusbar");
	$zip->addEmptyDir("Configurations2/floater");
	$zip->addEmptyDir("Configurations2/menubar");
	$zip->addEmptyDir("Configurations2/progressbar");
	$zip->addEmptyDir("Configurations2/toolbar");
	$zip->close();
	
	ini_set('default_charset',$charset);
}

function newOds() {
	$content = '<?xml version="1.0" encoding="UTF-8"?>
	<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"><office:scripts/><office:font-face-decls><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style></office:automatic-styles><office:body><office:spreadsheet><table:table table:name="Hoja1" table:style-name="ta1" table:print="false"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Hoja2" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Hoja3" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>';
	$obj = new odf();
	$obj->parse($content);	
	return $obj;
}

function get_tmp_dir() {
	$path = '';		
	if(!function_exists('sys_get_temp_dir')){	    
		$path = try_get_temp_dir();
	}else{				
		$path = sys_get_temp_dir();
		if(is_dir($path)){
			return $path;	
		}else{			
			$path = try_get_temp_dir();
		}
	}
	return $path;
}

function try_get_temp_dir() {	        
    // Try to get from environment variable
	if(!empty($_ENV['TMP'])){
		$path = realpath($_ENV['TMP']);
	}else if(!empty($_ENV['TMPDIR'])){
		$path = realpath( $_ENV['TMPDIR'] );
	}else if(!empty($_ENV['TEMP'])){
		$path = realpath($_ENV['TEMP']);
	}
	// Detect by creating a temporary file
	else{
		// Try to use system's temporary directory
		// as random name shouldn't exist
		$temp_file = tempnam(md5(uniqid(rand(),TRUE)),'');
		if ($temp_file){
			$temp_dir = realpath(dirname($temp_file));
			unlink($temp_file);
			$path = $temp_dir;
		}else{
			return "/tmp";
		}
	}	
	return $path;
}

?>
