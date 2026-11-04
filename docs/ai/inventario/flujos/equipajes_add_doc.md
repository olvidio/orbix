---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Añadir un documento a una valija"
flujo: "inventario.equipajes_add_doc.gestionar.flujo"
preguntas: ["Como añadir un documento a una valija en Añadir un documento a una valija?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_add_doc"]
source: "docs/catalogo/inventario/flujos/equipajes_add_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir un documento a una valija

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir un documento a una valija`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir un documento a una valija en Añadir un documento a una valija?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir un documento a una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **añadir documento**.
3. Elegir el **tipo de documento**; se listan los ejemplares libres. Marcar el ejemplar y pulsar **añadir** (o **cancel**).
4. El documento aparece en la valija; si falla se muestra el aviso **respuesta**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_add_doc`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Meter un documento en una valija del equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
