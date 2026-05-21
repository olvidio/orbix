---
id: "devel_db_admin.crear_usuarios"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/crear_usuarios"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/crear_usuarios.php"
entrada: ["post.dl:string", "post.region:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_crear_usuarios.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearUsuarios"]
tags: ["devel_db_admin", "crear", "usuarios"]
estado_revision: "generado"
---

# Crear Usuarios

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/crear_usuarios`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_usuarios.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\devel_db_admin\application\CrearUsuarios`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_crear_usuarios.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.