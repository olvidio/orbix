---
id: "devel_db_admin.migraciones_quitar_registro"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/migraciones_quitar_registro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_quitar_registro.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No hay migraciones seleccionadas.", "Ninguna migracion seleccionada tenia registro en migracion_aplicada.", "No se elimino ningun registro."]
frontend_referencias: ["frontend/devel_db_admin/controller/migraciones_lista.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesQuitarRegistro"]
tags: ["devel_db_admin", "migraciones", "quitar", "registro"]
estado_revision: "revisado"
---

# Migraciones Quitar Registro

Elimina registros de `migracion_aplicada` para permitir re-ejecutar migraciones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe ids en `sel[]` (mismo formato que la lista). Por cada migración encontrada en disco, borra
filas aplicadas en el repositorio. No ejecuta SQL; solo limpia el historial.

## Endpoint

- URL: `/src/devel_db_admin/migraciones_quitar_registro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_quitar_registro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller | Si | Ids de migración |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload: `{ "lines": list<string>, "error": string|null }`.

## Errores conocidos

- `No hay migraciones seleccionadas.`
- `Ninguna migracion seleccionada tenia registro en migracion_aplicada.`
- `No se elimino ningun registro.`

## Permisos

- Sin control propio; acción desde `migraciones_lista`.

## Casos De Uso

- `src\devel_db_admin\application\MigracionesQuitarRegistro`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/migraciones_lista.php`
