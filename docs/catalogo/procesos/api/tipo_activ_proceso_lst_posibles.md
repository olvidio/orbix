---
id: "procesos.tipo_activ_proceso_lst_posibles"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_lst_posibles"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php"
entrada: ["post.id_tipo_activ:integer", "post.propio:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso.php", "frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLstPosibles"]
tags: ["procesos", "tipo", "activ", "proceso", "lst", "posibles"]
estado_revision: "revisado"
---

# Tipo Activ Proceso Lst Posibles

Procesos posibles asignables a un tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista los tipos de proceso del SFSV de sesión ordenados por nombre, para elegir cuál asignar al
tipo de actividad indicado (contexto propio/no propio en `propio`).

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_lst_posibles`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | Si | Tipo de actividad destino |
| `propio` | `string` | application | No | `t`/`f`; eco en respuesta |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `id_tipo_activ` (`int`)
  - `propio` (`string`)
  - `a_procesos` (`list`): cada elemento con `id_tipo_proceso`, `nom_proceso`

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\TipoActivProcesoLstPosibles`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php` (mini-tabla HTML clickable)
- `frontend/procesos/controller/tipo_activ_proceso.php` (URL en `url_lst_posibles`)
