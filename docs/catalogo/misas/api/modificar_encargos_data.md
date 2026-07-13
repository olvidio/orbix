---
id: "misas.modificar_encargos_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ModificarEncargosDataData"
respuesta_data: ["error:string, a_opciones_zona: array<int|string, string>, a_orden: array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosData"]
tags: ["misas", "modificar", "encargos", "data"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "No tiene permiso para ver esta página", "orden", "prioridad", "alfabético"]
---

# Modificar encargos Data

Devuelve zonas permitidas y criterios de orden para la pantalla modificar encargos de zona.

Linaje: Slice 4 — migrado desde apps/misas/controller/modificar_encargos.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve zonas permitidas y criterios de orden para la pantalla modificar encargos de zona.

## Endpoint

- URL: `/src/misas/modificar_encargos_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_opciones_zona`: array<int|string, string>
  - `a_orden`: array<string, string>

## Errores conocidos
- `Usuario no encontrado`
- `No tiene permiso para ver esta página`
- `orden`
- `prioridad`
- `alfabético`

## Permisos

IdNomJefeResolver: p-sacd no jefe calendario filtra zonas por id_pau

## Casos De Uso

- `src\misas\application\ModificarEncargosData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_encargos.php"]`).
