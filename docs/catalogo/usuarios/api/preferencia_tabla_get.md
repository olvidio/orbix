---
id: "usuarios.preferencia_tabla_get"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/preferencia_tabla_get"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php"
entrada: ["post.id_tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_PreferenciaTablaDataData"
respuesta_data: ["formato_tabla:string, slickgrid: array<string, mixed>|null"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/Lista.php", "frontend/shared/web/TablaEditable.php"]
casos_uso: ["src\\usuarios\\application\\PreferenciaTablaData"]
tags: ["usuarios", "preferencia", "tabla", "get"]
estado_revision: "revisado"
errores: []
---

# Preferencia Tabla Get

Devuelve preferencias de presentación de tablas (global y SlickGrid por id_tabla+idioma).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve preferencias de presentación de tablas (global y SlickGrid por id_tabla+idioma).

## Endpoint

- URL: `/src/usuarios/preferencia_tabla_get`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tabla` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `formato_tabla`: html|slickgrid
  - `slickgrid`: prefs JSON o null

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Usuario autenticado (`ConfigGlobal::mi_id_usuario()`).

## Casos De Uso

- `src\usuarios\application\PreferenciaTablaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/shared/web/Lista.php", "frontend/shared/web/TablaEditable.php"]`).
