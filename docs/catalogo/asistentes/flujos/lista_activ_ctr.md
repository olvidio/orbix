---
id: "asistentes.lista_activ_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Ver las actividades por centro"
capacidad: "asistentes.lista_activ_ctr.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_activ_ctr"]
fragmentos: ["asistentes.pantalla.lista_activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_activ_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Activ Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver, centro por centro, las actividades previstas de sus personas.

## Punto De Entrada

Menú **vsm > ca > lista por ctr** (o crt, cve; también en vest, dagd y vsg según el colectivo).

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_activ_ctr`

## Escenarios Inferidos

### Ver las actividades por centro

Pasos propuestos:
1. Elegir los centros y el periodo (ver «Elegir centro y periodo») y pulsar **Buscar**.
2. Para cada centro (por ejemplo «Alimara») aparece una tabla «nombre / actividades previstas» con cada persona y su actividad (por ejemplo «ca n repaso Hostal Rialb (7/8/2026-29/8/2026)-dlb»), «NO HACE CA [pedida]» o «pendiente de solicitar» si aún no tiene.

Endpoints asociados:
- `/src/asistentes/lista_activ_ctr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_activ_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Destino del submit de que_ctr_lista (`lista=list_activ`)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr
