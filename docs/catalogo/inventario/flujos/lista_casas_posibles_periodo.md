---
id: "inventario.lista_casas_posibles_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar las casas del periodo"
capacidad: "inventario.lista_casas_posibles_periodo.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_casas_posibles"]
fragmentos: ["inventario.pantalla.equipajes_casas_posibles"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_casas_posibles_periodo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Casas Posibles Periodo

## Objetivo De Usuario

Ver en qué casas se realizan actividades en el periodo elegido, para escoger la casa del equipaje.

## Punto De Entrada

Dentro de **nuevo equipaje**, con el botón **ver las casas donde se realizan las actividades**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_casas_posibles`

## Escenarios Inferidos

### Elegir la casa del equipaje

Pasos propuestos:
1. En **nuevo equipaje**, elegir el periodo.
2. Pulsar **ver las casas donde se realizan las actividades**.
3. Elegir la casa en el desplegable: se carga la lista de sus actividades.

Endpoints asociados:
- `/src/inventario/lista_casas_posibles_periodo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.inicio`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_ver_actividades_casa`

## Endpoints Del Flujo

- `/src/inventario/lista_casas_posibles_periodo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
