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
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoEliminar"]
tags: ["actividadescentro", "centro", "encargado", "eliminar"]
estado_revision: "revisado"
---

# Centro Encargado Eliminar

Elimina un `CentroEncargado` (`{id_activ, id_ubi}`) del listado de centros encargados de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `orden` con `num_orden = 'borrar'` del dispatcher legacy `activ_ctr_ajax.php`.
Valida que `id_activ` e `id_ubi` sean `> 0`, busca el `CentroEncargado` con `findById` y lo elimina.

## Endpoint

- URL: `/src/actividadescentro/centro_encargado_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Debe ser `> 0` |
| `id_ubi` | `integer` | application | Si | Centro a eliminar; debe ser `> 0` |

El controller pasa `$_POST` completo al caso de uso, que lee `id_activ` / `id_ubi` con `inputInt`.

## Salida

- Helper: `ContestarJson::enviar` (el controller pasa `'ok'` como data literal).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.
- En error de negocio: `success: false`, `mensaje` con el texto devuelto por el caso de uso.

## Efectos colaterales

- Elimina un `CentroEncargado` (`{id_activ, id_ubi}`) del listado de centros encargados de una actividad.

## Errores conocidos

- `no se sabe cual borrar`
- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la
  acción solo se ofrece si `permite_modificar` / `perm_modificar_ctr`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadescentro\application\CentroEncargadoEliminar`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_eliminar` invoca este endpoint (URL firmada `url_eliminar`) desde el popup `borrar` y refresca
  la celda con `fnjs_actualizar_activ`.
