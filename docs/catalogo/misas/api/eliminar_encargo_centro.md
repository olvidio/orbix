---
id: "misas.eliminar_encargo_centro"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_centro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php"
entrada: ["post.ctx_eliminar:string", "post.id_item:string"]
entrada_obligatoria: ["ctx_eliminar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
hashb_campo: "ctx_eliminar"
hashb_action: "eliminar_encargo_centro"
errores: ["Operación no autorizada", "Falta el identificador del encargo-centro a eliminar", "No se encuentra el encargo-centro %s", "<repositorio getErrorTxt()>"]
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
| `ctx_eliminar` | `string` | cápsula HashB | Sí | Cápsula firmada (acción `eliminar_encargo_centro`). Los campos que vayan dentro de la cápsula se toman de ella y se ignoran en el body. |
| `id_item` | `string` | application | No | Se toma de la cápsula HashB; si viene en el body se ignora. |

## Autorizacion HashB

- Campo POST: `ctx_eliminar`
- Accion: `eliminar_encargo_centro`
- Contexto firmado: `{id_item}`
- Origen de la cápsula: `ver_encargos_centros_data` → `rows[].ctx_eliminar`
- Cápsula inválida: `success: false`, `mensaje: "Operación no autorizada"`, `data: "none"`.
- Ver `docs/dev/hash_arquitectura.md`.

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
