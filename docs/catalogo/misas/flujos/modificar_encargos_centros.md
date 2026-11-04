---
id: "misas.modificar_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir la zona de los encargos visibles por centro"
capacidad: "misas.modificar_encargos_centros.gestionar"
pantallas_principales: ["misas.pantalla.modificar_encargos_centros"]
fragmentos: ["misas.pantalla.modificar_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
estado_revision: "revisado"
---

# Flujo - Modificar encargos centros

## Objetivo De Usuario

Elegir la zona para decidir qué encargos ve cada centro.

## Punto De Entrada

Menú **dre > Misas > Encargos centro** (Pills2: ATENCIÓN SACD > Gestión de misas > Encargos ctr).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_encargos_centros`

## Escenarios Inferidos

### Elegir la zona de los encargos visibles por centro

Pasos propuestos:
1. Entrar en **Encargos centro**.
2. Elegir la zona en **Encargos de la zona**.
3. Se abre la lista de encargos visibles por centro.

Endpoints asociados:
- `/src/misas/modificar_encargos_centros_data`

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
