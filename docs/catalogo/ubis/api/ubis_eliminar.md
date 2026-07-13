---
id: "ubis.ubis_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_eliminar.php"
entrada: ["post.obj_pau:string", "post.id_ubi:integer"]
entrada_obligatoria: ["obj_pau", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ubi a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/ubis/controller/ubis_eliminar.php"]
casos_uso: ["src\\ubis\\application\\UbisEliminar"]
tags: ["ubis", "eliminar"]
estado_revision: "revisado"
---

# Ubis Eliminar

Elimina un ubi (centro o casa) del repositorio correspondiente a obj_pau.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un ubi (centro o casa) del repositorio correspondiente a obj_pau.

## Endpoint

- URL: `/src/ubis/ubis_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `id_ubi` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

## Permisos

have_perm_oficina(scl): botón eliminar en ubis_tabla frontend.

## Casos De Uso

- `src\ubis\application\UbisEliminar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_eliminar.php"]`).
