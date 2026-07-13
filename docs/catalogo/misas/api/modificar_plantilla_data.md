---
id: "misas.modificar_plantilla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_plantilla_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_plantilla_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_PlanDeMisasPantallaDataData"
respuesta_data: ["pantalla:string", "zonas_opciones:array", "orden_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_plantilla.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["misas", "modificar", "plantilla", "data"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "No tiene permiso para ver esta página"]
---

# Modificar plantilla Data

Carga desplegables de zona, orden y tipos de plantilla (con preferencia ultima_plantilla) para modificar plantilla.

Linaje: Slice 9 — reutiliza PlanDeMisasPantallaData con pantalla=modificar_plantilla; migrado desde apps/misas/controller/modificar_plantilla.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga desplegables de zona, orden y tipos de plantilla (con preferencia ultima_plantilla) para modificar plantilla.

## Endpoint

- URL: `/src/misas/modificar_plantilla_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_plantilla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `pantalla`: modificar_plantilla
  - `zonas_opciones`: array
  - `orden_opciones`: array
  - `tipos_plantilla`: array
  - `plantilla_selected`: string

## Errores conocidos
- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Permisos

IdNomJefeResolver

## Casos De Uso

- `src\misas\application\PlanDeMisasPantallaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_plantilla.php"]`).
