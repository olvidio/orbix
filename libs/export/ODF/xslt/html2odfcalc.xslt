<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet
version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" 
xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" 
xmlns:presentation="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0"
xmlns:officeooo="http://openoffice.org/2009/office"
xmlns:css3t="http://www.w3.org/TR/css3-text/"
xmlns:grddl="http://www.w3.org/2003/g/data-view#"
xmlns:xhtml="http://www.w3.org/1999/xhtml"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
xmlns:rpt="http://openoffice.org/2005/report"
xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:ooo="http://openoffice.org/2004/office"
xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
xmlns:drawooo="http://openoffice.org/2010/draw"
xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
xmlns:ooow="http://openoffice.org/2004/writer"
xmlns:oooc="http://openoffice.org/2004/calc"
xmlns:tableooo="http://openoffice.org/2009/table"
xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
xmlns:math="http://www.w3.org/1998/Math/MathML"
xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
xmlns:dom="http://www.w3.org/2001/xml-events"
xmlns:xforms="http://www.w3.org/2002/xforms"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
>

<!-- Copyright (C) 2006 by Tapsell-Ferrier Limited
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; see the file COPYING. If not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
-->

<xsl:output method="xml" indent="yes"/>
<xsl:output encoding="utf-8"/>
<xsl:template match="/html">
<office:document-content office:version="1.2">
	<office:scripts/>
	<office:automatic-styles>
		<xsl:call-template name="table_cols"/>
		<number:date-style style:name="N37" number:automatic-order="true">
		<number:day number:style="long"/>
		<number:text>/</number:text>
		<number:month number:style="long"/>
		<number:text>/</number:text>
		<number:year/>
		</number:date-style>
		<style:style style:name="cefecha" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N37"/>
		<number:date-style style:name="N51" number:automatic-order="true">
		<number:day number:style="long"/>
		<number:text>/</number:text>
		<number:month number:style="long"/>
		<number:text>/</number:text>
		<number:year number:style="long"/>
		<number:text> </number:text>
		<number:hours number:style="long"/>
		<number:text>:</number:text>
		<number:minutes number:style="long"/>
		<number:text>:</number:text>
		<number:seconds number:style="long"/>
		</number:date-style>
		<style:style style:name="cefechaHora" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N51"/>
		<number:number-style style:name="N0">
		<number:number number:min-integer-digits="1"/>
		</number:number-style>
		<style:style style:name="cenumero" style:family="table-cell" style:parent-style-name="Default" style:data-style-name="N0"/>
		<style:style style:name="cebold" style:family="table-cell" style:parent-style-name="Default">
			<style:text-properties fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"/>
		</style:style>
		<style:style style:name="ce2" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties fo:background-color="#fff200"/></style:style>
		<style:style style:name="Warning" style:family="table-cell" style:parent-style-name="Status"><style:text-properties fo:color="#cc0000" fo:font-style="normal" fo:font-weight="normal"/></style:style>
	</office:automatic-styles>

	<xsl:apply-templates select="body"/>
</office:document-content>
</xsl:template>

<xsl:template match="body">
	<office:body>
	 <office:spreadsheet>
	 <xsl:choose>
	 	<xsl:when test="./div[@class='salta_pag']">
				<xsl:apply-templates select="node()"/>
		</xsl:when>
		<xsl:otherwise>
			  <table:table table:name="Table1" table:style-name="Table1">
				<xsl:apply-templates select="node()"/>
			  </table:table>
	  </xsl:otherwise>
	  </xsl:choose>
	 </office:spreadsheet>
	</office:body>
</xsl:template>

<xsl:template match="h1|h2|h3|b">
 <table:table-row>
 	<table:table-cell table:style-name="cebold" office:value-type="string" >
		<xsl:call-template name="text_applyer"/>
	</table:table-cell>
 </table:table-row>
</xsl:template>

<xsl:template match="p">
 <text:p text:style-name="Standard">
  <xsl:apply-templates select="node()"/>
 </text:p>
</xsl:template>

<xsl:template name="table_cols">
		<xsl:for-each select="descendant::th">
			<xsl:variable name="colnum" select="position()" />
			<xsl:if test="@web-width">
				<xsl:variable name="ww" select="@web-width * 3 div 80" />
				<style:style style:name="co{$colnum}" style:family="table-column">
				<style:table-column-properties fo:break-before="auto" style:column-width="{$ww}cm"/>
				</style:style>
			</xsl:if>
		</xsl:for-each>
</xsl:template>

<xsl:template name="table_cols_2">
		<xsl:for-each select="descendant::th">
			<xsl:variable name="colnum" select="position()" />
			<xsl:if test="@web-width">
				<table:table-column table:style-name="co{$colnum}" table:default-cell-style-name="Default"/>
			</xsl:if>
		</xsl:for-each>
</xsl:template>

<xsl:template match="table">
	<!-- <table:table table:name="Table1" table:style-name="Table1"> -->
		<!-- <table:table-column table:style-name="Table1.A" table:number-columns-repeated="2"/> -->
		<!-- FIXME: should not do this... instead simply apply on node() and have template matches for tr[th] -->
		<xsl:call-template name="table_cols_2"/>
		<xsl:if test="child::thead">
			<xsl:apply-templates select="thead"/>
		</xsl:if>
		<xsl:if test="child::tbody">
			<xsl:apply-templates select="tbody"/>
		</xsl:if>
		<xsl:if test="child::tr">
			<xsl:apply-templates select="tr"/>
		</xsl:if>
</xsl:template>

<xsl:template name="subtable">
	<table:table-row>
		<xsl:apply-templates select="td"/>
	</table:table-row>
</xsl:template>

<xsl:template match="tbody|thead">
	<xsl:apply-templates select="tr"/>
</xsl:template>

<xsl:template match="tr">
	<xsl:choose>
		<xsl:when test="descendant::table">
			<xsl:apply-templates select="td"/>
		</xsl:when>
		<xsl:when test="th">
			<table:table-row>
				<xsl:apply-templates select="th"/>
			</table:table-row>
		</xsl:when>
		<xsl:when test="td">
			<table:table-row>
				<xsl:apply-templates select="td"/>
			</table:table-row>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="td|th">
	<xsl:choose>
		<xsl:when test="child::table">
			<xsl:for-each select="table/thead/tr|table/tr">
				<xsl:call-template name="subtable"/>
			</xsl:for-each>
			<xsl:for-each select="table/tbody/tr|table/tr">
				<xsl:call-template name="subtable"/>
			</xsl:for-each>
		</xsl:when>
		<xsl:when test="@class='warning'">
			<table:table-cell table:style-name="Warning" office:value-type="string" >
				<text:p>
					<xsl:value-of select="current()"/>
				</text:p>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="@class='alert'">
		 	<table:table-cell table:style-name="ce2" office:value-type="string" table:number-columns-spanned="1" table:number-rows-spanned="1">
				<text:p>
					<xsl:value-of select="current()"/>
				</text:p>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="@class='no_print'">
		</xsl:when>
		<xsl:when test="@tipo='notext'">
		</xsl:when>
		<xsl:when test="@class='fecha_hora'">
			<table:table-cell table:style-name="cefechaHora" office:value-type="date" office:date-value="{@fecha_iso}" >
				<xsl:call-template name="text_applyer"/>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="@class='fecha'">
			<table:table-cell table:style-name="cefecha" office:value-type="date" office:date-value="{@fecha_iso}" >
				<xsl:call-template name="text_applyer"/>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="@class='numero'">
			<xsl:choose>
				<xsl:when test="@colspan">
					<table:table-cell table:style-name="cenumero" office:value-type="float" office:value="{@num_iso}" table:number-columns-spanned="{@colspan}" table:number-rows-spanned="1">
						<xsl:call-template name="text_applyer"/>
					</table:table-cell>
				</xsl:when>
				<xsl:otherwise>
				<table:table-cell table:style-name="cenumero" office:value-type="float" office:value="{@num_iso}">
					<xsl:call-template name="text_applyer"/>
				</table:table-cell>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="child::h1|child::h2|child::h3|child::b">
		 	<table:table-cell table:style-name="cebold" office:value-type="string" table:number-columns-spanned="3" table:number-rows-spanned="1">
				<!-- <text:p> -->
					<xsl:call-template name="text_applyer"/>
				<!-- </text:p> -->
			</table:table-cell>
		</xsl:when>
		<xsl:when test="child::span[@class='warning']">
		 	<table:table-cell table:style-name="Warning" office:value-type="string" >
				<xsl:call-template name="text_applyer"/>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="child::span">
		   <table:table-cell table:style-name="ce2" office:value-type="string" table:number-columns-spanned="1" table:number-rows-spanned="1">
				<xsl:call-template name="text_applyer"/>
		   </table:table-cell>
		</xsl:when>
		<xsl:when test="@colspan">
			<table:table-cell table:style-name="ce1" office:value-type="string" table:number-columns-spanned="{@colspan}" table:number-rows-spanned="1">
				<xsl:call-template name="text_applyer"/>
			</table:table-cell>
		</xsl:when>
		<xsl:when test="@tipo='sel'">
		</xsl:when>
		<xsl:otherwise>
			<table:table-cell office:value-type="string">
				<xsl:call-template name="text_applyer"/>
			</table:table-cell>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="a">
	<xsl:call-template name="text_applyer"/>
</xsl:template>

<xsl:template match="ul">
	<text:list text:style-name="L1">
		<!-- FIXME: should not do this... instead simply apply on node() and have template matches for li -->
		<xsl:for-each select="li">
			<text:list-item><xsl:call-template name="text_applyer"/>
			</text:list-item>
		</xsl:for-each>
	</text:list>
</xsl:template>

<xsl:template name="text_applyer">
	<xsl:choose>
		<xsl:when test="h1|h2|h3|b">
			<text:p>
				<xsl:value-of select="current()"/>
			</text:p>
		</xsl:when>
		<xsl:when test="span">
			<text:p>
				<xsl:value-of select="current()"/>
			</text:p>
		</xsl:when>
		<xsl:when test="number()">
			<text:p>
				<xsl:value-of select="normalize-space(string(.))"/>
			</text:p>
		</xsl:when>
		<xsl:when test="text()">
			<text:p>
				<xsl:apply-templates select="node()"/>
				<!-- <xsl:value-of select="normalize-space(string(.))"/> -->
			</text:p>
		</xsl:when>
		<xsl:otherwise>
			<xsl:apply-templates select="node()"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="span">
	<xsl:choose>
		<xsl:when test="@class='link'">
			<xsl:call-template name="text_applyer"/>
		</xsl:when>
		<xsl:when test="@class='sortarrow'">
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="br">
	<xsl:text disable-output-escaping="yes">&amp;#13;</xsl:text>
	<xsl:text disable-output-escaping="yes">&amp;#10;</xsl:text>
</xsl:template>

<xsl:template match="p">
	<text:p><xsl:apply-templates select="node()"/>
	</text:p>
</xsl:template>

<xsl:template match="font">
	<text:p><xsl:apply-templates select="node()"/>
	</text:p>
</xsl:template>

<xsl:template match="div[@class='salta_pag']">
	<table:table table:name="Table1" table:style-name="Table1">
		<xsl:apply-templates select="node()"/>
	</table:table>
</xsl:template>


</xsl:stylesheet>
