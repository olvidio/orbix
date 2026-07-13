---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta Modificar"
flujo: "notas.acta_modificar.gestionar.flujo"
preguntas: ["Como ejecutar en Acta Modificar?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_modificar"]
source: "docs/catalogo/notas/flujos/acta_modificar.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Modificar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Modificar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Acta Modificar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`

## Objetivo

Guardar cambios de un acta existente desde `acta_ver`.

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
