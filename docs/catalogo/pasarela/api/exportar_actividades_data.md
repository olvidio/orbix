---
id: "pasarela.exportar_actividades_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/exportar_actividades_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/exportar_actividades_data.php"
entrada:
  - "post.id_tipo_activ:string"
  - "post.isfsv_val:string"
  - "post.iasistentes_val:string"
  - "post.iactividad_val:string"
  - "post.inicio_iso:string"
  - "post.fin_iso:string"
  - "post.id_cdc:array"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Periodo no válido"
frontend_referencias:
  - "frontend\/pasarela\/controller\/exportar_select.php"
casos_uso: ["src\pasarela\application\ExportarActividadesData"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Exportar Actividades Data

Genera cabeceras y filas para exportar actividades al exterior según filtros.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve tipo (directo o vía sfsv/asistentes/actividad), periodo ISO y casas (`id_cdc`).
Devuelve tabla con activación calculada, contribuciones, tarifas y centros encargados.
Errores parciales acumulados en `errores` (HTML `<br>`).

## Endpoint

- URL: `/src/pasarela/exportar_actividades_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_actividades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |
| `isfsv_val` | `string` | controller | No | |
| `iasistentes_val` | `string` | controller | No | |
| `iactividad_val` | `string` | controller | No | |
| `inicio_iso` | `string` | controller | No | |
| `fin_iso` | `string` | controller | No | |
| `id_cdc` | `array` | controller | No | |


## Salida

- Payload: `{a_cabeceras, a_botones, a_valores, errores}`.
- `a_valores` indexado por fila/columna (20 columnas).
- Doble `JSON.parse` en el front.

## Errores conocidos

- `Periodo no válido`

## Permisos

Sin control en el caso de uso; acceso vía pantalla exportar (frontend + permisos menú).

Notas: Si `id_tipo_activ` vacío, compone el id desde `isfsv_val`/`iasistentes_val`/`iactividad_val` (default sfsv según `ConfigGlobal::mi_sfsv()`).

## Casos De Uso

- `src\pasarela\application\ExportarActividadesData`

## Frontend Relacionado

- `frontend/pasarela/controller/exportar_select.php`