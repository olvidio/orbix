---
id: "inventario.equipajes_eliminar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: ["id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_equipaje", "No se encuentra el equipaje %d", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_ver.php"]
casos_uso: ["src\inventario\application\EquipajeEliminar"]
tags: ["inventario", "equipajes", "eliminar"]
estado_revision: "revisado"
---

# Eliminar equipaje

Borra un equipaje por `id_equipaje` vía caso de uso `EquipajeEliminar`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra un equipaje por `id_equipaje` vía caso de uso `EquipajeEliminar`.

## Endpoint

- URL: `/src/inventario/equipajes_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`.

## Errores conocidos

  - `falta id_equipaje`
  - `No se encuentra el equipaje %d`
  - `hay un error, no se ha eliminado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

src\inventario\application\EquipajeEliminar

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_ver.php`
