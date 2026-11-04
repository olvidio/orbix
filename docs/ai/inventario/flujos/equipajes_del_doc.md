---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Quitar un documento de una valija"
flujo: "inventario.equipajes_del_doc.gestionar.flujo"
preguntas: ["Como quitar un documento de una valija en Quitar un documento de una valija?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_del_doc"]
source: "docs/catalogo/inventario/flujos/equipajes_del_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Quitar un documento de una valija

Usa este documento para responder preguntas de usuario sobre como trabajar con `Quitar un documento de una valija`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como quitar un documento de una valija en Quitar un documento de una valija?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Quitar un documento de una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **quitar documento**.
3. Marcar los documentos a quitar y pulsar el botón de quitar (**Cancel** para salir sin cambios).
4. Los documentos dejan de estar en la valija; si falla se muestra el aviso **respuesta**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_del_doc`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Sacar un documento de una valija del equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
