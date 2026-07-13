---
id: "procesos.tipo_activ_proceso_lista"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso_lista.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLista"]
tags: ["procesos", "tipo", "activ", "proceso", "lista"]
estado_revision: "revisado"
---

# Tipo Activ Proceso Lista

Listado de tipos de actividad con proceso propio y no propio asignados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve todas los tipos de actividad con el nombre del proceso asignado para delegación propia
y para actividades ajenas (SFSV de sesión). Incluye cabeceras traducidas para la tabla.

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php`

## Entrada

Sin parámetros POST; el caso de uso no lee entrada.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras` (`list<string>`)
  - `a_tipos` (`list`): cada fila con `id_tipo_activ`, `nom`, `id_tipo_proceso`,
    `nom_proceso_propio`, `id_tipo_proceso_ex`, `nom_proceso_no_propio` (`----` si sin asignar)

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\TipoActivProcesoLista`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso_lista.php` (renderer HTML; invocado desde `url_lista`)
