---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Editar los textos tipo"
flujo: "inventario.cabecera_pie_txt.gestionar.flujo"
preguntas: ["Como editar los textos tipo en Editar los textos tipo?"]
pantallas_principales: ["inventario.pantalla.cabecera_pie_txt"]
fragmentos: ["inventario.pantalla.cabecera_pie_txt", "inventario.pantalla.equipajes_imprimir"]
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/cabecera_pie_txt_guardar"]
source: "docs/catalogo/inventario/flujos/cabecera_pie_txt.md"
estado_revision: "generado"
---

# Ayuda IA - Editar los textos tipo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Editar los textos tipo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como editar los textos tipo en Editar los textos tipo?

## Donde Entrar

- Textos cabecera/pie equipajes (`inventario.pantalla.cabecera_pie_txt`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Editar los textos tipo

1. Abrir **tipos de texto**.
2. Modificar la **cabecera tipo para equipajes** (instrucciones para comprobar el inventario de la casa al llegar y al marchar).
3. Modificar la **cabecera para equipajes tipo B) (que envía la dl)** (instrucciones para el material que lleva la delegación y se devuelve al final).
4. Modificar el **pie tipo para equipajes** (lista de lo que se deja en la casa) y el texto **para la firma en equipajes** (observaciones, fecha y firma).
5. Pulsar **guardar**; aparece «textos guardados correctamente».

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/cabecera_pie_txt_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.cabecera_pie_txt`
- `inventario.pantalla.cabecera_pie_txt`
- `inventario.pantalla.equipajes_imprimir`

## Objetivo

Fijar los textos tipo que llevan por defecto los equipajes impresos: cabecera, cabecera para el material que envía la dl y pie de firma.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
