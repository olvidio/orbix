---
id: "misas.modificar_iniciales_sacd_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Iniciales Sacd Zona"
capacidad: "misas.modificar_iniciales_sacd_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_iniciales_sacd_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
estado_revision: "revisado"
---

# Flujo - Modificar iniciales sacd zona

## Objetivo De Usuario

Devuelve el desplegable de todas las zonas para la pantalla de edición de iniciales SACD.

## Punto De Entrada

Menú Legacy: dre > Misas > Iniciales sacd. Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_iniciales_sacd_zona`

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
- `fnjs_ver_iniciales_sacd_zona`

## Endpoints Del Flujo

- `/src/misas/modificar_iniciales_sacd_zona_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd
