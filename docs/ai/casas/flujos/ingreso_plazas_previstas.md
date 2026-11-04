---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Corregir los asistentes previstos"
flujo: "casas.ingreso_plazas_previstas.gestionar.flujo"
preguntas: ["Como corregir los asistentes previstos en Corregir los asistentes previstos?"]
pantallas_principales: ["casas.pantalla.prevision_asistentes"]
fragmentos: ["casas.pantalla.prevision_asistentes"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update"]
source: "docs/catalogo/casas/flujos/ingreso_plazas_previstas.md"
estado_revision: "generado"
---

# Ayuda IA - Corregir los asistentes previstos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Corregir los asistentes previstos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como corregir los asistentes previstos en Corregir los asistentes previstos?

## Donde Entrar

- Prevision Asistentes (`casas.pantalla.prevision_asistentes`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Corregir los asistentes previstos

1. Abrir **previsión asistentes**.
2. Hacer doble clic en la celda del número de asistentes previstos, escribir el valor y confirmar.
3. Se guarda al salir de la celda.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/ingreso_plazas_previstas_update`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.prevision_asistentes`
- `casas.pantalla.prevision_asistentes`

## Objetivo

Cambiar el número de asistentes previstos de una actividad en la previsión.

## Errores Documentados

- `Hay un error, no se ha guardado`
- `no se encuentra el ingreso`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
