---
id: "actividades.tipo_activ_lista"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivLista"]
tags: ["actividades", "tipo", "activ", "lista"]
estado_revision: "revisado"
---

# Tipo Activ Lista

Devuelve la tabla HTML con los tipos de actividad existentes. Portado del case `lista` del
dispatcher legacy `frontend/actividades/controller/tipo_activ_ajax.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Renderiza el listado (`frontend\shared\web\Lista`) de todos los tipos de actividad ordenados por
`id_tipo_activ`, con las columnas `id_tipo_activ`, nombre del tipo y un enlace "modificar" que dispara
`fnjs_modificar(id_tipo_activ)`. Es el cuerpo del gestor de tipos de actividad (`tipo_activ.php`). No
usa parámetros de entrada: siempre lista todos los tipos.

## Endpoint

- URL: `/src/actividades/tipo_activ_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_lista.php`

## Entrada

Sin parámetros. El caso de uso ignora `$_POST` y lee todos los tipos del repositorio.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con una única clave `html` que contiene la tabla renderizada de tipos de actividad.

## Permisos

- El caso de uso no aplica control de permisos propio. La autorización de oficina se resuelve en el
  frontend (`tipo_activ.php`, que firma la llamada con `HashFront`) y en `$_SESSION['oPerm']`. No
  inferir permisos concretos aquí.

## Casos De Uso

- `src\actividades\application\TipoActivLista`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_lista`, firmada con `HashFront`).
