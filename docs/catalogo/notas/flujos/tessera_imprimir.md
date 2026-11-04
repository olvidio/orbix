---
id: "notas.tessera_imprimir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Imprimir la tessera"
capacidad: "notas.tessera_imprimir.gestionar"
pantallas_principales: ["notas.pantalla.tessera_imprimir"]
fragmentos: ["notas.pantalla.tessera_imprimir", "notas.pantalla.tessera_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_imprimir_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tessera Imprimir

Propuesta generada automaticamente desde la capacidad `notas.tessera_imprimir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Imprimir la tessera de estudios de una persona.

## Punto De Entrada

Tessera de la persona.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_imprimir`
- `notas.pantalla.tessera_imprimir_mpdf`

## Escenarios Inferidos

### Imprimir la tessera

Pasos propuestos:
1. Abrir la tessera de la persona.
2. Pulsar imprimir (HTML o PDF).

Endpoints asociados:
- `/src/notas/tessera_imprimir_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.cara`
- `post.id_nom`
- `post.id_tabla`
- `post.refresh`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/tessera_imprimir_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
