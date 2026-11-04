---
id: "notas.tessera_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Consultar la tessera de una persona"
capacidad: "notas.tessera_ver.gestionar"
pantallas_principales: ["notas.pantalla.tessera_ver"]
fragmentos: ["notas.pantalla.tessera_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_ver_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tessera Ver

Propuesta generada automaticamente desde la capacidad `notas.tessera_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver la tessera de estudios de una persona: asignaturas hechas y créditos, de filosofía y teología.

## Punto De Entrada

Ficha de la persona > tessera de estudios.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_ver`

## Escenarios Inferidos

### Consultar la tessera de una persona

Pasos propuestos:
1. Abrir la tessera de la persona.
2. Aparecen el número de asignaturas y créditos realizados en el curso y en total, separados por filosofía y teología, y lo que queda pendiente.

Endpoints asociados:
- `/src/notas/tessera_ver_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/tessera_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
