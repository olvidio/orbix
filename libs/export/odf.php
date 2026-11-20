<?php
/*

odf-php a library to read and write ods files from php.

This library has been forked from eyeOS project and licended under the LGPL3
terms available at: http://www.gnu.org/licenses/lgpl-3.0.txt (relicenced
with permission of the copyright holders)

Copyright: Juan Lao Tebar (juanlao@eyeos.org) and Jose Carlos Norte (jose@eyeos.org) - 2008 

https://sourceforge.net/projects/ods-php/

*/

class odf
{
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

    function __construct()
    {
        $this->styles = [];
        $this->fonts = array();
        $this->sheets = array();
        $this->currentRow = 0;
        $this->currentSheet = 0;
        $this->currentCell = 0;
        $this->repeat = 0;
    }

    function parse($data)
    {
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "characterData");
        xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

        xml_parse($xml_parser, $data, strlen($data));

        xml_parser_free($xml_parser);
    }


    function startElement($parser, $tagName, $attrs)
    {
        $cTagName = strtolower($tagName?? '');
        if ($cTagName === 'style:font-face') {
            //$this->fonts[$attrs['STYLE:NAME']] = $attrs;
        } elseif ($cTagName === 'style:style') {
            //$this->lastElement = $attrs['STYLE:NAME'];
            $this->styles[$this->lastElement]['attrs'] = $attrs;
        } elseif ($cTagName === 'style:table-column-properties' || $cTagName === 'style:table-row-properties'
            || $cTagName === 'style:table-properties' || $cTagName === 'style:text-properties') {
            $this->styles[$this->lastElement]['styles'][$cTagName] = $attrs;
        } elseif ($cTagName === 'table:table-cell') {
            $this->lastElement = $cTagName;
            $this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['attrs'] = $attrs;
            if (isset($attrs['TABLE:NUMBER-COLUMNS-REPEATED'])) {
                $times = (int)$attrs['TABLE:NUMBER-COLUMNS-REPEATED'];
                $times--;
                for ($i = 1; $i <= $times; $i++) {
                    $cnum = $this->currentCell + $i;
                    $this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['attrs'] = $attrs;
                }
                $this->currentCell += $times;
                $this->repeat = $times;
            }
            if (isset($this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED'])) {
                $times = (int)$this->lastRowAtt['TABLE:NUMBER-ROWS-REPEATED'];
                $times--;
                for ($i = 1; $i <= $times; $i++) {
                    $cnum = $this->currentRow + $i;
                    $this->sheets[$this->currentSheet]['rows'][$cnum][$i - 1]['attrs'] = $attrs;
                }
                $this->currentRow += $times;
            }
        } elseif ($cTagName === 'table:table-row') {
            $this->lastRowAtt = $attrs;
        }
    }

    function endElement($parser, $tagName)
    {
        $cTagName = strtolower($tagName?? '');
        if ($cTagName === 'table:table') {
            $this->currentSheet++;
            $this->currentRow = 0;
        } elseif ($cTagName === 'table:table-row') {
            $this->currentRow++;
            $this->currentCell = 0;
        } elseif ($cTagName === 'table:table-cell') {
            $this->currentCell++;
            $this->repeat = 0;
        }
    }

    function characterData($parser, $data)
    {
        if ($this->lastElement === 'table:table-cell') {
            $this->sheets[$this->currentSheet]['rows'][$this->currentRow][$this->currentCell]['value'] = $data;
            if ($this->repeat > 0) {
                for ($i = 0; $i < $this->repeat; $i++) {
                    $cnum = $this->currentCell - ($i + 1);
                    $this->sheets[$this->currentSheet]['rows'][$this->currentRow][$cnum]['value'] = $data;
                }
            }
        }
    }

    function getMeta($lang)
    {
        $myDate = date('Y-m-j\TH:i:s');
        $meta = '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-meta xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:ooo="http://openoffice.org/2004/office" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" office:version="1.2">
			<office:meta>
				<meta:generator>odf-php</meta:generator>
				<meta:creation-date>' . $myDate . '</meta:creation-date>
				<dc:date>' . $myDate . '</dc:date>
				<dc:language>' . $lang . '</dc:language>
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

    function getStyle($estilo_propio)
    {
        if ($estilo_propio) {
            if ($rta = file_get_contents($estilo_propio)) {
                return $rta;
            }
        }
    }

    function getSettings($settings_propio)
    {
        if ($settings_propio) {
            if ($rta = file_get_contents($settings_propio)) {
                return $rta;
            }
        }
    }

    function getManifest($doc_type)
    {
        switch ($doc_type) {
            case "text":
                $rta_1 = '<manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="application/vnd.oasis.opendocument.text"/>';
                break;
            case "spreadsheet":
                $rta_1 = '<manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="application/vnd.oasis.opendocument.spreadsheet"/>';
                break;
        }
        $rta = '<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0">';
        $rta .= $rta_1;
        $rta .= ' <manifest:file-entry manifest:full-path="Configurations2/" manifest:media-type="application/vnd.sun.xml.ui.configuration"/>
 <manifest:file-entry manifest:full-path="manifest.rdf" manifest:media-type="application/rdf+xml"/>
 <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="settings.xml" manifest:media-type="text/xml"/>
 <manifest:file-entry manifest:full-path="Thumbnails/thumbnail.png" manifest:media-type="image/png"/>
</manifest:manifest>';

        return $rta;
    }

    public function getManifestRdf()
    {
        $rta = '<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
  <rdf:Description rdf:about="styles.xml">
    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/odf#StylesFile"/>
  </rdf:Description>
  <rdf:Description rdf:about="">
    <ns0:hasPart xmlns:ns0="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#" rdf:resource="styles.xml"/>
  </rdf:Description>
  <rdf:Description rdf:about="content.xml">
    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/odf#ContentFile"/>
  </rdf:Description>
  <rdf:Description rdf:about="">
    <ns0:hasPart xmlns:ns0="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#" rdf:resource="content.xml"/>
  </rdf:Description>
  <rdf:Description rdf:about="">
    <rdf:type rdf:resource="http://docs.oasis-open.org/ns/office/1.2/meta/pkg#Document"/>
  </rdf:Description>
</rdf:RDF>';
        return $rta;
    }

    function addCell($sheet, $row, $cell, $value, $type)
    {
        $this->sheets[$sheet]['rows'][$row][$cell]['attrs'] = array('OFFICE:VALUE-TYPE' => $type, 'OFFICE:VALUE' => $value);
        $this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
    }

    function editCell($sheet, $row, $cell, $value)
    {
        $this->sheets[$sheet]['rows'][$row][$cell]['attrs']['OFFICE:VALUE'] = $value;
        $this->sheets[$sheet]['rows'][$row][$cell]['value'] = $value;
    }
}
