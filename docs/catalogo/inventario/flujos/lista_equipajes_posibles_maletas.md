---
id: "inventario.lista_equipajes_posibles_maletas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Crear una valija nueva"
capacidad: "inventario.lista_equipajes_posibles_maletas.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_posibles_maletas"]
fragmentos: ["inventario.pantalla.equipajes_posibles_maletas"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_equipajes_posibles_maletas"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Equipajes Posibles Maletas

## Objetivo De Usuario

Calcular el nombre de la siguiente valija (maleta) que se puede crear en un equipaje.

## Punto De Entrada

Dentro de **hacer equipajes**, al crear una valija nueva.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_posibles_maletas`

## Escenarios Inferidos

### Crear una valija nueva

Pasos propuestos:
1. En **hacer equipajes**, elegir el equipaje.
2. Pulsar **nueva valija**.
3. El sistema propone el siguiente número («valija» + número) para la nueva maleta.

Endpoints asociados:
- `/src/inventario/lista_equipajes_posibles_maletas`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_equipaje`

Acciones JavaScript:
- `fnjs_ver_docs`

## Endpoints Del Flujo

- `/src/inventario/lista_equipajes_posibles_maletas`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
