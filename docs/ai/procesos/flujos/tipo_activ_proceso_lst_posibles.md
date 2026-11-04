---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Ver los procesos asignables a un tipo de actividad"
flujo: "procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo"
preguntas: ["Como ver los procesos asignables a un tipo de actividad en Ver los procesos asignables a un tipo de actividad?"]
pantallas_principales: ["procesos.pantalla.tipo_activ_proceso"]
fragmentos: ["procesos.pantalla.tipo_activ_proceso_lst_posibles"]
endpoints: ["/src/procesos/tipo_activ_proceso_lst_posibles"]
source: "docs/catalogo/procesos/flujos/tipo_activ_proceso_lst_posibles.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los procesos asignables a un tipo de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los procesos asignables a un tipo de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los procesos asignables a un tipo de actividad en Ver los procesos asignables a un tipo de actividad?

## Donde Entrar

- Tipo Activ Proceso (`procesos.pantalla.tipo_activ_proceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los procesos asignables a un tipo de actividad

1. Entrar en **tipo activ - proceso**.
2. Elegir el tipo de actividad (propio de la dl o no propio).
3. Se muestra la mini-tabla de procesos posibles.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.tipo_activ_proceso`
- `procesos.pantalla.tipo_activ_proceso_lst_posibles`

## Objetivo

Mostrar los procesos que se pueden asignar a un tipo de actividad.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
