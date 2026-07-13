---
id: "procesos.procesos_clonar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_clonar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_clonar.php"
entrada: ["post.id_tipo_proceso:integer", "post.id_tipo_proceso_ref:integer"]
entrada_obligatoria: ["id_tipo_proceso", "id_tipo_proceso_ref"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se ha indicado el proceso a clonar"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosClonar"]
tags: ["procesos", "clonar"]
estado_revision: "revisado"
---

# Procesos Clonar

Clona las tareas de un proceso de referencia al proceso indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina todas las `tareas_proceso` del proceso destino (`id_tipo_proceso`) y copia las del proceso
referencia (`id_tipo_proceso_ref`), asignando nuevos `id_item`.

## Endpoint

- URL: `/src/procesos/procesos_clonar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_clonar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | Si | Proceso destino (> 0) |
| `id_tipo_proceso_ref` | `integer` | application | Si | Proceso origen a copiar (> 0) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se ha indicado el proceso a clonar`

## Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosClonar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php` (URL emitida como `url_clonar`)
