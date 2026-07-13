---
id: "misas.ver_iniciales_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Iniciales Zona"
capacidad: "misas.ver_iniciales_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_iniciales_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver iniciales zona

## Objetivo De Usuario

Lista sacds de una zona con sus iniciales y color para edición inline en SlickGrid.

## Punto De Entrada

Menú Legacy: dre > Misas > Iniciales sacd. Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_iniciales_zona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.color`
- `form.id_sacd`
- `form.iniciales`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_generarNomEnc`

## Endpoints Del Flujo

- `/src/misas/ver_iniciales_zona_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd
