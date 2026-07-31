---
id: "encargossacd.opciones_seccion_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/opciones_seccion_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/opciones_seccion_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_OpcionesSeccionDataData"
respuesta_data: ["opciones:array<string,string>"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/propuestas_lista.php"]
casos_uso: ["src\\encargossacd\\application\\OpcionesSeccionData"]
errores: []
tags: ["encargossacd", "opciones", "seccion", "data", "propuestas"]
estado_revision: "revisado"
---
# Opciones Seccion Data

Opciones del desplegable «grupo de ctrs» (`getArraySeccion`) para pantallas frontend sin
resolver DI en el árbol `frontend/`. Usado por la lista editable de propuestas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el mapa id→etiqueta de sección de centros:

| Clave | Etiqueta |
|-------|----------|
| `1` | sv |
| `2` | sf |
| `3` | sss+ |
| `4` | igl |
| `5` | cgi/oc |
| `8` | zonas |

Si el usuario **no** tiene `have_perm_oficina('des')` ni `have_perm_oficina('vcsd')`, se
elimina la clave `2` (sf).

## Endpoint

- URL: `/src/encargossacd/opciones_seccion_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/opciones_seccion_data.php`

## Entrada

Sin parámetros.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves: `opciones` (`array<string, string>`).

## Errores conocidos

Ninguno.

## Permisos

Filtro implícito de opciones vía `EncargoAplicacionService::havePermOficina('des'|'vcsd')`
(oculta sf sin esos permisos). No bloquea el endpoint; solo cambia el catálogo. Resto:
frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\OpcionesSeccionData`

## Frontend Relacionado

- `frontend/encargossacd/controller/propuestas_lista.php` (construye `Desplegable` `filtro_ctr`)

## Notas

- La clave `8` (zonas) no tiene rama en `PropuestasCentrosPorFiltro` al listar propuestas.
- Relación pantallas↔API: no está en `relaciones/pantallas_api.md` (índice generado previo).
