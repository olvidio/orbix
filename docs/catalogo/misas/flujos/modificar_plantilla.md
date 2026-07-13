---
id: "misas.modificar_plantilla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Plantilla"
capacidad: "misas.modificar_plantilla.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_plantilla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_plantilla_data"]
estado_revision: "revisado"
---

# Flujo - Modificar plantilla

## Objetivo De Usuario

Carga desplegables de zona, orden y tipos de plantilla (con preferencia ultima_plantilla) para modificar plantilla.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plantilla. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plantilla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_plantilla`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`
- `form.importar_de_plantilla`
- `form.orden`
- `form.tipo_plantilla`
- `form.tipo_plantilla_destino`
- `form.tipo_plantilla_origen`
- `html.importar`

Acciones JavaScript:
- `button:importar`
- `fnjs_importar_de_plantilla_zona`
- `fnjs_ver_plantilla_zona`

## Endpoints Del Flujo

- `/src/misas/modificar_plantilla_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plantilla
