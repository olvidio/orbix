---
id: "configuracion.periodo_calendario_escolar_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/periodo_calendario_escolar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/configuracion/infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "configuracion_PeriodoCalendarioEscolarDataData"
respuesta_data: ["mes_fin_stgr:integer", "mes_fin_crt:integer", "dia_ini_stgr:integer", "mes_ini_stgr:integer", "dia_fin_stgr:integer", "dia_ini_crt:integer", "mes_ini_crt:integer", "dia_fin_crt:integer", "any_final_est:integer", "any_final_crt:integer"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/shared/web/Periodo.php"]
casos_uso: ["src\\configuracion\\application\\PeriodoCalendarioEscolarData"]
tags: ["configuracion", "periodo", "calendario", "escolar", "data"]
estado_revision: "revisado"
---

# Periodo Calendario Escolar Data

Devuelve las fechas y metadatos del curso (STGR / CRT) que antes solo vivían en
`$_SESSION['oConfig']`, para inyectarlos en el objeto `Periodo` del frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Expone el snapshot de configuración de calendario escolar. El caso de uso
`PeriodoCalendarioEscolarData::execute()` no recibe parámetros: reutiliza el
`ConfigSnapshot` de `$_SESSION['oConfig']` si existe, o lo reconstruye con
`ObtenerConfigSnapshot`. A partir del snapshot calcula los días/meses de inicio y fin
de los cursos STGR y CRT y los años finales (`any_final_curs('est')` / `('crt')`).

## Endpoint

- URL: `/src/configuracion/periodo_calendario_escolar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php`

## Entrada

Sin parámetros POST: el caso de uso lee la sesión (`$_SESSION['oConfig']`) o la
configuración persistida.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload de configuración (schema `configuracion_PeriodoCalendarioEscolarDataData`), con claves:
  - `mes_fin_stgr` (`integer`)
  - `mes_fin_crt` (`integer`)
  - `dia_ini_stgr` (`integer`)
  - `mes_ini_stgr` (`integer`)
  - `dia_fin_stgr` (`integer`)
  - `dia_ini_crt` (`integer`)
  - `mes_ini_crt` (`integer`)
  - `dia_fin_crt` (`integer`)
  - `any_final_est` (`integer`)
  - `any_final_crt` (`integer`)
- En error inesperado: el controller captura la excepción y responde `success: false` con el mensaje (`data: "none"`).

## Permisos

- El caso de uso no aplica control de permisos propio: se limita a leer configuración.
  La autorización de oficina se resuelve en el frontend y en `$_SESSION['oPerm']`. No
  inferir permisos concretos aquí.

## Casos De Uso

- `src\configuracion\application\PeriodoCalendarioEscolarData`

## Frontend Relacionado

- `frontend/shared/web/Periodo.php`
