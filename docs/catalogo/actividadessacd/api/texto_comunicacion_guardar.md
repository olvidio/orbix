---
id: "actividadessacd.texto_comunicacion_guardar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/texto_comunicacion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php"
entrada: ["post.clave:string", "post.idioma:string", "post.texto:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros clave / idioma", "hay un error, no se ha eliminado el texto", "hay un error, no se ha guardado el texto"]
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_txt.php", "frontend/actividadessacd/view/com_sacd_txt.phtml"]
casos_uso: ["src\\actividadessacd\\application\\TextoComunicacionGuardar"]
tags: ["actividadessacd", "texto", "comunicacion", "guardar"]
estado_revision: "generado"
---

# Texto Comunicacion Guardar

Endpoint backend: upsert/delete del texto de comunicacion (`clave`, `idioma`, `texto`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/texto_comunicacion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `string` | application | No | application |
| `idioma` | `string` | application | No | application |
| `texto` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Guarda/actualiza/elimina el texto de comunicacion de `{clave, idioma}`.
- Reglas (mismas que el legacy `com_sacd_txt_ajax.php` rama `update`): - Si ya existe la fila y `texto === ''` → se elimina.

## Errores conocidos

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

## Casos De Uso

- `src\actividadessacd\application\TextoComunicacionGuardar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php`
- `frontend/actividadessacd/view/com_sacd_txt.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.