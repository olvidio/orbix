---
id: "actividades.tipo_activ_form_nuevo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_form_nuevo"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_nuevo.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormNuevo"]
tags: ["actividades", "tipo", "activ", "form", "nuevo"]
estado_revision: "revisado"
---

# Tipo Activ Form Nuevo

Devuelve el HTML del formulario para crear un nuevo tipo de actividad. Portado del case `form_nuevo`
del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Compone el formulario de alta de tipo de actividad. Renderiza el selector de composición del código
(`ActividadTipo` en modo gestión, con `perm_jefe`) más los inputs `id_nom_tipo_activ` (dígito final del
id) y `nom_tipo_activ` (nombre), e incluye la cápsula HashB de alta
(`TipoActivGestionFormHashCompose::nuevoHiddenHtml`). El submit llama a `fnjs_guardar_nuevo`.

## Endpoint

- URL: `/src/actividades/tipo_activ_form_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_nuevo.php`

## Entrada

Sin parámetros. El caso de uso no lee `$_POST`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con una única clave `html` que contiene el formulario de alta renderizado.

## Permisos

- El formulario se construye con `perm_jefe(true)`. El control de acceso real se resuelve en el
  frontend (`tipo_activ.php`, firma `HashFront`) y en `$_SESSION['oPerm']`; no inferir permisos aquí.

## Casos De Uso

- `src\actividades\application\TipoActivFormNuevo`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_form_nuevo`, firmada con `HashFront`).
