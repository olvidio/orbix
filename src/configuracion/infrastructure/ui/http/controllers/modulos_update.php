<?php

use src\configuracion\application\ModulosUpdateAction;

header('Content-Type: text/plain; charset=UTF-8');
echo ModulosUpdateAction::run($_POST);
