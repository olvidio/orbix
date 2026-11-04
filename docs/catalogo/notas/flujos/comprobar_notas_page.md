---
id: "notas.comprobar_notas_page.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Comprobar los datos de notas"
capacidad: "notas.comprobar_notas_page.gestionar"
pantallas_principales: ["notas.pantalla.comprobar_notas"]
fragmentos: ["notas.pantalla.comprobar_notas"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/comprobar_notas_page_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Comprobar Notas Page

Propuesta generada automaticamente desde la capacidad `notas.comprobar_notas_page.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ejecutar las comprobaciones de los datos de notas y ver las incidencias.

## Punto De Entrada

Pills2: ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.comprobar_notas`

## Escenarios Inferidos

### Comprobar los datos de notas

Pasos propuestos:
1. Entrar en **Comprobar datos n** (o **agd**).
2. La pantalla ejecuta las comprobaciones y muestra el listado de incidencias.
3. Corregir los datos de las personas afectadas y volver a comprobar.

Endpoints asociados:
- `/src/notas/comprobar_notas_page_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/comprobar_notas_page_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (variantes id_tabla)
- **Pills2:** ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd
