---
id: "usuarios.usuario_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_lista.php"
entrada: ["post.username:string"]
entrada_obligatoria: []
respuesta: "custom_json"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_lista.php"]
casos_uso: ["src\\usuarios\\application\\usuariosLista"]
tags: ["usuarios", "usuario", "lista"]
estado_revision: "generado"
---

# Usuario Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/usuario_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::send`
- Forma: `custom_json`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\usuarios\application\usuariosLista`

## Frontend Relacionado

- `frontend/usuarios/controller/usuario_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.