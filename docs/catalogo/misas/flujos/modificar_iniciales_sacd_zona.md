---
id: "misas.modificar_iniciales_sacd_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir la zona de las iniciales"
capacidad: "misas.modificar_iniciales_sacd_zona.gestionar"
pantallas_principales: ["misas.pantalla.modificar_iniciales_sacd_zona"]
fragmentos: ["misas.pantalla.modificar_iniciales_sacd_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
estado_revision: "revisado"
---

# Flujo - Modificar iniciales sacd zona

## Objetivo De Usuario

Elegir la zona cuyas iniciales de sacerdotes se quieren editar.

## Punto De Entrada

Menú **dre > Misas > Iniciales sacd** (Pills2: ATENCIÓN SACD > Gestión de misas > Iniciales sacd).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_iniciales_sacd_zona`

## Escenarios Inferidos

### Elegir la zona de las iniciales

Pasos propuestos:
1. Entrar en **Iniciales sacd**.
2. Elegir la zona en **Iniciales sacerdotes de la zona**.

Endpoints asociados:
- `/src/misas/modificar_iniciales_sacd_zona_data`

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
