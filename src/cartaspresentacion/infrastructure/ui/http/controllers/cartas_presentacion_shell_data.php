<?php

use src\cartaspresentacion\application\CartasPresentacionShellData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', CartasPresentacionShellData::build());
