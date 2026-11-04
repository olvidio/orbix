---
id: "misas.update_iniciales.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Guardar iniciales y color"
capacidad: "misas.update_iniciales.gestionar"
pantallas_principales: ["misas.pantalla.ver_iniciales_zona"]
fragmentos: ["misas.pantalla.ver_iniciales_zona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/update_iniciales"]
estado_revision: "revisado"
---

# Flujo - Update iniciales

## Objetivo De Usuario

Guardar las iniciales y el color de un sacerdote.

## Punto De Entrada

Menú **dre > Misas > Iniciales sacd** (Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_iniciales_zona`

## Escenarios Inferidos

### Guardar iniciales y color

Pasos propuestos:
1. Cambiar las iniciales o el color en la fila.
2. Se guardan al salir de la celda.

Endpoints asociados:
- `/src/misas/update_iniciales`

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
