---
id: "usuarios.role_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "custom_json"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/role_lista.php"]
casos_uso: ["src\\usuarios\\application\\rolesLista"]
tags: ["usuarios", "role", "lista"]
estado_revision: "generado"
---

# Role Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/role_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::send`
- Forma: `custom_json`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\usuarios\application\rolesLista`

## Frontend Relacionado

- `frontend/usuarios/controller/role_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.