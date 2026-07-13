---
id: "misas.modificar_encargos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Encargos"
capacidad: "misas.modificar_encargos.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_encargos"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_data"]
estado_revision: "revisado"
---

# Flujo - Modificar encargos

## Objetivo De Usuario

Devuelve zonas permitidas y criterios de orden para la pantalla modificar encargos de zona.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar encargos. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_encargos`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`
- `form.orden`

Acciones JavaScript:
- `fnjs_ver_encargos_zona`

## Endpoints Del Flujo

- `/src/misas/modificar_encargos_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`
- `orden`
- `prioridad`
- `alfabético`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
