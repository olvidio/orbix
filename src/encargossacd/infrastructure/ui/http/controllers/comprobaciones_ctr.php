<?php

use src\encargossacd\application\EncargoComprobacionesCtr;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', EncargoComprobacionesCtr::ejecutar());
