<?php

use src\cartaspresentacion\application\CartasPresentacionShellData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', CartasPresentacionShellData::build());
