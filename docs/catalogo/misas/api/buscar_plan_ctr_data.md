---
id: "misas.buscar_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_BuscarPlanCtrDataData"
respuesta_data: ["view:'sacd'|'centro'|'none'", "zonas_opciones:array", "zonas_selected:integer", "centros_opciones:array", "centros_selected:string", "id_ubi_centro:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/buscar_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanCtrData"]
tags: ["misas", "buscar", "plan", "ctr", "data"]
estado_revision: "revisado"
errores: ["No tiene permiso para ver esta página"]
---

# Buscar plan ctr Data

Inicializa el formulario de búsqueda del plan CTR: zonas, centros disponibles y selección por defecto según rol del usuario.

Linaje: Slice 7 — migrado desde apps/misas/controller/buscar_plan_ctr.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Inicializa el formulario de búsqueda del plan CTR: zonas, centros disponibles y selección por defecto según rol del usuario.

## Endpoint

- URL: `/src/misas/buscar_plan_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `view`: sacd|centro|none
  - `zonas_opciones`: array<int|string, string>
  - `zonas_selected`: integer
  - `centros_opciones`: array<int|string, string>
  - `centros_selected`: string
  - `id_ubi_centro`: string

## Errores conocidos
- `No tiene permiso para ver esta página`

## Permisos

Rol Centro sv/sf: solo su centro Rol p-sacd: IdNomJefeResolver (jefe calendario ve todas las zonas; sacd no-jefe solo sus zonas) view=none si sin permiso o usuario no encontrado

## Casos De Uso

- `src\misas\application\BuscarPlanCtrData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/buscar_plan_ctr.php"]`).
