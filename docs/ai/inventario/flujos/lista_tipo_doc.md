---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Lista Tipo Doc"
flujo: "inventario.lista_tipo_doc.gestionar.flujo"
preguntas: ["Como ejecutar en Lista Tipo Doc?"]
pantallas_principales: []
fragmentos: ["inventario.pantalla.docs_asignar_que", "inventario.pantalla.equipajes_form_add"]
endpoints: ["/src/inventario/lista_tipo_doc"]
source: "docs/catalogo/inventario/flujos/lista_tipo_doc.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Tipo Doc

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Tipo Doc`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Lista Tipo Doc?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.docs_asignar_que`
- `inventario.pantalla.equipajes_form_add`

## Objetivo

Gestiona TipoDocOpciones. Opciones del desplegable de tipos de documento (lista_tipo_doc.php).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
