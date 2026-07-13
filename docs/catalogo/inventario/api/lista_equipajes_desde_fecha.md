---
id: "inventario.lista_equipajes_desde_fecha"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_equipajes_desde_fecha"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php"
entrada: ["post.f_ini_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_desplegable.php", "frontend/inventario/controller/equipajes_movimientos_que.php", "frontend/inventario/controller/equipajes_ver.php"]
casos_uso: []
tags: ["inventario", "lista", "equipajes", "desde", "fecha"]
estado_revision: "revisado"
---

# Equipajes desde fecha

Opciones de equipajes con fecha inicio >= `f_ini_iso` (desplegable/movimientos).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Opciones de equipajes con fecha inicio >= `f_ini_iso` (desplegable/movimientos).

## Endpoint

- URL: `/src/inventario/lista_equipajes_desde_fecha`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_iso` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_opciones}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_desplegable.php`
- `frontend/inventario/controller/equipajes_movimientos_que.php`
- `frontend/inventario/controller/equipajes_ver.php`
