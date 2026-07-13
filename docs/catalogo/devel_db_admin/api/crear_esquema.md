---
id: "devel_db_admin.crear_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/crear_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/crear_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["esquema", "region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Esquema de referencia no válido.", "No se puede crear «%s»: el esquema destino ya existe en alguna base (intento anterior del paso 2 o alta duplicada). Los roles del paso «crear usuarios» no impiden continuar.", "No se puede crear: falta el esquema de referencia «%s» en:", "Aviso: no se puede crear la estructura de esquemas para «%s». Primero ejecute el paso «1º crear usuarios» (misma región y delegación) y, si hace falta, copie las entradas en los ficheros .inc que indica ese paso."]
frontend_referencias: ["frontend/devel_db_admin/controller/db_crear_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearEsquema", "src\\devel_db_admin\\application\\ComprobarPrecondicionesCrearEsquema", "src\\devel_db_admin\\application\\CrearEsquemaPrecondicionException"]
tags: ["devel_db_admin", "crear", "esquema"]
estado_revision: "revisado"
---

# Crear Esquema

Crea esquemas PostgreSQL (comun / sv / sv-e / sf) clonando la estructura de un esquema de referencia.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Paso 2 del asistente «nuevo esquema». Valida precondiciones (`ComprobarPrecondicionesCrearEsquema`:
destino libre, referencia existente, roles creados en paso 1). Por cada bloque marcado crea rol,
schema y tablas vía `DBEsquemaCreate`, réplicas `*_select` fuera de Docker, y registra en
`db_idschema`. Devuelve avisos no bloqueantes (p. ej. réplica).

## Endpoint

- URL: `/src/devel_db_admin/crear_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema` | `string` | controller | Si | Esquema referencia (base) |
| `region` | `string` | controller | Si | Región destino |
| `dl` | `string` | controller | Si | Delegación destino |
| `comun`, `sv`, `sf` | `integer` | controller | No | Bloques a crear |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `{ "ok": true, "avisos": list<string> }`.
- Precondición: `{ "ok": false, "avisos": [mensaje] }` (`CrearEsquemaPrecondicionException`).
- Otros errores: `success: false`, `mensaje`.

## Errores conocidos

- `Esquema de referencia no válido.`
- Mensajes de `ComprobarPrecondicionesCrearEsquema` (destino ocupado, falta referencia, faltan roles)

## Permisos

- Sin control propio; menú `sistema > DB > nuevo esquema`.

## Casos De Uso

- `src\devel_db_admin\application\CrearEsquema`
- `src\devel_db_admin\application\ComprobarPrecondicionesCrearEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_crear_esquema.php`
