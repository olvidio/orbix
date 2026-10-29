---
id: "misas.update_iniciales"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/update_iniciales"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/update_iniciales.php"
entrada: ["post.ctx_update:string", "post.id_sacd:integer", "post.iniciales:string", "post.color:string"]
entrada_obligatoria: ["ctx_update"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
hashb_campo: "ctx_update"
hashb_action: "update_iniciales"
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\UpdateIniciales"]
tags: ["misas", "update", "iniciales"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "<repositorio getErrorTxt()>"]
---

# Update iniciales

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

Linaje: Slice 3 — migrado desde apps/misas/controller/update_iniciales.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

## Endpoint

- URL: `/src/misas/update_iniciales`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/update_iniciales.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_update` | `string` | cápsula HashB | Sí | Cápsula firmada (acción `update_iniciales`). Los campos que vayan dentro de la cápsula se toman de ella y se ignoran en el body. |
| `id_sacd` | `integer` | application | No | Se toma de la cápsula HashB; si viene en el body se ignora. |
| `iniciales` | `string` | application | No | |
| `color` | `string` | application | No | |

## Autorizacion HashB

- Campo POST: `ctx_update`
- Accion: `update_iniciales`
- Contexto firmado: `{id_sacd}`
- Origen de la cápsula: `ver_iniciales_zona_data` → `rows[].ctx_update`
- Cápsula inválida: `success: false`, `mensaje: "Operación no autorizada"`, `data: "none"`.
- Ver `docs/dev/hash_arquitectura.md`.

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_sacd`: integer

## Errores conocidos
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\UpdateIniciales`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_iniciales_zona.php"]`).
