---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Imprimir un equipaje"
flujo: "inventario.equipajes_egm.gestionar.flujo"
preguntas: ["Como imprimir un equipaje en Imprimir un equipaje?", "Como ver las valijas de un equipaje en Imprimir un equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_imprimir"]
fragmentos: ["inventario.pantalla.equipajes_doc_casa", "inventario.pantalla.equipajes_imprimir"]
endpoints: ["/src/inventario/equipajes_egm"]
source: "docs/catalogo/inventario/flujos/equipajes_egm.md"
estado_revision: "generado"
---

# Ayuda IA - Imprimir un equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Imprimir un equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como imprimir un equipaje en Imprimir un equipaje?
- Como ver las valijas de un equipaje en Imprimir un equipaje?

## Donde Entrar

- Imprimir equipaje (`inventario.pantalla.equipajes_imprimir`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Imprimir un equipaje

1. Abrir **imprimir equipajes** y elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje**.
3. Se muestra la hoja «(casa): Inventario de publicaciones internas» con las actividades del equipaje y la cabecera.
4. Revisar el apartado **A) Documentación que pertenece a la casa y se queda siempre allí** (con los ejemplares y numeración de cada documento).
5. Si el equipaje tiene valijas, revisar el apartado **B) Documentación que envía la Delegación**, con cada valija y sus documentos.
6. Comprobar el pie y el bloque final de **Observacions** y **data i firma**.
7. Imprimir desde el navegador. Los lápices de **modificar texto** no salen en el papel.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_egm`

## Ver las valijas de un equipaje

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (nombre de la casa) se listan las valijas con sus documentos.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_egm`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_imprimir`
- `inventario.pantalla.equipajes_doc_casa`
- `inventario.pantalla.equipajes_imprimir`

## Objetivo

Preparar e imprimir el equipaje de una casa: el inventario de lo que hay en la casa más el material que lleva la delegación, con espacio para observaciones y firma.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
