---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Abrir el texto para modificarlo"
flujo: "inventario.texto_de_egm.gestionar.flujo"
preguntas: ["Como abrir el texto para modificarlo en Abrir el texto para modificarlo?"]
pantallas_principales: ["inventario.pantalla.equipajes_form_texto_listado"]
fragmentos: ["inventario.pantalla.equipajes_form_texto_listado"]
endpoints: ["/src/inventario/texto_de_egm"]
source: "docs/catalogo/inventario/flujos/texto_de_egm.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir el texto para modificarlo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir el texto para modificarlo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el texto para modificarlo en Abrir el texto para modificarlo?

## Donde Entrar

- Editar texto listado (`inventario.pantalla.equipajes_form_texto_listado`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el texto para modificarlo

1. En **imprimir equipajes**, pulsar el lápiz junto a la cabecera, la cabecera B) o el pie.
2. Se abre el cuadro con el texto actual.
3. Cambiarlo y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/texto_de_egm`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_form_texto_listado`
- `inventario.pantalla.equipajes_form_texto_listado`

## Objetivo

Cargar el texto que se añade a las maletas (cabecera, cabecera B, pie) para poder modificarlo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
