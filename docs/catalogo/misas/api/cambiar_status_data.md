---
id: "misas.cambiar_status_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/cambiar_status_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_CambiarStatusPantallaDataData"
respuesta_data: ["zonas_opciones:array", "orden_opciones:array", "estados_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\CambiarStatusPantallaData"]
tags: ["misas", "cambiar", "status", "data"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "No tiene permiso para ver esta página"]
---

# Cambiar status Data

Carga los desplegables de la pantalla cambiar estado del plan de misas: zonas permitidas, criterios de orden y estados posibles.

Linaje: Slice 10 — migrado desde apps/misas/controller/cambiar_status.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga los desplegables de la pantalla cambiar estado del plan de misas: zonas permitidas, criterios de orden y estados posibles.

## Endpoint

- URL: `/src/misas/cambiar_status_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `zonas_opciones`: array<int|string, string>
  - `orden_opciones`: array<string, string>
  - `estados_opciones`: array<int, string>

## Errores conocidos
- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Permisos

IdNomJefeResolver: p-sacd no jefe calendario sin id_pau → error

## Casos De Uso

- `src\misas\application\CambiarStatusPantallaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/cambiar_status.php"]`).
