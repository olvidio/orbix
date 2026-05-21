---
id: "actividadessacd.locales_desplegable_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/locales_desplegable_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/locales_desplegable_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_LocalesDesplegableDataData"
respuesta_data: ["a_locales:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_txt.php"]
casos_uso: ["src\\actividadessacd\\application\\LocalesDesplegableData"]
tags: ["actividadessacd", "locales", "desplegable", "data"]
estado_revision: "generado"
---

# Locales Desplegable Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/locales_desplegable_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/locales_desplegable_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadessacd_LocalesDesplegableDataData`):
  - `a_locales` (`array`)

## Casos De Uso

- `src\actividadessacd\application\LocalesDesplegableData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.