---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Buscar los centros"
flujo: "cartaspresentacion.cartas_presentacion_shell.gestionar.flujo"
preguntas: ["Como buscar los centros en Buscar los centros?", "Como modificar la carta de un centro en Buscar los centros?", "Como quitar una carta en Buscar los centros?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: []
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data", "/src/cartaspresentacion/ubis_lista_data", "/src/cartaspresentacion/poblaciones_data", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update", "/src/cartaspresentacion/carta_presentacion_eliminar"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_shell.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar los centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar los centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar los centros en Buscar los centros?
- Como modificar la carta de un centro en Buscar los centros?
- Como quitar una carta en Buscar los centros?

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar los centros

1. Abrir **modificar**: sale el formulario de selección.
2. Elegir «según dl» y, si procede, la población (con «según r» no hay población).
3. Pulsar **buscar**: debajo aparece la tabla de centros.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Modificar la carta de un centro

1. Pulsar **director** en la fila del centro: se abre una ventana.
2. Rellenar los campos y pulsar **guardar**; la lista se actualiza.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Quitar una carta

1. En las filas con carta, pulsar **quitar** y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`

## Objetivo

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de regiones extranjeras.

## Errores Documentados

- `Formulario: No puede modificar datos de otra dl, Centro no encontrado.`
- `Update: Hay un error, no se ha guardado.`
- `Eliminar: Carta de presentacion no encontrada, Hay un error, no se ha borrado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
