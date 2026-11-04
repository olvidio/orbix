---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Eliminar una valija"
flujo: "inventario.equipajes_eliminar_grupo.gestionar.flujo"
preguntas: ["Como eliminar una valija en Eliminar una valija?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_eliminar_grupo"]
source: "docs/catalogo/inventario/flujos/equipajes_eliminar_grupo.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar una valija

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar una valija`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar una valija en Eliminar una valija?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **eliminar valija**.
3. Confirmar la pregunta «¿Está seguro de borrar este grupo?».
4. La valija desaparece del equipaje y los documentos que llevaba dejan de estar en ella (siguen existiendo).

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_eliminar_grupo`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Borrar una valija entera del equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
