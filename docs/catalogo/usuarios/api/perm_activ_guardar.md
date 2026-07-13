---
id: "usuarios.perm_activ_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_activ_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php"
entrada: ["post.id_usuario:integer", "post.id_tipo_activ:integer", "post.id_item:integer", "post.dl_propia:string", "post.fase_ref:array", "post.perm_on:array", "post.perm_off:array", "post.afecta_a:array"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: []
tags: ["usuarios", "perm", "activ", "guardar"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado", "hay un error."]
---

# Perm Activ Guardar

Crea/actualiza/elimina permisos de actividad por tipo, fase y bits on/off para cada `afecta_a`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea/actualiza/elimina permisos de actividad por tipo, fase y bits on/off para cada `afecta_a`.

## Endpoint

- URL: `/src/usuarios/perm_activ_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |
| `id_tipo_activ` | `integer` | application | No | |
| `id_item` | `integer` | application | No | |
| `dl_propia` | `string` | application | No | |
| `fase_ref` | `array` | application | No | |
| `perm_on` | `array` | application | No | |
| `perm_off` | `array` | application | No | |
| `afecta_a` | `array` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `hay un error, no se ha guardado`
- `hay un error.`

## Permisos

Admin en modal permisos actividad del usuario.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/procesos/controller/usuario_perm_activ.php"]`).
