---
id: "notas.informe_stgr_n.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Sacar el informe anual de numerarios"
capacidad: "notas.informe_stgr_n.gestionar"
pantallas_principales: ["notas.pantalla.informe_stgr_n"]
fragmentos: ["notas.pantalla.informe_stgr_n"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/informe_stgr_n_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Informe Stgr N

Propuesta generada automaticamente desde la capacidad `notas.informe_stgr_n.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Generar el informe anual del Studium Generale de numerarios.

## Punto De Entrada

Pills2: ESTUDIOS > Datos e informes > Informe anual n > Con números / Con listados.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.informe_stgr_n`

## Escenarios Inferidos

### Sacar el informe anual de numerarios

Pasos propuestos:
1. Entrar en **Informe anual n** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

Endpoints asociados:
- `/src/notas/informe_stgr_n_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.lista`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/informe_stgr_n_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Datos e informes > Informe anual n > Con números / Con listados
