---
id: "actividadessacd.texto_comunicacion_guardar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/texto_comunicacion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php"
entrada: ["post.clave:string", "post.idioma:string", "post.texto:string"]
entrada_obligatoria: ["clave", "idioma"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros clave / idioma", "hay un error, no se ha eliminado el texto", "hay un error, no se ha guardado el texto"]
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_txt.php", "frontend/actividadessacd/view/com_sacd_txt.phtml"]
casos_uso: ["src\\actividadessacd\\application\\TextoComunicacionGuardar"]
tags: ["actividadessacd", "texto", "comunicacion", "guardar"]
estado_revision: "revisado"
---

# Texto Comunicacion Guardar

Upsert/borrado del texto de comunicación a los sacd identificado por `{clave, idioma}`
(entidad `ActividadSacdTexto`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Valida que llegan `clave` e `idioma`.
- Busca el texto existente por `{clave, idioma}`:
  - Si existe y `texto` viene vacío → lo **elimina**.
  - Si existe y `texto` tiene contenido → lo **actualiza**.
  - Si no existe y `texto` viene vacío → no hace nada (éxito silencioso).
  - Si no existe y `texto` tiene contenido → lo **crea** (nuevo `id_item`).

## Endpoint

- URL: `/src/actividadessacd/texto_comunicacion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `string` | controller (`inputString`) | Si | Clave del texto (`com_sacd`, `t_propio`, `t_f_ini`, …) |
| `idioma` | `string` | controller (`inputString`) | Si | Locale (`es_ES.UTF-8`, …) |
| `texto` | `string` | controller (`inputString`) | No | Contenido; vacío = eliminar el texto existente |

El controller construye `$input` con `clave`, `idioma` y `texto`.

## Salida

- Helper: `ContestarJson::enviar($useCase->execute($input), 'ok')` — el caso de uso devuelve el
  texto de error (vacío en éxito); `data` es el literal `"ok"`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Crea, actualiza o elimina la fila de `ActividadSacdTexto` para `{clave, idioma}`.

## Errores conocidos

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

## Permisos

- Sin control propio en el caso de uso. La pantalla `com_sacd_txt.php` restringe la edición según
  `perm_mod_txt` (proviene de `com_sacd_activ_periodo_page_data`: los usuarios con rol `p-sacd` no
  pueden modificar). URL firmada con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\TextoComunicacionGuardar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php` (emite `url_guardar`).
- `frontend/actividadessacd/view/com_sacd_txt.phtml` (`fnjs_guardar` hace el POST).
