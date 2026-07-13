---
id: "procesos.fases_activ_cambio_tipo_html"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_tipo_html"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_tipo_html.php"
entrada: ["post.id_tipo_activ:string", "post.sactividad:string", "post.sactividad2:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioTipoActividadHtmlData"]
tags: ["procesos", "fases", "activ", "cambio", "tipo", "html"]
estado_revision: "revisado"
---

# Fases Activ Cambio Tipo Html

Fragmento HTML del selector de tipo de actividad en `fases_activ_cambio`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Renderiza el widget de búsqueda de tipo de actividad (`ActividadTipo`) para la pantalla de cambio
masivo de fases. El modo extendido se activa si `sactividad2` no viene vacío. El alcance de tipos
visibles depende de permisos de oficina (`vcsd`, `des`, `calendario`) o del SFSV de sesión.

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_tipo_html`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_tipo_html.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | application | No | Tipo preseleccionado |
| `sasistentes` | `string` | application | No | Filtro asistentes del widget |
| `sactividad` | `string` | application | No | Filtro actividad |
| `sactividad2` | `string` | application | No | No vacío → modo extendido |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `tipo_actividad_html` (`string`): HTML del selector

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- No llama a `perm_*` explícito; restringe tipos visibles según oficinas `vcsd`/`des`/`calendario`
  en `$_SESSION['oPerm']`, o limita a SV/SF de la sesión si no tiene esos permisos.

## Casos De Uso

- `src\procesos\application\FasesActivCambioTipoActividadHtmlData`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php` (carga inicial vía `PostRequest::getDataFromUrl`)
