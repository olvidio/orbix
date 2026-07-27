---
id: "encargossacd.propuestas_ajax"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/propuestas_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/propuestas_ajax.php"
entrada: ["post.que:string", "post.filtro_ctr:integer", "post.tipo:string", "post.id_item:integer", "post.id_enc:integer", "post.id_sacd:integer", "post.dedic_m:integer", "post.dedic_t:integer", "post.dedic_v:integer"]
entrada_obligatoria: ["post.que:string"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_PropuestasAjaxData"
respuesta_data: ["success:bool", "mensaje?:string", "lista?:string", "html?:string", "nombre?:string", "id_sacd?:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/propuestas_ajax.php", "frontend/encargossacd/controller/propuestas_lista.php", "frontend/encargossacd/controller/propuestas_menu.php"]
casos_uso: ["src\\encargossacd\\application\\PropuestasAjaxDispatch", "src\\encargossacd\\application\\PropuestasAjaxGetLista", "src\\encargossacd\\application\\PropuestasCrearTabla", "src\\encargossacd\\application\\PropuestasAjaxMutations"]
errores: ["Operación no soportada", "Debe crear la tabla de propuestas", "No se puede crear la tabla", "No se puede guardar. Vuelva a cargar la vista", "Registro no encontrado", "Operación no reconocida"]
tags: ["encargossacd", "propuestas", "ajax"]
estado_revision: "revisado"
---
# Propuestas Ajax

Dispatcher multipropósito de la pantalla de propuestas (`que` → caso de uso). El frontend
`propuestas_ajax.php` reenvía el POST a esta URL y responde al JS legacy con el payload interno
en la raíz (`{success, mensaje?, lista?, html?, ...}`), no con el sobre estándar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Opera sobre tablas staging `propuesta_*` (encargos SACD propuestos para el nuevo curso):

| `que` | Comportamiento |
|-------|----------------|
| `get_lista` | HTML editable de encargos por centros filtrados (`filtro_ctr`). |
| `crear_tabla` | Crea/recrea tablas staging (`DBPropuestas::createAll`). |
| `lista_sacd` | HTML del desplegable de SACD posibles para titular/suplente/colaborador. |
| `cmb_sacd` | Asigna o cambia SACD propuesto (`id_nom_new`); alta si `id_item === id_enc`. |
| `dedicacion` | Formulario HTML de dedicación m/t/v. |
| `dedicacion_update` | Guarda dedicación en horario staging. |
| `info` | Popup HTML con encargos propuestos del SACD. |
| *(otro)* | `success: false`, mensaje «Operación no soportada». |

## Endpoint

- URL: `/src/encargossacd/propuestas_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion` (dispatcher; `get_lista` es lectura/HTML)
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/propuestas_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller | Sí | Rama del `match` en `PropuestasAjaxDispatch` |
| `filtro_ctr` | `integer` | application | No | Solo `get_lista`: 1=sv, 2=sf, 3=sss+, 4=igl, 5=cgi/oc; otro → lista vacía |
| `tipo` | `string` | application | No | `titular` / `suplente` / `colaborador` (`lista_sacd`, `cmb_sacd`) |
| `id_item` | `integer` | application | No | Ítem propuesta; si igual a `id_enc` → alta |
| `id_enc` | `integer` | application | No | Encargo |
| `id_sacd` | `integer` | application | No | SACD seleccionado / actual |
| `dedic_m` / `dedic_t` / `dedic_v` | `integer` | application | No | Solo `dedicacion_update` (mañana / tarde 1ª / tarde 2ª) |

## Casos particulares

- **`get_lista`**: si no existen tablas staging → error «Debe crear la tabla de propuestas».
- **`crear_tabla`**: `RuntimeException` → «No se puede crear la tabla».
- **`cmb_sacd`** con `id_item === id_enc`: crea `PropuestaEncargoSacd` (modo 2/4/5 según `tipo`).
- **`cmb_sacd`** colaborador sin SACD actual ni nuevo: elimina la fila (`html: "borrar"`).
- **`dedicacion_update`**: si `id_item === id_enc` intenta resolver el ítem real; si no hay exactamente uno → «No se puede guardar. Vuelva a cargar la vista».
- **`filtro_ctr=8` (zonas)** aparece en opciones de sección pero `PropuestasCentrosPorFiltro` no tiene rama 8 (queda vacío). Pendiente de aclarar si es intencional.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en clientes vía `PostRequest`).
- Payload interno (clave `data` del sobre): siempre incluye `success: bool`.
  - `get_lista`: `{success, lista}` (HTML) o `{success:false, mensaje}`.
  - `crear_tabla` / `dedicacion_update`: `{success}` o `{success:false, mensaje}`.
  - `lista_sacd` / `dedicacion` / `info`: `{success, html}`.
  - `cmb_sacd`: `{success, nombre, id_sacd, html}` o error.
- El controller FE `propuestas_ajax.php` emite ese payload en la raíz del JSON HTTP (el JS no hace segundo parse del sobre).

## Errores conocidos

- `Operación no soportada`
- `Debe crear la tabla de propuestas`
- `No se puede crear la tabla`
- `No se puede guardar. Vuelva a cargar la vista`
- `Registro no encontrado`
- `Operación no reconocida` (rama interna de mutations; no debería alcanzarse vía dispatch)
- Mensajes de repositorio (`getErrorTxt()`) en fallos de `Guardar`

## Efectos colaterales

- `crear_tabla`: elimina/recrea tablas staging.
- `cmb_sacd` / `dedicacion_update`: escriben en `propuesta_encargo_sacd` y `propuesta_encargo_sacd_horario`; pueden llamar `cambiarSacd` en horarios.

## Permisos

Sin control propio en el caso de uso; autorización vía frontend + `$_SESSION['oPerm']` / menú Encargos. Pendiente: confirmar si hace falta `des`/`vcsd` explícito.

## Casos De Uso

- `src\encargossacd\application\PropuestasAjaxDispatch`
- `src\encargossacd\application\PropuestasAjaxGetLista`
- `src\encargossacd\application\PropuestasCrearTabla`
- `src\encargossacd\application\PropuestasAjaxMutations`

## Frontend Relacionado

- `frontend/encargossacd/controller/propuestas_ajax.php` (proxy JSON)
- `frontend/encargossacd/controller/propuestas_lista.php` (hashes y llamadas JS)
- `frontend/encargossacd/controller/propuestas_menu.php` (`crear_tabla`)

## Notas

- Relación pantallas↔API: no está en `relaciones/pantallas_api.md` (índice generado previo); cruzar por estas referencias FE.
