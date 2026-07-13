---
id: "misas.importar_plantilla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Importar Plantilla"
capacidad: "misas.importar_plantilla.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.importar_plantilla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/importar_plantilla_data"]
estado_revision: "revisado"
---

# Flujo - Importar plantilla

## Objetivo De Usuario

Copia asignaciones de plantilla origen a destino para una zona, creando/actualizando EncargoDia en el rango correspondiente.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plantilla. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.importar_plantilla`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
