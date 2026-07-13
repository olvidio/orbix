---
id: "ubis.direcciones_que"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_que"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_que.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DireccionesQueDataData"
respuesta_data: ["tipo_ubi:string|null, titulo: string"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_que.php"]
casos_uso: ["src\\ubis\\application\\DireccionesQueData"]
tags: ["ubis", "direcciones", "que"]
estado_revision: "revisado"
errores: []
---

# Direcciones Que

Prepara el formulario de búsqueda de direcciones existentes para asignar a un ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara el formulario de búsqueda de direcciones existentes para asignar a un ubi.

## Endpoint

- URL: `/src/ubis/direcciones_que`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_que.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `tipo_ubi`: tipo del ubi
  - `titulo`: título formulario búsqueda

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\DireccionesQueData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direcciones_que.php"]`).
