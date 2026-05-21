---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Procesos Ver"
flujo: "procesos.procesos_ver.gestionar.flujo"
preguntas: ["Como obtener datos en Procesos Ver?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_ver_data"]
source: "docs/catalogo/procesos/flujos/procesos_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Procesos Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Procesos Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Procesos Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_ver`

## Objetivo

Gestiona ProcesosVer. Caso de uso: datos para la pantalla procesos_ver (formulario editar / nuevo de una fase dentro de un tipo de proceso). Devuelve todos los arrays necesarios para que el controlador frontend monte los frontend\shared\web\Desplegable (fases, tareas, status, oficinas responsables, fases previas y sus tareas) y el formulario de edicion.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
