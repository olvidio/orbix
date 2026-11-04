---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Exportar actividades al exterior"
flujo: "pasarela.exportar_actividades.gestionar.flujo"
preguntas: ["Como exportar actividades al exterior en Exportar actividades al exterior?"]
pantallas_principales: ["pasarela.pantalla.exportar_que"]
fragmentos: [""]
endpoints: [""]
source: "docs/catalogo/pasarela/flujos/exportar_actividades.md"
estado_revision: "generado"
---

# Ayuda IA - Exportar actividades al exterior

Usa este documento para responder preguntas de usuario sobre como trabajar con `Exportar actividades al exterior`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como exportar actividades al exterior en Exportar actividades al exterior?

## Donde Entrar

- Exportar actividades (`pasarela.pantalla.exportar_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Exportar actividades al exterior

1. Abrir **Pasarela > exportar actividades**.
2. Elegir el tipo de actividad y el **periodo**.
3. Pulsar **ver selección**.
4. Se muestra la tabla de actividades preparada para el sistema externo.
5. Leer los avisos: indican tarifas o fechas de activación que faltan para alguna actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/pasarela/exportar_actividades_data`

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.exportar_que`
- ``

## Objetivo

Generar listado tabular con datos de actividades filtradas para sistemas externos.

## Errores Documentados

- `Periodo no válido`
- `valor no válido para la activación del tipo de actividad %s`
- `No está definido el tipo tarifa...`
- `No está definida la id_tarifa...`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
