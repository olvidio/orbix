---
id: "procesos.tipo_activ_proceso_asignar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php"
entrada: ["post.id_tipo_activ:integer", "post.id_tipo_proceso:integer", "post.propio:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["tipo de actividad no encontrado", "hay un error, no se ha guardado el proceso"]
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoAsignar"]
tags: ["procesos", "tipo", "activ", "proceso", "asignar"]
estado_revision: "revisado"
---

# Tipo Activ Proceso Asignar

Asigna un proceso tipo a un tipo de actividad (propio o no propio).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Vincula `id_tipo_proceso` al `TipoDeActividad` indicado. Si `propio` es verdadero actualiza el
proceso de delegación propia (`setId_tipo_proceso`); si no, el de actividades ajenas
(`setId_tipo_proceso_ex`), ambos para el SFSV de sesión.

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | Si | Tipo de actividad destino |
| `id_tipo_proceso` | `integer` | application | No | Proceso a asignar (0 = quitar) |
| `propio` | `string` | application | No | `t`/`f`; propio vs no propio |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `tipo de actividad no encontrado`
- `hay un error, no se ha guardado el proceso`

## Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\TipoActivProcesoAsignar`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso.php` (URL emitida como `url_asignar`)
