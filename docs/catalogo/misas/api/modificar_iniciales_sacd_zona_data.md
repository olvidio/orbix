---
id: "misas.modificar_iniciales_sacd_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_iniciales_sacd_zona_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_iniciales_sacd_zona.php"]
casos_uso: ["src\\misas\\application\\ModificarInicialesSacdZonaData"]
tags: ["misas", "modificar", "iniciales", "sacd", "zona", "data"]
estado_revision: "revisado"
errores: []
---

# Modificar iniciales sacd zona Data

Devuelve el desplegable de todas las zonas para la pantalla de edición de iniciales SACD.

Linaje: Slice 3 — migrado desde apps/misas/controller/modificar_iniciales_sacd_zona.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el desplegable de todas las zonas para la pantalla de edición de iniciales SACD.

## Endpoint

- URL: `/src/misas/modificar_iniciales_sacd_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_opciones`: array<int|string, string>

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\ModificarInicialesSacdZonaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/modificar_iniciales_sacd_zona.php"]`).
