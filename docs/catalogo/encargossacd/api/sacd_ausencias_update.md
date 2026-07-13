---
id: "encargossacd.sacd_ausencias_update"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_update.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdAusenciasUpdateData"
respuesta_data: ["error:bool, mensajes: string"]
requiere_hashb: false
errores: ["no se ha encontrado el encargo del sacd", "hay un error, no se ha eliminado", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_update.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasUpdate"]
tags: ["encargossacd", "sacd", "ausencias", "update"]
estado_revision: "revisado"
---
# Sacd Ausencias Update

Guarda/modifica las ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_update.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda/modifica/elimina ausencias de un SACD (arrays `inicio[]`, `fin[]`, `id_enc[]`, `id_item[]`). Sucesor de `apps/encargossacd/controller/sacd_ausencias_update.php`.

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_update.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Éxito: `error: false`, `mensajes: ""`.
- Error parcial: `error: true`, `mensajes` concatenados.


## Errores conocidos

- `no se ha encontrado el encargo del sacd`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`


## Permisos

Sin control propio; menú ausencias.

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_update.php`

