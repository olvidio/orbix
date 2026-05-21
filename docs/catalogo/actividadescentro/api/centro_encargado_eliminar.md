---
id: "actividadescentro.centro_encargado_eliminar"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centro_encargado_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_eliminar.php"
entrada: ["post.id_activ:integer", "post.id_ubi:integer"]
entrada_obligatoria: ["id_activ", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se sabe cual borrar", "el centro encargado ya no existe", "hay un error, no se ha eliminado el centro"]
frontend_referencias: []
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoEliminar"]
tags: ["actividadescentro", "centro", "encargado", "eliminar"]
estado_revision: "generado"
---

# Centro Encargado Eliminar

Endpoint backend: elimina un CentroEncargado de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/centro_encargado_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | application |
| `id_ubi` | `integer` | application | Si | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `CentroEncargado` ({id_activ, id_ubi}) del listado de centros encargados de una actividad.

## Errores conocidos

- `no se sabe cual borrar`
- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`

## Casos De Uso

- `src\actividadescentro\application\CentroEncargadoEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.