---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Elegir la zona y el periodo"
flujo: "planning.planning_zones_que.gestionar.flujo"
preguntas: ["Como elegir la zona y el periodo en Elegir la zona y el periodo?"]
pantallas_principales: ["planning.pantalla.planning_zones_que"]
fragmentos: []
endpoints: ["/src/planning/planning_zones_que_data"]
source: "docs/catalogo/planning/flujos/planning_zones_que.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la zona y el periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la zona y el periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la zona y el periodo en Elegir la zona y el periodo?

## Donde Entrar

- Planning por zonas SACD (filtros) (`planning.pantalla.planning_zones_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la zona y el periodo

1. Abrir «trimestre o mes que interesa» y marcar un trimestre (enero-marzo, abril-junio, julio-septiembre, octubre-diciembre) o un mes concreto.
2. Elegir el **año** y la **zona** (según tu rol, solo las tuyas o también «todo»).
3. Marcar si se quieren ver las actividades (sí / no).
4. Pulsar **planning vista tabla**.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_zones_que_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_zones_que`

## Objetivo

Elegir la zona de sacerdotes y el trimestre o mes del planning.

## Errores Documentados

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
