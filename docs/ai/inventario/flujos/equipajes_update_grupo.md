---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Crear una valija con documentos"
flujo: "inventario.equipajes_update_grupo.gestionar.flujo"
preguntas: ["Como crear una valija en Crear una valija con documentos?"]
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
endpoints: ["/src/inventario/equipajes_update_grupo"]
source: "docs/catalogo/inventario/flujos/equipajes_update_grupo.md"
estado_revision: "generado"
---

# Ayuda IA - Crear una valija con documentos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear una valija con documentos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear una valija en Crear una valija con documentos?

## Donde Entrar

- Gestionar equipajes (`inventario.pantalla.equipajes_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (casa), pulsar **nueva valija**: aparece una fila «valija1» (el número va subiendo) con una lista de maletas.
3. Elegir la maleta (por ejemplo «Maleta» o «Maleta Rialb 1 (color groc)»): se muestran los documentos que lleva.
4. La valija queda creada en el equipaje; aparecerá en la hoja impresa como «valija1: Maleta».

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_update_grupo`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_ver`

## Objetivo

Crear una valija o cambiar los documentos que lleva.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
