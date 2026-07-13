---
id: "misas.modificar_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ModificarEncargosCentrosDataData"
respuesta_data: ["error:string, a_opciones_zona: array<int|string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosCentrosData"]
tags: ["misas", "modificar", "encargos", "centros", "data"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "No tiene permiso para ver esta página"]
---

# Modificar encargos centros Data

Devuelve el desplegable de zonas permitidas para la pantalla modificar encargos de centros.

Linaje: Slice 5 — migrado desde apps/misas/controller/modificar_encargos_centros.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el desplegable de zonas permitidas para la pantalla modificar encargos de centros.

## Endpoint

- URL: `/src/misas/modificar_encargos_centros_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_opciones_zona`: array<int|string, string>

## Errores conocidos
- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Permisos

IdNomJefeResolver: p-sacd no jefe calendario filtra zonas por id_pau

## Casos De Uso

- `src\misas\application\ModificarEncargosCentrosData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_encargos_centros.php"]`).
