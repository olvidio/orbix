---
id: "procesos.fases_activ_cambio_lista"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php"
entrada: ["post.accion:string", "post.dl_propia:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_fase_nueva:string", "post.id_tipo_activ:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: ["id_fase_nueva"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Debe poner la fase nueva"]
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio_lista.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioLista"]
tags: ["procesos", "fases", "activ", "cambio", "lista"]
estado_revision: "revisado"
---

# Fases Activ Cambio Lista

Datos estructurados para la tabla de actividades candidatas a cambiar de fase.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filtra actividades por tipo, delegación propia/ajena, periodo y fechas; evalúa si cada una puede
marcar o desmarcar la fase nueva (`accion` = `marcar` o `desmarcar`) según requisitos de fases
previas. Devuelve cabeceras, filas y la lista `select` de `id_activ` elegibles.

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_fase_nueva` | `string` | application | Si | Fase destino; vacío → error |
| `id_tipo_activ` | `string` | application | No | `......` = todos los tipos |
| `dl_propia` | `string` | application | No | `t`/`f`; DL propia vs ajena |
| `periodo` | `string` | application | No | Por defecto `actual` |
| `year` | `string` | application | No | Año del periodo |
| `empiezamin` | `string` | application | No | Filtro fecha inicio (periodo `otro`) |
| `empiezamax` | `string` | application | No | Filtro fecha fin (periodo `otro`) |
| `accion` | `string` | application | No | `marcar` o `desmarcar` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `error` (`string`)
  - `msg` (`string`): resumen «N actividades, M para cambiar»
  - `num_activ`, `num_ok` (`int`)
  - `accion`, `id_fase_nueva` (`string`): eco de entrada
  - `a_cabeceras` (`list<string>`): `nom`, `cumple requisito`
  - `a_valores` (`array`): filas indexadas; cada fila con `sel` (`id_activ`), columnas `1`/`2`,
    opcional `clase`; clave `select` con lista de `id_activ` elegibles

## Errores conocidos

- `Debe poner la fase nueva` (en `data.error`)

## Permisos

- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\FasesActivCambioLista`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio_lista.php` (renderer HTML; invocado desde `url_lista`)
