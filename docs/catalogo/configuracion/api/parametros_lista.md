---
id: "configuracion.parametros_lista"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/parametros_lista"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/configuracion/infrastructure/ui/http/controllers/parametros_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/configuracion/controller/parametros.php"]
casos_uso: []
tags: ["configuracion", "parametros", "lista"]
estado_revision: "revisado"
---

# Parametros Lista

Devuelve los valores actuales de todos los parámetros de configuración para pintar el
formulario de parámetros.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Aunque su nombre sea `lista`, no produce datos tabulares: reúne los valores de cada
parámetro de configuración (`ConfigSchemaRepositoryInterface::findById`) y los devuelve
como payload para poblar el formulario de la pantalla de parámetros. La lógica vive en el
propio controller (no hay caso de uso de `application/`). Para cada parámetro aplica un
valor por defecto cuando no está guardado (p. ej. periodos de curso, `nota_corte = 0.6`,
`nota_max = 10`, `ambito = dl`, `gesCalendario = central`) y añade el catálogo de locales
para el desplegable de idioma.

## Endpoint

- URL: `/src/configuracion/parametros_lista`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/parametros_lista.php`

## Entrada

Sin parámetros POST: el controller lee toda la configuración persistida.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload de configuración, con claves:
  - `aCursoCrt`, `aCursoStgr`: arrays `{ini_dia, ini_mes, fin_dia, fin_mes}` de cada periodo de curso.
  - `jefe_calendario`, `ce_lugar`, `region_latin`, `vstgr`, `lugar_firma`, `dir_stgr`: cadenas de texto de configuración.
  - `nota_corte`, `nota_max`, `caduca_cursada`, `ini_contador_certificados`: valores numéricos/textuales.
  - `a_locales`: catálogo de locales disponibles; `idioma_select`: locale seleccionado.
  - `chk_dl`, `chk_r`, `chk_rstgr`: flags `checked` del ámbito (`ambito`).
  - `chk_central`, `chk_of`: flags `checked` de la gestión de calendario (`gesCalendario`).

## Permisos

- No hay control de permisos propio en el controller; la autorización de oficina se
  resuelve en el frontend (`parametros.php`) y en `$_SESSION['oPerm']`. No inferir
  permisos concretos aquí.

## Casos De Uso

- No usa capa `application/`: la lógica está en el controller, apoyada en
  `ConfigSchemaRepositoryInterface` y `LocalRepositoryInterface`.

## Frontend Relacionado

- `frontend/configuracion/controller/parametros.php`
