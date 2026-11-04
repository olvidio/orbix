---
id: "notas.informe_stgr_agd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Sacar el informe anual de agregados"
capacidad: "notas.informe_stgr_agd.gestionar"
pantallas_principales: ["notas.pantalla.informe_stgr_agd"]
fragmentos: ["notas.pantalla.informe_stgr_agd"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/informe_stgr_agd_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Informe Stgr Agd

Propuesta generada automaticamente desde la capacidad `notas.informe_stgr_agd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Generar el informe anual del Studium Generale de agregados.

## Punto De Entrada

Pills2: ESTUDIOS > Datos e informes > Informe anual agd > Con números / Con listados.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.informe_stgr_agd`

## Escenarios Inferidos

### Sacar el informe anual de agregados

Pasos propuestos:
1. Entrar en **Informe anual agd** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

Endpoints asociados:
- `/src/notas/informe_stgr_agd_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.lista`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/informe_stgr_agd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Calendario > Nuevo calendario > Previsión asistentes (sin lista); variantes lista en ESTUDIOS
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión asistentes; ESTUDIOS > Datos e informes > Informe anual agd > Con números / Con listados
