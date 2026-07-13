---
id: "misas.update_iniciales.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Update Iniciales"
capacidad: "misas.update_iniciales.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/update_iniciales"]
estado_revision: "revisado"
---

# Flujo - Update iniciales

## Objetivo De Usuario

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

## Punto De Entrada

Menú Legacy: dre > Misas > Iniciales sacd. Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_iniciales_zona`

## Escenarios Inferidos

### Ejecutar

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

- `/src/misas/update_iniciales`

## Errores Conocidos

- `<repositorio getErrorTxt()>`

## Ruta de menú

- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd
