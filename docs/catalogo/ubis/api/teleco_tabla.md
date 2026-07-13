---
id: "ubis.teleco_tabla"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_tabla"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_tabla.php"
entrada: ["post.obj_pau:string", "post.id_ubi:integer"]
entrada_obligatoria: ["obj_pau", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_tabla.php"]
casos_uso: ["src\\ubis\\application\\TelecoTablaData"]
tags: ["ubis", "teleco", "tabla"]
estado_revision: "revisado"
errores: []
---

# Teleco Tabla

Lista las telecomunicaciones de un centro o casa con botones según permisos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista las telecomunicaciones de un centro o casa con botones según permisos.

## Endpoint

- URL: `/src/ubis/teleco_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `id_ubi` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `botones`: 1 o 0
  - `dl`: delegación
  - `tit_txt`: título tabla
  - `a_cabeceras`: cabeceras dinámicas
  - `a_valores`: filas teleco
  - `a_botones`: modificar/eliminar si permitido

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

UbiPermisos.puedeModificarPorObjeto: botones y a_botones.

## Casos De Uso

- `src\ubis\application\TelecoTablaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/teleco_tabla.php"]`).
