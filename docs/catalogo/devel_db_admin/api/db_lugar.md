---
id: "devel_db_admin.db_lugar"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/db_lugar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/db_lugar.php"
entrada: ["post.region:string"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php", "frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml", "frontend/devel_db_admin/view/db_crear_esquema_que.phtml", "frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml"]
casos_uso: ["src\\devel_db_admin\\application\\DbLugarDropdown"]
tags: ["devel_db_admin", "db", "lugar"]
estado_revision: "generado"
---

# Db Lugar

Fragmento HTML: desplegable `dl` según `region` (POST), para AJAX en db_que / db_cambiar_nombre_que.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/db_lugar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_lugar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | No | controller |

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\devel_db_admin\application\DbLugarDropdown`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_cambiar_nombre_que.php`
- `frontend/devel_db_admin/controller/db_crear_esquema_que.php`
- `frontend/devel_db_admin/controller/db_eliminar_esquema_que.php`
- `frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml`
- `frontend/devel_db_admin/view/db_crear_esquema_que.phtml`
- `frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.