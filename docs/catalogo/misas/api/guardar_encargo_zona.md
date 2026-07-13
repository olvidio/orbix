---
id: "misas.guardar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php"
entrada: ["post.id_enc:integer", "post.id_tipo_enc:integer", "post.id_ubi:integer", "post.id_zona:integer", "post.orden:integer", "post.prioridad:integer", "post.descripcion_lugar:string", "post.encargo:string", "post.idioma_enc:string", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_GuardarEncargoZonaData"
respuesta_data: ["error:string, data: array{id_enc: int, lugar: string}"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoZona"]
tags: ["misas", "guardar", "encargo", "zona"]
estado_revision: "revisado"
errores: ["No se encuentra el encargo %d", "<repositorio getErrorTxt()>"]
---

# Guardar encargo zona

Crea o actualiza un Encargo del grupo ZONAS_MISAS (id_enc=0 → alta) y devuelve id y nombre del centro.

Linaje: Slice 4 — ramas nuevo/modificar de apps/misas/controller/update_encargos_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza un Encargo del grupo ZONAS_MISAS (id_enc=0 → alta) y devuelve id y nombre del centro.

## Endpoint

- URL: `/src/misas/guardar_encargo_zona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `integer` | application | No | |
| `id_tipo_enc` | `integer` | application | No | |
| `id_ubi` | `integer` | application | No | |
| `id_zona` | `integer` | application | No | |
| `orden` | `integer` | application | No | |
| `prioridad` | `integer` | application | No | |
| `descripcion_lugar` | `string` | application | No | |
| `encargo` | `string` | application | No | |
| `idioma_enc` | `string` | application | No | |
| `observ` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_enc`: integer
  - `lugar`: string

## Errores conocidos
- `No se encuentra el encargo %d`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\GuardarEncargoZona`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_encargos_zona.php"]`).
