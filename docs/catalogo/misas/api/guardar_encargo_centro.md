---
id: "misas.guardar_encargo_centro"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_encargo_centro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_encargo_centro.php"
entrada: ["post.id_item:string", "post.id_enc:integer", "post.id_ctr:integer"]
entrada_obligatoria: ["id_enc", "id_ctr"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoCentro"]
tags: ["misas", "guardar", "encargo", "centro"]
estado_revision: "revisado"
errores: ["No se encuentra el encargo-centro %s", "<repositorio getErrorTxt()>"]
---

# Guardar encargo centro

Inserta o actualiza un EncargoCtr vinculando un encargo de zona con un centro.

Linaje: Slice 5 — ramas nuevo/modificar de apps/misas/controller/update_encargos_centros.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Inserta o actualiza un EncargoCtr vinculando un encargo de zona con un centro.

## Endpoint

- URL: `/src/misas/guardar_encargo_centro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_centro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | application | No | |
| `id_enc` | `integer` | application | Si | |
| `id_ctr` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_item`: string
  - `id_enc`: integer
  - `id_ctr`: integer

## Errores conocidos
- `No se encuentra el encargo-centro %s`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\GuardarEncargoCentro`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_centros.php"]`).
