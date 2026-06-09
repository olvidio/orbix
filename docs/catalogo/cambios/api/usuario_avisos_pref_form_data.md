---
id: "cambios.usuario_avisos_pref_form_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/usuario_avisos_pref_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/usuario_avisos_pref_form_data.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.id_usuario:integer", "post.quien:string", "post.salida:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\UsuarioAvisosPrefFormData"]
tags: ["cambios", "usuario", "avisos", "pref", "form", "data"]
estado_revision: "generado"
---

# Usuario Avisos Pref Form Data

Endpoint JSON que devuelve la informacion necesaria para pintar el formulario `usuario_avisos_pref` (edicion de un aviso de usuario/grupo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/usuario_avisos_pref_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_avisos_pref_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto` | `integer` | controller+application | No | controller+application |
| `id_usuario` | `integer` | controller+application | No | controller+application |
| `quien` | `string` | controller+application | No | controller+application |
| `salida` | `string` | controller+application | No | controller+application |
| `sel` | `array` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`
- Permiso oficina `calendario`

## Casos De Uso

- `src\cambios\application\UsuarioAvisosPrefFormData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.