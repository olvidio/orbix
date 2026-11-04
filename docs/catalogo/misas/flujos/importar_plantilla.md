---
id: "misas.importar_plantilla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Importar otra plantilla"
capacidad: "misas.importar_plantilla.gestionar"
pantallas_principales: ["misas.pantalla.importar_plantilla"]
fragmentos: ["misas.pantalla.importar_plantilla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/importar_plantilla_data"]
estado_revision: "revisado"
---

# Flujo - Importar plantilla

## Objetivo De Usuario

Copiar las asignaciones de una plantilla a otra de la misma zona.

## Punto De Entrada

Menú **dre > Misas > Modificar plantilla** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.importar_plantilla`

## Escenarios Inferidos

### Importar otra plantilla

Pasos propuestos:
1. En **Modificar plantilla**, elegir la plantilla origen en **Importar de la plantilla**.
2. Pulsar **importar** y confirmar el aviso de que se borra lo que hay.
3. Se muestra la plantilla con los datos copiados.

Endpoints asociados:
- `/src/misas/importar_plantilla_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_zona`
- `post.tipo_plantilla_destino`
- `post.tipo_plantilla_origen`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/importar_plantilla_data`

## Errores Conocidos

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
