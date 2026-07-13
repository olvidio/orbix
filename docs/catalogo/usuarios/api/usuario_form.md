---
id: "usuarios.usuario_form"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_form"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_form.php"
entrada: ["post.id_usuario:integer", "post.quien:string"]
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_form.php"]
casos_uso: []
tags: ["usuarios", "usuario", "form"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "no tiene permisos para ver esto", "Rol no encontrado"]
---

# Usuario Form

Builder formulario usuario: alta/edición con roles, pau, permisos menú/actividad y ctx firmado para guardar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Builder formulario usuario: alta/edición con roles, pau, permisos menú/actividad y ctx firmado para guardar.

## Endpoint

- URL: `/src/usuarios/usuario_form`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_form.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | No | |
| `quien` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_campos`: JSON string con ctx_guardar HashB, roles, pau, permisos… (doble parse)

## Errores conocidos
- `Usuario no encontrado`
- `no tiene permisos para ver esto`
- `Rol no encontrado`

## Permisos

id_role<4 admin; resto solo avisos (error permisos).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_form.php"]`).
