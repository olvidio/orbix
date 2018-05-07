@media print {
	#menu, #submenu, #cargando {
		display: none;
	}
	#main {
	clear:both;
	height:auto;
	overflow:visible;
	}
}
@media screen, projection {
#udm {	margin-top:0px; padding-top:6px;	padding-bottom:6px;	background-color: <?= $medio ?>;	}
#submenu {
	margin-top: 0px;
	margin-left: -10px;
	padding-left: 0px;
	clear: both;
}
.udm ul a, .udm ul a.nohref {
	font-family : Arial;
	font-size : 10pt;
}
.udm a, .udm a.nohref {
	font-family : Arial;
	font-size : 12pt;
}
#main {
	clear:both;
	height:78%;
	overflow-x:auto;
	overflow-y:auto;
	padding-bottom:2em;
	padding-left:2em;
	padding-right:1em;
	padding-top:1em;
}
#menu {
	font-family : Arial;
	font-size : 12pt;
	background-color:<?= $claro ?>;
	border-style:none;
	height:auto;
	line-height:2.5em;
	margin: -10px -10px 0px;
}
#menu li:hover, #menu li:active, #menu li.selec {
	border-style:none;
	cursor: pointer;
	background-color:<?= $medio ?>;
	color: <?= $claro ?>;
}
#menu li {
	display: inline;
	background-color:<?= $claro ?>;
	color: <?= $oscuro ?>;
	font-family : verdana;
	font-weight : bold;
	font-size : 12pt;
	border-style:none;
	padding-top:0.2em;
	padding-right:0.2em;
	padding-left:0.2em;
	padding-bottom:1em;
}
}