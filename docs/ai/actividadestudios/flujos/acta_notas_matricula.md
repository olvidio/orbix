---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Acta Notas Matricula"
flujo: "actividadestudios.acta_notas_matricula.gestionar.flujo"
preguntas: ["Como guardar en Acta Notas Matricula?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
source: "docs/catalogo/actividadestudios/flujos/acta_notas_matricula.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Notas Matricula

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Notas Matricula`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar en Acta Notas Matricula?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.acta_notas`

## Objetivo

Gestiona ActaNotasMatricula. Guarda el borrador de notas sobre cada matricula (rama que=1 del legacy apps/actividadestudios/controller/acta_notas_update.php).

## Errores Documentados

- `Hay una nota mayor que el máximo`
- `hay un error, no se ha guardado`
- `no se puede definir cursada con preceptor`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
