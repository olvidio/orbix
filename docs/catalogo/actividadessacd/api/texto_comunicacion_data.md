---
id: "actividadessacd.texto_comunicacion_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/texto_comunicacion_data"
metodos: ["GET", "POST"]
operacion: "form_data"
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
estado_revision: "revisado"
---

# Texto Comunicacion Data

Devuelve el texto de comunicación a los sacd para un `{clave, idioma}` (para precargar el `<textarea>`
del editor).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Si falta `clave` o `idioma`, devuelve `{texto: ''}`.
- Busca el `ActividadSacdTexto` por `{clave, idioma}`; si no existe, `{texto: ''}`; si existe,
  devuelve su contenido.

## Endpoint

- URL: `/src/actividadessacd/texto_comunicacion_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `string` | controller (`inputString`) | No | Clave del texto (`com_sacd`, `t_propio`, …); vacío devuelve texto vacío |
| `idioma` | `string` | controller (`inputString`) | No | Locale (`es_ES.UTF-8`, …); vacío devuelve texto vacío |

El controller construye `$input` con `clave` e `idioma`.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_TextoComunicacionDataData`):
  - `texto` (`string`): contenido del texto (cadena vacía si no existe).

## Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend
  (`com_sacd_txt.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadessacd\application\TextoComunicacionData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php` (emite `url_data` y precarga `com_sacd/es`).
- `frontend/actividadessacd/view/com_sacd_txt.phtml` (`fnjs_get_texto` recarga el textarea al cambiar clave/idioma).
