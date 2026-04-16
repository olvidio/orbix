<?php

use src\ubis\application\CentrosUpdate;

header('Content-Type: text/plain; charset=UTF-8');
echo CentrosUpdate::execute($_POST);
