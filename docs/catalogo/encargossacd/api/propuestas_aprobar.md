---
id: "encargossacd.propuestas_aprobar"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/propuestas_aprobar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/propuestas_aprobar.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_PropuestasAprobarData"
respuesta_data: ["text:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/propuestas_aprobar.php", "frontend/encargossacd/controller/propuestas_menu.php"]
casos_uso: ["src\\encargossacd\\application\\PropuestasAprobar", "src\\encargossacd\\application\\services\\PropuestasAprobarService"]
errores: []
tags: ["encargossacd", "propuestas", "aprobar"]
estado_revision: "revisado"
---
# Propuestas Aprobar

Aplica las propuestas staging a tablas reales (`encargos_sacd` / `encargo_sacd_horario`) y elimina
las tablas staging. Invocado desde el menú de propuestas tras confirmación del usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recorre todas las filas de `propuesta_encargo_sacd` y, por cada una:

| Condición | Acción |
|-----------|--------|
| `id_nom_new` vacío y `id_nom` > 0 | Cierra encargo real (`f_fin` = hoy) y horarios staging. |
| `id_nom` vacío y `id_nom_new` > 0 | Alta de encargo SACD + horarios desde staging. |
| Mismo SACD (`id_nom === id_nom_new`) | Solo actualiza/crea horarios si cambió dedicación. |
| SACD distinto | Cierra el actual y da de alta el nuevo. |

Al final: `DBPropuestas::eliminarAll()` (borra staging). Devuelve texto «Hecho!».

## Endpoint

- URL: `/src/encargossacd/propuestas_aprobar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/propuestas_aprobar.php`

## Entrada

Sin parámetros; opera sobre todo el staging.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves: `text` (traducible `_('Hecho!')`).
- FE `propuestas_aprobar.php` imprime solo ese string en `#main`.

## Errores conocidos

Ningún mensaje `_()` de fallo en el caso de uso; fallos de repositorio no se propagan como
`mensaje` al cliente. Pendiente: comportamiento si no existen tablas staging.

## Efectos colaterales

- Escribe en `encargos_sacd` y `encargo_sacd_horario` (producción).
- Elimina tablas staging de propuestas (irreversible desde UI).

## Permisos

Sin control propio; acceso vía menú Encargos > propuestas + confirm JS. Pendiente: confirmar
restricción `des`/`vcsd` u oficina.

## Casos De Uso

- `src\encargossacd\application\PropuestasAprobar`
- `src\encargossacd\application\services\PropuestasAprobarService`

## Frontend Relacionado

- `frontend/encargossacd/controller/propuestas_aprobar.php`
- `frontend/encargossacd/controller/propuestas_menu.php` (`fnjs_aprobar_propuestas`)

## Notas

- Relación pantallas↔API: no está en `relaciones/pantallas_api.md` (índice generado previo).
