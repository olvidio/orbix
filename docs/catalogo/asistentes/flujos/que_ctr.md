---
id: "asistentes.que_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Elegir centro y periodo"
capacidad: "asistentes.que_ctr.gestionar"
pantallas_principales: ["asistentes.pantalla.que_ctr_lista"]
fragmentos: ["asistentes.pantalla.que_ctr_lista"]
acciones: ["listar"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Que Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Elegir de qué centros se quiere ver las actividades de sus personas y en qué periodo.

## Punto De Entrada

Menú **vsm > ca > lista por ctr** (o crt, cve; también en vest, dagd y vsg según el colectivo).

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.que_ctr_lista`

## Escenarios Inferidos

### Elegir centro y periodo

Pasos propuestos:
1. Abrir «Actividades de personas por centros de la delegación» (pregunta «¿qué centro interesa?»).
2. Elegir **Todos los numerarios**, **Todos ctr numerarios jóvenes**, **Todos ctr numerarios mayores**, **Todos los agregados**, **Todos los de sss+** u **Otro...** (y entonces el centro concreto).
3. En «PERIODO DE INICIO O FINALIZACIÓN DE LAS ACTIVIDADES» elegir curso ca, curso crt, todo el año u **otro** (con fechas) y el año.
4. Pulsar **Buscar** (**Borrar** limpia el formulario). Si el resultado es grande, tarda unos segundos (hasta unos 10 con todos los numerarios y todo el año, lo que se considera aceptable) y muestra «Cargando...».

Endpoints asociados:
- `/src/asistentes/que_ctr_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.btn_ok`
- `html.n_agd`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_otro`

## Endpoints Del Flujo

- `/src/asistentes/que_ctr_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Varias entradas según `lista`/`sactividad` (vsm/vest/dagd > crt/ca/cv > list por ctr; dre > personas > pendientes)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr, Mejores ca para n/agd, Listado de personas sin ca/crt
