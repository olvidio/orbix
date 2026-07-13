---
id: "actividades.lista_sr_csv_que_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_sr_csv_que_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/lista_sr_csv_que.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvQueDatos"]
tags: ["actividades", "lista", "sr", "csv", "que", "datos"]
estado_revision: "revisado"
---

# Lista Sr Csv Que Datos

Devuelve los valores por defecto del formulario de búsqueda `lista_sr_csv_que`, a partir de la
preferencia guardada del usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee la preferencia `busqueda_activ_sr` del usuario y la traduce a valores de formulario. Si no hay
preferencia (o es inválida), aplica defaults: status `[1,2]`, periodo `curso_ca`, tipo_activ `[1,3]`,
sin ubis. Devuelve el periodo, la lista de ubis compartidas y los flags `checked` de los checkboxes de
status (1/2) y de actividad (crt/cv).

## Endpoint

- URL: `/src/actividades/lista_sr_csv_que_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php`

## Entrada

Sin parámetros. El caso de uso lee la preferencia del usuario en sesión.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` con claves: `periodo`, `sel_ubis`, `chk_status_1`, `chk_status_2`, `chk_activ_crt`,
  `chk_activ_cv` (los `chk_*` valen `checked` o cadena vacía).

## Permisos

- Sin control de permisos propio; solo lee la preferencia del usuario actual. La autorización se
  resuelve en el frontend.

## Casos De Uso

- `src\actividades\application\ListaSrCsvQueDatos`

## Frontend Relacionado

- `frontend/actividades/controller/lista_sr_csv_que.php` (formulario de filtros del listado SR).
