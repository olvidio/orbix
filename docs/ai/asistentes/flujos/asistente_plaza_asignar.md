---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Asignar plaza a varios asistentes"
flujo: "asistentes.asistente_plaza_asignar.gestionar.flujo"
preguntas: ["Como asignar plaza a varios asistentes en Asignar plaza a varios asistentes?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/asistentes/asistente_plaza_asignar"]
source: "docs/catalogo/asistentes/flujos/asistente_plaza_asignar.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar plaza a varios asistentes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar plaza a varios asistentes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar plaza a varios asistentes en Asignar plaza a varios asistentes?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar plaza a varios asistentes

1. En la lista de asistentes, marcar los que corresponda.
2. Elegir asignar plaza y la plaza que se quiere dar.
3. Confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/asistente_plaza_asignar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Asignar una plaza común a varios asistentes marcados a la vez.

## Errores Documentados

- `falta id_activ`
- `falta lista de seleccion`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
