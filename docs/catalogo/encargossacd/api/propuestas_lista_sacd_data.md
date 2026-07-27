---
id: "encargossacd.propuestas_lista_sacd_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/propuestas_lista_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/propuestas_lista_sacd_data.php"
entrada: ["post.sel:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_PropuestasListaSacdDataData"
respuesta_data: ["array_modo:array", "lugar_fecha:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/propuestas_lista_sacd.php"]
casos_uso: ["src\\encargossacd\\application\\PropuestasListaSacdData"]
errores: []
tags: ["encargossacd", "propuestas", "lista", "sacd", "data"]
estado_revision: "revisado"
---
# Propuestas Lista Sacd Data

Payload para el listado impreso de propuestas por SACD (sucesor de la lógica inline de
`propuestas_lista_sacd.php`). Consume tablas staging `propuesta_*`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada persona SACD activa de la DL según `sel`, arma filas de encargos propuestos
(`id_nom_new`) con dedicación, sección, pareja titular/suplente y observaciones.

| `sel` | Personas |
|-------|----------|
| `nagd` | `id_tabla ~ ^n\|^a`, situacion A, sacd, DL propia |
| `sssc` | `id_tabla ~ ^sss`, situacion A, sacd, DL propia |
| *(otro / vacío)* | Lista vacía (`array_modo` vacío) |

Excluye tipos de encargo cuyo primer dígito es 4/7/8 y tipos concretos 4002, 1110, 1210.
Tipos 5020/5030/6000 van al grupo «otros» con dedicación textual.

## Endpoint

- URL: `/src/encargossacd/propuestas_lista_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/propuestas_lista_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | controller | No | `nagd` \| `sssc`; menú actual solo pasa `nagd` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves:
  - `array_modo`: lista indexada de SACD; cada ítem con `nom_ap`, `txt` (traducciones),
    `grupo` (mapa grupo→filas encargo: `desc_enc`, `nombre_ubi`, `seccion`, `dedic_*`, `sup_tit`),
    `observ`.
  - `lugar_fecha`: cabecera «población, fecha local» (la vista actual no la muestra; queda en payload).

## Errores conocidos

Sin mensajes `_()` de error; si faltan tablas staging el comportamiento depende del repositorio
(pendiente documentar fallo explícito).

## Permisos

Sin control propio; frontend + menú. Pendiente: confirmar oficina requerida.

## Casos De Uso

- `src\encargossacd\application\PropuestasListaSacdData`

## Frontend Relacionado

- `frontend/encargossacd/controller/propuestas_lista_sacd.php` → vista `propuestas_lista_sacd.phtml`

## Notas

- Relación pantallas↔API: no está en `relaciones/pantallas_api.md` (índice generado previo).
