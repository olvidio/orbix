---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Trasladar documentos a otro centro"
flujo: "inventario.traslado_doc.gestionar.flujo"
preguntas: ["Como trasladar documentos a otro centro en Trasladar documentos a otro centro?"]
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: []
endpoints: ["/src/inventario/traslado_doc_guardar"]
source: "docs/catalogo/inventario/flujos/traslado_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Trasladar documentos a otro centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Trasladar documentos a otro centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como trasladar documentos a otro centro en Trasladar documentos a otro centro?

## Donde Entrar

- Traslado de documentos — filtro (`inventario.pantalla.traslado_doc_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Trasladar documentos a otro centro

1. Abrir **traslado de doc**.
2. Elegir el centro de origen, luego el lugar, y pulsar **Buscar**. La lista puede ser muy larga (más de mil documentos en un almacén).
3. Marcar en **lista de documentos del centro** los documentos que se mueven.
4. Elegir el centro y el lugar de destino.
5. Pulsar **Asignar**: los documentos pasan al lugar de destino y la lista de origen se actualiza.
6. Si hay un problema aparece un aviso **respuesta**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/traslado_doc_guardar`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.traslado_doc_que`

## Objetivo

Pasar documentos de un centro (y lugar) a otro.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
