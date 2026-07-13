---
id: "actividadescentro.centro_encargado_asignar"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centro_encargado_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_asignar.php"
entrada: ["post.id_activ:integer", "post.id_ubi:integer"]
entrada_obligatoria: ["id_activ", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_ubi", "hay un error, no se ha guardado el centro encargado"]
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoAsignar"]
tags: ["actividadescentro", "centro", "encargado", "asignar"]
estado_revision: "revisado"
---

# Centro Encargado Asignar

Asigna un nuevo `CentroEncargado` (centro `id_ubi`) a una actividad (`id_activ`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `asignar` del dispatcher legacy `activ_ctr_ajax.php`. Calcula
`num_orden = max(num_orden) + 1` (consultando los centros ya asignados con `_ordre = num_orden DESC`,
o `1` si no hay ninguno) para dejar el nuevo centro al final del listado, fija `encargo = 'organizador'`
y guarda el `CentroEncargado`.

## Endpoint

- URL: `/src/actividadescentro/centro_encargado_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Debe ser `> 0` |
| `id_ubi` | `integer` | application | Si | Centro a asignar; debe ser `> 0` |

El controller pasa `$_POST` completo al caso de uso, que lee `id_activ` / `id_ubi` con `inputInt`.

## Salida

- Helper: `ContestarJson::enviar` (el controller pasa `'ok'` como data literal).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.
- En error de negocio: `success: false`, `mensaje` con el texto devuelto por el caso de uso.

## Efectos colaterales

- Crea un `CentroEncargado` `{id_activ, id_ubi, num_orden, encargo: 'organizador'}`.

## Errores conocidos

- `faltan parametros id_activ / id_ubi`
- `hay un error, no se ha guardado el centro encargado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend
  (la acción solo se ofrece si la fila del listado trae `perm_crear_ctr`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadescentro\application\CentroEncargadoAsignar`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_asignar_ctr` invoca este endpoint (URL firmada `url_asignar`) al elegir un centro candidato y,
  tras el éxito, refresca la celda con `fnjs_actualizar_activ`.
