---
id: "misas.eliminar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php"
entrada: ["post.ctx_eliminar:string", "post.id_enc:integer"]
entrada_obligatoria: ["ctx_eliminar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
hashb_campo: "ctx_eliminar"
hashb_action: "eliminar_encargo_zona"
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoZona"]
tags: ["misas", "eliminar", "encargo", "zona"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "No se encuentra el encargo %d", "<repositorio getErrorTxt()>"]
---

# Eliminar encargo zona

Elimina un Encargo de zona (grupo ZONAS_MISAS) por id_enc.

Linaje: Slice 4 — rama borrar de apps/misas/controller/update_encargos_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un Encargo de zona (grupo ZONAS_MISAS) por id_enc.

## Endpoint

- URL: `/src/misas/eliminar_encargo_zona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_eliminar` | `string` | cápsula HashB | Sí | Cápsula firmada (acción `eliminar_encargo_zona`). Los campos que vayan dentro de la cápsula se toman de ella y se ignoran en el body. |
| `id_enc` | `integer` | application | No | Se toma de la cápsula HashB; si viene en el body se ignora. |

## Autorizacion HashB

- Campo POST: `ctx_eliminar`
- Accion: `eliminar_encargo_zona`
- Contexto firmado: `{id_enc}`
- Origen de la cápsula: `ver_encargos_zona_data` → `rows[].ctx_eliminar`
- Cápsula inválida: `success: false`, `mensaje: "Operación no autorizada"`, `data: "none"`.
- Ver `docs/dev/hash_arquitectura.md`.

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_enc`: integer

## Errores conocidos
- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\EliminarEncargoZona`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_zona.php"]`).
