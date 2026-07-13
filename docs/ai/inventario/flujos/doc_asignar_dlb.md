---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Doc Asignar Dlb"
flujo: "inventario.doc_asignar_dlb.gestionar.flujo"
preguntas: ["Como guardar en Doc Asignar Dlb?"]
pantallas_principales: []
fragmentos: ["inventario.pantalla.doc_asignar_dlb"]
endpoints: ["/src/inventario/doc_asignar_dlb_guardar"]
source: "docs/catalogo/inventario/flujos/doc_asignar_dlb.md"
estado_revision: "generado"
---

# Ayuda IA - Doc Asignar Dlb

Usa este documento para responder preguntas de usuario sobre como trabajar con `Doc Asignar Dlb`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Doc Asignar Dlb?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_asignar_dlb`

## Objetivo

Gestiona DocAsignarDlb. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
