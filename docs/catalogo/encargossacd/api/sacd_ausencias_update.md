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
errores: ["no se ha encontrado el encargo del sacd"]
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_update.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasUpdate"]
tags: ["encargossacd", "sacd", "ausencias", "update"]
estado_revision: "generado"
---

# Sacd Ausencias Update

Guarda/modifica las ausencias de un SACD (`frontend/encargossacd/controller/sacd_ausencias_update.php`). Devuelve ['error' => bool, 'mensajes' => string] donde `mensajes` acumula los errores de guardado/eliminacion para mostrar al usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_update.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_SacdAusenciasUpdateData`):
  - `error` (`bool, mensajes: string`)

## Efectos colaterales

- Devuelve ['error' => bool, 'mensajes' => string] donde `mensajes` acumula los errores de guardado/eliminacion para mostrar al usuario.

## Errores conocidos

- `no se ha encontrado el encargo del sacd`

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.