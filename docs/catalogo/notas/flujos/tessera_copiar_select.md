---
id: "notas.tessera_copiar_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Copiar una tessera a otra persona"
capacidad: "notas.tessera_copiar_select.gestionar"
pantallas_principales: ["notas.pantalla.tessera_copiar_select"]
fragmentos: ["notas.pantalla.tessera_copiar_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_copiar_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Tessera Copiar Select

Propuesta generada automaticamente desde la capacidad `notas.tessera_copiar_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Copiar la tessera de una persona a otra con el mismo primer apellido (por ejemplo de numerario a agregado).

## Punto De Entrada

Tessera de la persona, botón de copiar.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_copiar_select`

## Escenarios Inferidos

### Copiar una tessera a otra persona

Pasos propuestos:
1. Abrir la tessera de la persona.
2. En **copiar la tesera de … a**, elegir la persona destino: se listan las personas con el mismo primer apellido entre numerarios y agregados.
3. Pulsar **copiar**.

Endpoints asociados:
- `/src/notas/tessera_copiar_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom_dst`
- `html.copiar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_copiar`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/tessera_copiar_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
