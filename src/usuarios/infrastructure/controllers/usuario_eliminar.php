<?php

use src\usuarios\application\usuarioEliminar;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
usuarioEliminar::eliminarFromAray($a_sel);