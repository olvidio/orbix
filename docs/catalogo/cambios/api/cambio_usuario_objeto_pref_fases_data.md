---
id: "cambios.cambio_usuario_objeto_pref_fases_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_fases_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_fases_data.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string", "post.objeto:string"]
entrada_obligatoria: ["objeto"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_fases.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefFasesData"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "fases", "data"]
estado_revision: "revisado"
---

# Cambio Usuario Objeto Pref Fases Data

Lista de fases (o estados de actividad) posibles para el `id_tipo_activ` indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Refresca el desplegable de fase de referencia al cambiar `objeto`, `id_tipo_activ` o `dl_propia`. Con
módulo `procesos` instalado devuelve fases de proceso; si no, estados `StatusId`.

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_fases_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_fases_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `objeto` | `string` | controller+application | Sí | Nombre del objeto vigilado |
| `id_tipo_activ` | `string` | controller+application | No | Código de 6 caracteres del tipo |
| `dl_propia` | `string` | controller+application | No | Delegación propia (`t`/`f`); default `true` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `error` (`string`)
  - `objeto` (`string`)
  - `aFases` (`array`): id → etiqueta
  - `fases_usa_procesos` (`bool`)

## Errores conocidos

- `primero debe elegir un objeto sobre el que mirar los cambios`

## Permisos

- Sin control propio; invocado desde el formulario de preferencias del usuario.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefFasesData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_fases.php`: fragmento AJAX HTML del desplegable;
  llamado por `fnjs_actualizar_fases` en `usuario_avisos_pref`.
