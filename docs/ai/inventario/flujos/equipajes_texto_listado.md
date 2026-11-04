---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Cambiar los textos del listado del equipaje"
flujo: "inventario.equipajes_texto_listado.gestionar.flujo"
preguntas: ["Como cambiar los textos del listado del equipaje en Cambiar los textos del listado del equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_texto_listado_guardar"]
source: "docs/catalogo/inventario/flujos/equipajes_texto_listado.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar los textos del listado del equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar los textos del listado del equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar los textos del listado del equipaje en Cambiar los textos del listado del equipaje?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar los textos del listado del equipaje

1. En **imprimir equipajes**, pulsar el lápiz (**modificar texto**) (o **introducir texto** si aún está vacío).
2. Escribir el texto de **cabecera**, **cabecera B)**, **pie** o **texto a añadir en las maletas**.
3. Pulsar **guardar** (o **cancel**).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_texto_listado_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Guardar el texto de cabecera, cabecera B), pie o el texto para las maletas de un equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
