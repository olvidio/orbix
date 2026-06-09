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
errores: ["falta id_equipaje", "hay un error, no se ha eliminado"]
frontend_referencias: []
casos_uso: ["src\\inventario\\application\\EquipajeEliminar"]
tags: ["inventario", "equipajes", "eliminar"]
estado_revision: "generado"
---

# Equipajes Eliminar

Borrado de un equipaje (antes solo en `equipajes_eliminar.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/equipajes_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | controller | Si | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Borrado de un equipaje (antes solo en `equipajes_eliminar.php`).

## Errores conocidos

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\inventario\application\EquipajeEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.