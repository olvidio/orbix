---
id: "inventario.equipajes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Ciclo completo de un equipaje"
capacidad: "inventario.equipajes.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["eliminar"]
endpoints: ["/src/inventario/equipajes_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes

Propuesta generada automaticamente desde la capacidad `inventario.equipajes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ciclo de vida de los equipajes: crear, componer las valijas, imprimir y eliminar.

## Punto De Entrada

Menú **Inventario > equipajes**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ciclo completo de un equipaje

Pasos propuestos:
1. Crear con **nuevo equipaje**.
2. Componer las valijas en **hacer equipajes**.
3. Imprimir con **imprimir equipajes**.
4. Al terminar, eliminar el equipaje desde **hacer equipajes** (se eliminan también sus valijas).

Endpoints asociados:
- `/src/inventario/equipajes_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_eliminar`

## Errores Conocidos

- ``falta id_equipaje``
- ``hay un error, no se ha eliminado``

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > hacer equipajes
- **Pills2:** —
