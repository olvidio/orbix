---
id: "encargossacd.horario_update_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_update_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_update_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoHorarioUpdateData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_update.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioUpdate"]
errores: ["acción no válida", "Debe llenar todos los campos que tengan un (*)", "registro no encontrado", "hay un error, no se ha guardado"]
tags: ["encargossacd", "horario", "update", "data"]
estado_revision: "revisado"
---
# Horario Update Data

Alta/edición/baja de horario de encargo (tabla encargo_horario).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta/edición/baja de horario de encargo (`encargo_horario`). Sucesor de `apps/encargossacd/controller/horario_update.php`. `mod`: nuevo|editar|eliminar; eliminar usa `sel_nom[0]` token `id_item_h#...`.

## Endpoint

- URL: `/src/encargossacd/horario_update_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_update_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `data: ""`, `{ok: true}`.
- Error: mensaje en `data`.


## Errores conocidos

- `acción no válida`
- `Debe llenar todos los campos que tengan un (*)`
- `registro no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoHorarioUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_update.php`

