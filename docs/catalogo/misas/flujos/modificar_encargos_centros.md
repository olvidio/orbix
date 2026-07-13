---
id: "misas.modificar_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Encargos Centros"
capacidad: "misas.modificar_encargos_centros.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
estado_revision: "revisado"
---

# Flujo - Modificar encargos centros

## Objetivo De Usuario

Devuelve el desplegable de zonas permitidas para la pantalla modificar encargos de centros.

## Punto De Entrada

Menú Legacy: dre > Misas > Encargos centro. Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_encargos_centros`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`

Acciones JavaScript:
- `fnjs_ver_encargos_centros`

## Endpoints Del Flujo

- `/src/misas/modificar_encargos_centros_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
