<?php

use usuarios\domain\usuarioEliminar;

require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
usuarioEliminar::eliminarFromAray($a_sel);