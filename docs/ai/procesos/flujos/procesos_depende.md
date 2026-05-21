---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Procesos Depende"
flujo: "procesos.procesos_depende.gestionar.flujo"
preguntas: ["Como ejecutar en Procesos Depende?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
endpoints: ["/src/procesos/procesos_depende"]
source: "docs/catalogo/procesos/flujos/procesos_depende.md"
estado_revision: "generado"
---

# Ayuda IA - Procesos Depende

Usa este documento para responder preguntas de usuario sobre como trabajar con `Procesos Depende`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Procesos Depende?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_ver`

## Objetivo

Gestiona ProcesosDepende. Caso de uso: devuelve las opciones disponibles para el desplegable de tareas dependientes de la fase indicada (usado al cambiar de fase o fase_previa en el formulario procesos_ver). Respuesta JSON con opciones (value => label). El frontend inyecta los <option> en el <select> destino indicado por acc.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
