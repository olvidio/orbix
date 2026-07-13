---
id: "encargossacd.horario_sacd_update_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_sacd_update_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_update_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoSacdHorarioUpdateData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_sacd_update.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSacdHorarioUpdate"]
errores: ["acción no válida", "registro no encontrado", "Debe llenar todos los campos que tengan un (*)", "hay un error, no se ha guardado"]
tags: ["encargossacd", "horario", "sacd", "update", "data"]
estado_revision: "revisado"
---
# Horario Sacd Update Data

Alta/edición/baja de horario de encargo sacd (`encargo_sacd_horario`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta/edición/baja de horario de tarea SACD (`encargo_sacd_horario`). Sucesor de ramas horario en legacy SACD.

## Endpoint

- URL: `/src/encargossacd/horario_sacd_update_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_update_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `data: ""`, payload `{ok: true}`.
- Error: mensaje en `data` (`_error`).


## Errores conocidos

- `acción no válida`
- `registro no encontrado`
- `Debe llenar todos los campos que tengan un (*)`
- `hay un error, no se ha guardado`

## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoSacdHorarioUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_sacd_update.php`

