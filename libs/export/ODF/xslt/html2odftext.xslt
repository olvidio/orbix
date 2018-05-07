<?xml version="1.0" encoding="utf-8" ?> <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

<!-- Copyright (C) 2006 by Tapsell-Ferrier Limited

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; see the file COPYING. If not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA -->

<xsl:output method="xml" indent="yes"/>
<xsl:output type="text" encoding="utf-8"/>
<xsl:template match="/html">
<office:document-content office:version="1.0"> <office:scripts/>
	<office:automatic-styles>
		<style:style style:name="P3" style:family="paragraph" style:parent-style-name="Standard">
			<style:paragraph-properties fo:break-before="page">
				<style:tab-stops/>
			</style:paragraph-properties>
		</style:style>
		<style:style style:name="P1" style:family="paragraph" style:parent-style-name="Standard" />
		<style:style style:name="T2" style:family="text">
			<style:text-properties fo:background-color="#fff200" loext:char-shading-value="0"/>
		</style:style>
		<xsl:call-template name="table_cols"/>
	</office:automatic-styles>

	<xsl:apply-templates select="body"/>
</office:document-content>
</xsl:template>

<xsl:template match="body">
	<office:body>
	 <office:text text:use-soft-page-breaks="true">
	 	<xsl:apply-templates select="node()"/>
	 </office:text>
	</office:body>
</xsl:template>

<xsl:template match="div[@class='salta_pag']">
	<xsl:apply-templates select="node()"/>
	<text:h text:style-name="P3" text:outline-level="3" />
	<!-- </text:h> -->
</xsl:template>

<xsl:template match="h1">
 <text:h text:style-name="Heading_20_1" text:outline-level="1">
  <xsl:apply-templates select="node()"/>
 </text:h>
</xsl:template>

<xsl:template match="h2">
 <text:h text:style-name="Heading_20_2" text:outline-level="1">
  <xsl:apply-templates select="node()"/>
 </text:h>
</xsl:template>

<xsl:template match="h3">
 <text:h text:style-name="Heading_20_3" text:outline-level="1">
  <xsl:apply-templates select="node()"/>
 </text:h>
</xsl:template>

<xsl:template match="h4">
 <text:h text:style-name="Heading_20_4" text:outline-level="1">
  <xsl:apply-templates select="node()"/>
 </text:h>
</xsl:template>

<xsl:template name="table_cols">
	<style:style style:name="P2" style:family="paragraph" style:parent-style-name="Standard">
		<style:paragraph-properties>
			<style:tab-stops>
			<xsl:for-each select="descendant::th">
				<xsl:variable name="colnum" select="position()" />
				<xsl:if test="@tab-width">
					<xsl:variable name="ww" select="@tab-width * 2 div 100" />
					<style:tab-stop style:position="{$ww}cm" />
				</xsl:if>
			</xsl:for-each>
			</style:tab-stops>
		</style:paragraph-properties>
	</style:style>
</xsl:template>

<xsl:template match="table">
		<!-- <table:table-column table:style-name="Table1.A" table:number-columns-repeated="2"/> -->
		<!-- FIXME: should not do this... instead simply apply on node() and have template matches for tr[th] -->
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


<xsl:template match="tbody|thead">
	<xsl:apply-templates select="tr"/>
</xsl:template>


<xsl:template name="subtable">
	<xsl:choose>
		<xsl:when test="descendant::h3">
			<xsl:apply-templates select="node()"/>
		</xsl:when>
		<xsl:when test="child::td/h3|child::td/b">
			<xsl:apply-templates select="td"/>
		</xsl:when>
		<xsl:when test="child::th/h3|child::th/b">
			<xsl:apply-templates select="th"/>
		</xsl:when>
		<xsl:otherwise>
			<text:p text:style-name="P2">
				<xsl:apply-templates select="node()"/>
			</text:p>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="tr">
	<xsl:call-template name="subtable"/>
</xsl:template>
		<!--
		<xsl:if test="./td/p">
			<text:p text:style-name="WP2">
			<xsl:apply-templates select="td/p"/>
			</text:p>
		</xsl:if>
		-->

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
		<xsl:when test="@class='alert'">
			<text:tab />
			<text:span text:style-name="T2">
				<xsl:value-of select="node()"/>
			</text:span>
		</xsl:when>
		<xsl:when test="@tipo='no_print'">
		</xsl:when>
		<xsl:when test="@tipo='notext'">
		</xsl:when>
		<xsl:when test="@tipo='sel'">
		</xsl:when>
		<xsl:when test="./p">
				<text:tab />
				<xsl:value-of select="normalize-space(string(./p))"/>
		</xsl:when>
		<xsl:when test="descendant::h3">
			<text:h text:style-name="Heading_20_3" text:outline-level="3"><xsl:value-of select="current()"/></text:h>
		</xsl:when>
		<xsl:when test="child::h3|child::b">
			<xsl:call-template name="text_applyer" />
		</xsl:when>

		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="preceding-sibling::td[@tipo='sel']">
					<!-- En la primera col no pongo tabulador (tampoco en el select) -->
					<xsl:if test="position() > 2">
						<text:tab />
					</xsl:if>
					<xsl:call-template name="text_applyer" />
				</xsl:when>
				<xsl:otherwise>
					<!-- En la primera col no pongo tabulador -->
					<xsl:if test="position() > 1">
						<text:tab />
					</xsl:if>
					<xsl:call-template name="text_applyer" />
				</xsl:otherwise>
			</xsl:choose>
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
				<xsl:value-of select="normalize-space(string(.))"/>
			</text:p>
		</xsl:when>
		<xsl:otherwise>
			<!-- <xsl:value-of select="."/> -->
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="text_applyer">
	<xsl:choose>
		<xsl:when test="h1|h2|h3|b">
			<text:h text:style-name="Heading_20_3" text:outline-level="3">
				<xsl:value-of select="node()"/>
			</text:h>
		</xsl:when>
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
		<xsl:when test="@class='alert'">
			<text:span text:style-name="T2">
				<xsl:value-of select="node()"/>
			</text:span>
		</xsl:when>
		<xsl:when test="@class='sortarrow'">
		</xsl:when>
		<xsl:otherwise>
		<!--	<xsl:apply-templates select="node()"/> -->
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="p">
 <text:p text:style-name="Standard">
  <xsl:apply-templates select="node()"/>
 </text:p>
</xsl:template>

<xsl:template match="docs">
 <text:p text:style-name="Standard">
  <xsl:apply-templates select="node()"/>
 </text:p>
</xsl:template>

</xsl:stylesheet>
