---
id: "cambios.cambio_usuario_objeto_pref_guardar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_guardar.php"
entrada: ["post.aviso_off:string", "post.aviso_on:string", "post.aviso_outdate:string", "post.aviso_tipo:integer", "post.casas:array", "post.dl_propia:string", "post.id_fase_ref:integer", "post.id_item_usuario_objeto:integer", "post.id_tipo_activ:string", "post.id_usuario:integer", "post.objeto:string"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefGuardar"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "guardar"]
estado_revision: "revisado"
---

# Cambio Usuario Objeto Pref Guardar

Crea o actualiza un `CambioUsuarioObjetoPref` (qué objeto/tipo/fase/tipo de aviso se escucha).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta (`id_item_usuario_objeto=0`) o edición de la preferencia de objeto. Normaliza `id_tipo_activ` a
6 caracteres (relleno con `.`), calcula `dl_org` según `dl_propia`, y opcionalmente guarda casas
(`casas[]` → `csv_id_pau`).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller+application | Sí | Usuario o grupo destino |
| `id_item_usuario_objeto` | `integer` | controller+application | No | `0` = alta |
| `id_tipo_activ` | `string` | controller+application | No | 6 chars; se rellena con `.` |
| `dl_propia` | `string` | controller+application | No | Checkbox delegación propia |
| `objeto` | `string` | controller+application | No | Objeto del catálogo de avisos |
| `aviso_tipo` | `integer` | controller+application | No | Tipo de aviso |
| `id_fase_ref` | `integer` | controller+application | No | Fase/estado de referencia |
| `aviso_off` / `aviso_on` / `aviso_outdate` | `string` | controller+application | No | Checkboxes |
| `casas` | `array` | controller+application | No | IDs de ubicación filtradas |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `data` con `id_item_usuario_objeto` (`int`).
- Error: mensaje en envelope; `id_item_usuario_objeto: 0` en payload.

## Errores conocidos

- `falta id_usuario`
- `id_tipo_activ invalido`
- `Hay un error, no se ha guardado`

## Permisos

- Sin control propio; formulario `usuario_avisos_pref` + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefGuardar`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref.php`: primer paso de `fnjs_grabar_todo` vía
  `url_guardar_objeto` del payload de `usuario_avisos_pref_form_data`.
