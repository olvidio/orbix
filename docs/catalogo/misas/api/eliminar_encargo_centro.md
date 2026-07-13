---
id: "misas.eliminar_encargo_centro"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_centro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta el identificador del encargo-centro a eliminar", "No se encuentra el encargo-centro %s", "<repositorio getErrorTxt()>"]
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoCentro"]
tags: ["misas", "eliminar", "encargo", "centro"]
estado_revision: "revisado"
---

# Eliminar encargo centro

Elimina la relación EncargoCtr (encargo visible en un centro) por uuid.

Linaje: Slice 5 — rama borrar de apps/misas/controller/update_encargos_centros.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina la relación EncargoCtr (encargo visible en un centro) por uuid.

## Endpoint

- URL: `/src/misas/eliminar_encargo_centro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_item`: string

## Errores conocidos
- `Falta el identificador del encargo-centro a eliminar`
- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\EliminarEncargoCentro`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_centros.php"]`).
