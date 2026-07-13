---
id: "misas.eliminar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php"
entrada: ["post.id_enc:integer"]
entrada_obligatoria: ["id_enc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoZona"]
tags: ["misas", "eliminar", "encargo", "zona"]
estado_revision: "revisado"
errores: ["No se encuentra el encargo %d", "<repositorio getErrorTxt()>"]
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
| `id_enc` | `integer` | application | Si | |

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
