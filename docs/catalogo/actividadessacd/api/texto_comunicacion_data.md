---
id: "actividadessacd.texto_comunicacion_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/texto_comunicacion_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_data.php"
entrada: ["post.clave:string", "post.idioma:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_TextoComunicacionDataData"
respuesta_data: ["texto:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_txt.php", "frontend/actividadessacd/view/com_sacd_txt.phtml"]
casos_uso: ["src\\actividadessacd\\application\\TextoComunicacionData"]
tags: ["actividadessacd", "texto", "comunicacion", "data"]
estado_revision: "generado"
---

# Texto Comunicacion Data

Endpoint backend: devuelve el texto de comunicacion (`clave`, `idioma`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/texto_comunicacion_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `string` | application | No | application |
| `idioma` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadessacd_TextoComunicacionDataData`):
  - `texto` (`string`)

## Casos De Uso

- `src\actividadessacd\application\TextoComunicacionData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php`
- `frontend/actividadessacd/view/com_sacd_txt.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.