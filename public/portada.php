<?php
namespace core;
use web;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

?>

<script> 
  $(function(){
      $("#includedContent").load("public/ayuda/index.php"); 
  });
</script>

<div id=includedContent ></div>