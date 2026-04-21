<?php

use src\encargossacd\application\EncargoComprobacionesCtr;
use web\ContestarJson;

ContestarJson::enviar('', EncargoComprobacionesCtr::ejecutar());
