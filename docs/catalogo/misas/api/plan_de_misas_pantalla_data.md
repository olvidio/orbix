---
id: "misas.plan_de_misas_pantalla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/plan_de_misas_pantalla_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php"
entrada: ["post.pantalla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_PlanDeMisasPantallaDataData"
respuesta_data: ["pantalla:string", "zonas_opciones:array", "orden_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["misas", "plan", "de", "pantalla", "data"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "No tiene permiso para ver esta página"]
---

# Plan de misas pantalla Data

Datos comunes para pantallas preparar/modificar/ver plan de misas: zonas, orden y tipos de plantilla en preparar.

Linaje: Slice 8 — sustituye lógica de apps/misas/controller/preparar_plan_de_misas.php, modificar_plan_de_misas.php, ver_plan_de_misas.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Datos comunes para pantallas preparar/modificar/ver plan de misas: zonas, orden y tipos de plantilla en preparar.

## Endpoint

- URL: `/src/misas/plan_de_misas_pantalla_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pantalla` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `pantalla`: string
  - `zonas_opciones`: array
  - `orden_opciones`: array
  - `tipos_plantilla`: array (solo preparar)
  - `plantilla_selected`: string (solo preparar)

## Errores conocidos
- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Permisos

IdNomJefeResolver

## Casos De Uso

- `src\misas\application\PlanDeMisasPantallaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]`).
