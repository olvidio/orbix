---
id: "asistentes.lista_asistentes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Ver la relación de asistentes de una actividad"
capacidad: "asistentes.lista_asistentes.gestionar"
pantallas_principales: ["asistentes.pantalla.lista_asistentes"]
fragmentos: ["asistentes.pantalla.lista_asistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asistentes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Asistentes

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Ver la relación de asistentes de una actividad (botones **lista cl** y **lista**).

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **lista cl** o **lista**.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.lista_asistentes`

## Escenarios Inferidos

### Ver la relación de asistentes de una actividad

Pasos propuestos:
1. Marcar la actividad en la lista y pulsar **lista** (nombres) o **lista cl** (con más datos).
2. Aparece el título de la actividad y «Relación de asistentes» numerada.
3. **lista**: nombre y dl (o centro) de cada asistente. **lista cl**: además estudios, profesión, edad, eap y observaciones de la actividad.

Endpoints asociados:
- `/src/asistentes/lista_asistentes_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/lista_asistentes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
