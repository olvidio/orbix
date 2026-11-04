---
id: "asistentes.lista_asis_conjunto_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Ver las plazas de varias actividades"
capacidad: "asistentes.lista_asis_conjunto_activ.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_asis_conjunto_activ"]
fragmentos: ["asistentes.pantalla.lista_asis_conjunto_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asis_conjunto_activ_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Asis Conjunto Activ

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver el listado conjunto de plazas y asistentes de varias actividades.

## Punto De Entrada

Selección de varias actividades.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_asis_conjunto_activ`

## Escenarios Inferidos

### Ver las plazas de varias actividades

Pasos propuestos:
1. Seleccionar las actividades.
2. Elegir el listado conjunto: se muestran las plazas y asistentes de todas ellas.

Endpoints asociados:
- `/src/asistentes/lista_asis_conjunto_activ_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_asis_conjunto_activ_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
