---
id: "actividadescentro.activ_ctr_shell_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/activ_ctr_shell_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/activ_ctr_shell_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_ActivCtrShellDataData"
respuesta_data: ["tipo:string", "url_lista:array", "url_encargados:array", "url_disponibles:array", "url_asignar:array", "url_reordenar:array", "url_eliminar:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\ActivCtrShellData"]
tags: ["actividadescentro", "activ", "ctr", "shell", "data"]
estado_revision: "generado"
---

# Activ Ctr Shell Data

Tipo resuelto y especificaciones de URL para la shell de `activ_ctr` (sin `HashFront` en `src/`). La firma `linkSinVal` se aplica en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/activ_ctr_shell_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/activ_ctr_shell_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadescentro_ActivCtrShellDataData`):
  - `tipo` (`string`)
  - `url_lista` (`array`)
  - `url_encargados` (`array`)
  - `url_disponibles` (`array`)
  - `url_asignar` (`array`)
  - `url_reordenar` (`array`)
  - `url_eliminar` (`array`)

## Casos De Uso

- `src\actividadescentro\application\ActivCtrShellData`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.