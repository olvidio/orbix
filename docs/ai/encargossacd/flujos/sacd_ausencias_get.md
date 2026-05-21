---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Sacd Ausencias Get"
flujo: "encargossacd.sacd_ausencias_get.gestionar.flujo"
preguntas: ["Como obtener datos en Sacd Ausencias Get?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_get"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias_get.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Ausencias Get

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Ausencias Get`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacd Ausencias Get?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias_get`

## Objetivo

Gestiona SacdAusenciasGet. Datos para la ficha de ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_get.php). Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD. Con historial=1 incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
