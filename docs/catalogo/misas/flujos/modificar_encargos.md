---
id: "misas.modificar_encargos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir la zona de los encargos"
capacidad: "misas.modificar_encargos.gestionar"
pantallas_principales: ["misas.pantalla.modificar_encargos"]
fragmentos: ["misas.pantalla.modificar_encargos"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_data"]
estado_revision: "revisado"
---

# Flujo - Modificar encargos

## Objetivo De Usuario

Elegir la zona para crear y modificar sus encargos (tareas de misa).

## Punto De Entrada

Menú **dre > Misas > Modificar encargos** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar encargos).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_encargos`

## Escenarios Inferidos

### Elegir la zona de los encargos

Pasos propuestos:
1. Entrar en **Modificar encargos**.
2. Elegir la zona en **Encargos de la zona** y el **Orden**.
3. Se abre la lista de encargos de esa zona.

Endpoints asociados:
- `/src/misas/modificar_encargos_data`

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
