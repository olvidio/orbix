---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Asignar camas a los asistentes"
flujo: "ubiscamas.actividad_habitaciones.gestionar.flujo"
preguntas: ["Como asignar camas a los asistentes en Asignar camas a los asistentes?"]
pantallas_principales: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
fragmentos: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
source: "docs/catalogo/ubiscamas/flujos/actividad_habitaciones.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar camas a los asistentes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar camas a los asistentes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar camas a los asistentes en Asignar camas a los asistentes?

## Donde Entrar

- Lista Habitaciones (`ubiscamas.pantalla.lista_habitaciones`)
- Lista Habitaciones Distribucion (`ubiscamas.pantalla.lista_habitaciones_distribucion`)
- Lista Habitaciones Nombres (`ubiscamas.pantalla.lista_habitaciones_nombres`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar camas a los asistentes

1. Abrir las habitaciones de la actividad; se ofrece **lista por habitaciones** o **lista por nombre**.
2. Arrastrar al asistente a la cama o usar las flechas de asignar y desasignar.
3. Marcar **solo VIP** para ver únicamente las camas VIP.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/actividad_habitaciones_lista`

## Pantallas Y Fragmentos Relacionados

- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`
- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`

## Objetivo

Ver las camas de la casa de una actividad y asignar a cada asistente su cama.

## Errores Documentados

- `Actividad not found`
- `No Ubi assigned to activity`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
