---
id: "inventario.lista_equipajes_desde_fecha"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_equipajes_desde_fecha"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php"
entrada: ["post.f_ini_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/inventario/controller/equipajes_desplegable.php", "frontend/inventario/controller/equipajes_movimientos_que.php", "frontend/inventario/controller/equipajes_ver.php"]
casos_uso: []
tags: ["inventario", "lista", "equipajes", "desde", "fecha"]
estado_revision: "generado"
---

# Lista Equipajes Desde Fecha

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/lista_equipajes_desde_fecha`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_iso` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_desplegable.php`
- `frontend/inventario/controller/equipajes_movimientos_que.php`
- `frontend/inventario/controller/equipajes_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.