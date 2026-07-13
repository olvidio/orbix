---
id: "actividadescentro.lista_actividades_ctr_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/lista_actividades_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/lista_actividades_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.tipo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_ListaActividadesCtrDataData"
respuesta_data: ["titulo:string", "tipo:string", "inicio_iso:string", "fin_iso:string", "filas:list<array<string, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\ListaActividadesCtrData"]
tags: ["actividadescentro", "lista", "actividades", "ctr", "data"]
estado_revision: "revisado"
---

# Lista Actividades Ctr Data

Builder de la tabla principal de `actividadescentro/activ_ctr`: devuelve las actividades del tipo +
periodo elegidos y, para cada una, sus centros encargados y los flags de permiso (modificar / crear
centros) que el frontend usa para decidir cómo renderizar cada celda.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `lista_activ` del dispatcher legacy `apps/actividadescentro/controller/activ_ctr_ajax.php`.
A partir del `tipo` y el periodo:

- Resuelve el rango de fechas con `frontend\shared\web\Periodo` (`periodo` por defecto `actual`,
  `year` con `setDefaultAny('next')`, `empiezamin`/`empiezamax`).
- Filtra las actividades con `status < 3` y `f_ini BETWEEN [inicio, fin]`, aplicando además un regex
  sobre `id_tipo_activ` según `tipo` (`sg`→`^1[45]`, `sr`→`^17`, `nagd`→`^1[13]`, `sssc`→`^16`,
  `sfsg`→`^2[45]`, `sfsr`→`^27`, `sfnagd`→`^2[123]`; sin `tipo` no filtra).
- Descarta filas donde el usuario no tiene permiso `ocupado` o `ver` sobre la actividad.
- Solo carga los centros encargados de la actividad si tiene permiso `ver` en la faceta `ctr`.
- Ordena las filas por fecha de inicio y nombre de centro de la actividad.

## Endpoint

- URL: `/src/actividadescentro/lista_actividades_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/lista_actividades_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller+application | No | Colectivo (`sg`/`sr`/`nagd`/`sssc`/`sfsg`/`sfsr`/`sfnagd`); vacío = sin filtro de tipo |
| `year` | `string` | controller+application | No | Año del periodo (`Periodo::setAny`) |
| `periodo` | `string` | controller+application | No | Por defecto `actual` |
| `empiezamin` | `string` | controller+application | No | Fecha ISO mínima de inicio |
| `empiezamax` | `string` | controller+application | No | Fecha ISO máxima de inicio |

El controller construye el `$input` con los cinco campos vía `FuncTablasSupport::inputString`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadescentro_ListaActividadesCtrDataData`):
  - `titulo` (`string`): `sprintf(_("listado de actividades %s"), $tipo)`.
  - `tipo` (`string`): tipo recibido.
  - `inicio_iso` / `fin_iso` (`string`): rango ISO resuelto por `Periodo`.
  - `filas` (`list<array<string, mixed>>`): una entrada por actividad visible con las claves
    `id_activ` (int), `nom_activ` (string), `f_ini` / `f_fin` (fecha local), `perm_modificar_ctr`
    (bool), `perm_crear_ctr` (bool) y `centros` (`list<array{id_ubi:int, nombre_ubi:string}>`).

## Permisos

- Se apoya en `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando `procesos` está instalado;
  en caso contrario usa `PermisosActividadesTrue`.
- Por actividad exige `have_perm_activ('ocupado')` y `have_perm_activ('ver')` en la faceta `datos`;
  los centros solo se listan si la faceta `ctr` tiene `ver`. `perm_modificar_ctr` / `perm_crear_ctr`
  reflejan `modificar` / `crear` de la faceta `ctr`.

## Casos De Uso

- `src\actividadescentro\application\ListaActividadesCtrData`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_ver()` invoca este endpoint (URL firmada `url_lista` emitida por `activ_ctr_shell_data`) y
  construye la tabla en cliente con `fnjs_construir_tabla_lista`.
