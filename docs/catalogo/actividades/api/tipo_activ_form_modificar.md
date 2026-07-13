---
id: "actividades.tipo_activ_form_modificar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_form_modificar"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar.php"
entrada: ["post.id_tipo_activ:integer"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormModificar"]
tags: ["actividades", "tipo", "activ", "form", "modificar"]
estado_revision: "revisado"
---

# Tipo Activ Form Modificar

Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad existente. Portado del
case `form_modificar` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

A partir de `id_tipo_activ` compone la cabecera legible del tipo (sfsv + asistentes + actividad) y un
input `nom_tipo_activ` precargado con el nombre actual, más la cápsula HashB de modificación
(`TipoActivGestionFormHashCompose::modificarHiddenHtml`). Ofrece dos acciones: `fnjs_guardar(...,'update')`
y `fnjs_guardar(...,'eliminar')`.

## Endpoint

- URL: `/src/actividades/tipo_activ_form_modificar`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | Sí | Tipo a editar; leído con `inputInt` |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con una única clave `html` que contiene el formulario de edición renderizado.

## Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend
  (`tipo_activ.php`, firma `HashFront` sobre el campo `id_tipo_activ`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\TipoActivFormModificar`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_form_modificar`, firmada con `HashFront`).
