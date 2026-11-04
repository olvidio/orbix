---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "los asistentes de una actividad"
flujo: "asistentes.asistente.gestionar.flujo"
preguntas: ["Como gestionar los asistentes de una actividad en los asistentes de una actividad?", "Como mover un asistente a otra actividad en los asistentes de una actividad?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/asistentes/asistente_eliminar", "/src/asistentes/asistente_guardar"]
source: "docs/catalogo/asistentes/flujos/asistente.md"
estado_revision: "generado"
---

# Ayuda IA - los asistentes de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `los asistentes de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar los asistentes de una actividad en los asistentes de una actividad?
- Como mover un asistente a otra actividad en los asistentes de una actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar los asistentes de una actividad

1. Marcar la actividad y pulsar **asistentes**. Se abre «Relación de asistentes» con un enlace **( dossiers )** y una tabla (nombre, dl, propio, estudios, observaciones, teléfono, mail, ctr, propietario de la plaza…).
2. El color de cada fila indica su estado: **pedida**, **en espera**, **asignada** o **confirmada** (la leyenda está debajo).
3. Marcar una o varias filas (Ctrl+clic o Mayús+clic para varias) y pulsar **modificar asistencia**, **cambiar actividad**, **borrar asistencia**, **transferir a históricos**, **añadir cargo**, **modificar cargo**, **quitar cargo**, **plan estudios** o **E43**.
4. Bajo la tabla se indican las plazas (por ejemplo «plazas actividad: 31» y «dlb: 1 (de las 1 cedidas por dlmE) => ocupadas=1/(1) libres=0»).
5. Para añadir a alguien de la dl, pulsar **+ n** o **+ agd** (según el colectivo) en «Añadir asistente de la dl».

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Mover un asistente a otra actividad

1. Marcar al asistente y pulsar **cambiar actividad**.
2. Se abre «Mover a:» con las actividades del mismo tipo, cada una con sus «plazas libres/concedidas».
3. Elegir la actividad de destino y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Ver y gestionar los asistentes de una actividad: quién está, estado de su plaza, añadir, cambiar de actividad y quitar.

## Errores Documentados

- `falta id_activ_old`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `los datos de asistencia los modifica la dl del asistente`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
