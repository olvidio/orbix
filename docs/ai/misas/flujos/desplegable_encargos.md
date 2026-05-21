---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Desplegable Encargos"
flujo: "misas.desplegable_encargos.gestionar.flujo"
preguntas: ["Como ejecutar en Desplegable Encargos?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
endpoints: ["/src/misas/desplegable_encargos"]
source: "docs/catalogo/misas/flujos/desplegable_encargos.md"
estado_revision: "generado"
---

# Ayuda IA - Desplegable Encargos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Desplegable Encargos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Desplegable Encargos?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_centros`

## Objetivo

Gestiona DesplegableEncargos. Payload JSON para el desplegable dinamico de encargos de una zona. Sigue el contrato de desplegables de refactor.md: - id : id del <select> que montara fnjs_construir_desplegable. - opciones : map id_enc => desc_enc de los encargos con id_tipo_enc >= 8100 de la zona. - selected : id_enc preseleccionado ('' si no aplica). - blanco : true si se quiere opcion en blanco. - val_blanco: valor de la opcion en blanco. - action : handler onchange opcional (vacio por defecto).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
