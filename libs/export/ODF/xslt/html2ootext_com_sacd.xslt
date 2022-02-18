<?xml version="1.0" encoding="utf-8" ?>
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
<xsl:output type="text" encoding="utf-8"/>
<xsl:template match="/html">
<office:document-content office:version="1.0"> <office:scripts/>
	<office:automatic-styles>
	<style:style style:name="Taula1" style:family="table">
		<style:table-properties style:width="25.698cm" table:align="margins" fo:margin-bottom="1cm"/>
	</style:style>
	<style:style style:name="Taula1.A" style:family="table-column">
		<style:table-column-properties style:column-width="2.4cm" style:rel-column-width="1366*"/>
	</style:style>
	<style:style style:name="Taula1.B" style:family="table-column">
		<style:table-column-properties style:column-width="2.22cm" style:rel-column-width="1260*"/>
	</style:style>
	<style:style style:name="Taula1.C" style:family="table-column">
		<style:table-column-properties style:column-width="5.13cm" style:rel-column-width="2909*"/>
	</style:style>
	<style:style style:name="Taula1.D" style:family="table-column">
		<style:table-column-properties style:column-width="1.95cm" style:rel-column-width="1094*"/>
	</style:style>
	<style:style style:name="Taula1.E" style:family="table-column">
		<style:table-column-properties style:column-width="1.32cm" style:rel-column-width="750*"/>
	</style:style>
	<style:style style:name="Taula1.F" style:family="table-column">
		<style:table-column-properties style:column-width="1.27cm" style:rel-column-width="720*"/>
	</style:style>
	<style:style style:name="Taula1.G" style:family="table-column">
		<style:table-column-properties style:column-width="3.21cm" style:rel-column-width="1818*"/>
	</style:style>
	<style:style style:name="Taula1.H" style:family="table-column">
		<style:table-column-properties style:column-width="4.5cm" style:rel-column-width="2551*"/>
	</style:style>
	<style:style style:name="Taula1.I" style:family="table-column">
		<style:table-column-properties style:column-width="3.7cm" style:rel-column-width="2097*"/>
	</style:style>
	<style:style style:name="Taula1.A1" style:family="table-cell">
		<style:table-cell-properties fo:padding="0.097cm" fo:border-left="0.002cm solid #000000" fo:border-right="none" fo:border-top="0.002cm solid #000000" fo:border-bottom="0.002cm solid #000000"/>
	</style:style>
	<style:style style:name="Taula1.I1" style:family="table-cell">
		<style:table-cell-properties fo:padding="0.097cm" fo:border="0.002cm solid #000000"/>
	</style:style>
	<style:style style:name="Taula1.A2" style:family="table-cell">
		<style:table-cell-properties fo:padding="0.097cm" fo:border-left="0.002cm solid #000000" fo:border-right="none" fo:border-top="none" fo:border-bottom="0.002cm solid #000000"/>
	</style:style>
	<style:style style:name="Taula1.I2" style:family="table-cell">
		<style:table-cell-properties fo:padding="0.097cm" fo:border-left="0.002cm solid #000000" fo:border-right="0.002cm solid #000000" fo:border-top="none" fo:border-bottom="0.002cm solid #000000"/>
	</style:style>

	<style:style style:name="P1" style:family="paragraph" style:parent-style-name="capçalera_20_landscape">
	</style:style>
	<style:style style:name="P2" style:family="paragraph" style:parent-style-name="comunicacio">
		<style:paragraph-properties>
		<style:tab-stops/>
		</style:paragraph-properties>
	</style:style>
	<style:style style:name="P3" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties fo:text-align="center" style:justify-single-word="false">
			<style:tab-stops/>
		</style:paragraph-properties>
		<style:text-properties fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"/>
	</style:style>
	<style:style style:name="P4" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties fo:margin-left="0.009cm" fo:margin-right="0.009cm" fo:text-align="center" style:justify-single-word="false" fo:text-indent="0cm" style:auto-text-indent="false">
			<style:tab-stops/>
		</style:paragraph-properties>
	<style:text-properties fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"/>
	</style:style>
	<style:style style:name="P5" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties fo:text-align="center" style:justify-single-word="false">
			<style:tab-stops/>
		</style:paragraph-properties>
	</style:style>
	<style:style style:name="P6" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties fo:text-align="center" style:justify-single-word="false">
			<style:tab-stops/>
		</style:paragraph-properties>
	</style:style>
	<style:style style:name="P7" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties fo:break-before="page"/>
	</style:style>
</office:automatic-styles>
	
<xsl:apply-templates select="body"/>
</office:document-content>
</xsl:template>

<xsl:template match="body">
	<office:body>
	 <office:text>
		<xsl:apply-templates select="node()"/>
	 </office:text>
	</office:body>
</xsl:template>


<xsl:template match="table">
	<table:table table:name="Taula1" table:style-name="Taula1">
	<table:table-column table:style-name="Taula1.A"/>
	<table:table-column table:style-name="Taula1.B"/>
	<table:table-column table:style-name="Taula1.C"/>
	<table:table-column table:style-name="Taula1.D"/>
	<table:table-column table:style-name="Taula1.E"/>
	<table:table-column table:style-name="Taula1.F"/>
	<table:table-column table:style-name="Taula1.G"/>
	<table:table-column table:style-name="Taula1.H"/>
	<table:table-column table:style-name="Taula1.I"/>

	<xsl:for-each select="thead|tbody">
		<xsl:for-each select="tr[td]">
			<table:table-row>
			<xsl:choose>
				<xsl:when test="position() = 1"> <!-- la primera fila está en negrita -->
						<xsl:apply-templates select="td">
							<xsl:with-param name="negrita" select="1" />
						</xsl:apply-templates>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="td"/>
				</xsl:otherwise>
			</xsl:choose>
			</table:table-row>
		</xsl:for-each>
	</xsl:for-each>
	</table:table>
</xsl:template>

<xsl:template match="td">
	<xsl:param name="negrita" />
	<xsl:choose>
		<xsl:when test="$negrita = 1"> <!-- la primera fila está en negrita -->
			<xsl:variable name="parrafo" select="'P3'" />
			<xsl:choose>
				<xsl:when test="position() > 8"> <!-- la ultima columna tiene estilo diferente (borde izqd) -->
					<xsl:variable name="tabla" select="'Taula1.I1'" />
						<xsl:call-template name="celda">
							<xsl:with-param name="parrafo" select="$parrafo" />
							<xsl:with-param name="tabla" select="$tabla" />
						</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="tabla" select="'Taula1.A1'" />
						<xsl:call-template name="celda">
							<xsl:with-param name="parrafo" select="$parrafo" />
							<xsl:with-param name="tabla" select="$tabla" />
						</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="parrafo" select="'P5'" />
			<xsl:choose>
				<xsl:when test="position() > 8"> <!-- la ultima columna tiene estilo diferente (borde izqd) -->
					<xsl:variable name="tabla" select="'Taula1.I2'" />
						<xsl:call-template name="celda">
							<xsl:with-param name="parrafo" select="$parrafo" />
							<xsl:with-param name="tabla" select="$tabla" />
						</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="tabla" select="'Taula1.A2'" />
						<xsl:call-template name="celda">
							<xsl:with-param name="parrafo" select="$parrafo" />
							<xsl:with-param name="tabla" select="$tabla" />
						</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="celda">
	<xsl:param name="parrafo" />
	<xsl:param name="tabla" />
	<table:table-cell table:style-name="{$tabla}" office:value-type="string">
		<text:p text:style-name="{$parrafo}">
			<xsl:value-of select="normalize-space(string(.))"/>
		</text:p>
	</table:table-cell>
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

<xsl:template match="td/p">
	<!-- la primera p la pongo en la misma linea -->
	<xsl:choose>
		<xsl:when test="position() > 1">
			<text:p text:style-name="P2">
				<xsl:for-each select="../../td">
					<!-- En la primera col no pongo tabulador -->
					<xsl:if test="position() > 2">
						<text:tab />
					</xsl:if>
				</xsl:for-each>
				<xsl:value-of select="."/>
			</text:p>
		</xsl:when>
		<xsl:otherwise>
			<!-- <xsl:value-of select="."/> -->
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="text_applyer">
	<xsl:choose>
		<xsl:when test="text()">
			<xsl:value-of select="normalize-space(string(.))"/>
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

<xsl:template match="p">
 <text:p text:style-name="Standard">
  <xsl:apply-templates select="node()"/>
 </text:p>
</xsl:template>

<xsl:template match="div[@class='salta_pag']">
  <xsl:apply-templates select="node()"/>
 <text:p text:style-name="P7" />
</xsl:template>

<xsl:template match="cabecera|pie">
 <text:p text:style-name="P1">
 	<xsl:value-of select="./izquierda"/>
  <text:tab />
  <text:tab />
	<xsl:value-of select="./derecha"/>
 </text:p>
</xsl:template>

<xsl:template match="comunicacion">
 <text:p text:style-name="P2">
  <xsl:apply-templates select="node()"/>
 </text:p>
</xsl:template>

</xsl:stylesheet>
