---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ca Posibles"
flujo: "actividadestudios.ca_posibles.gestionar.flujo"
preguntas: ["Como obtener datos en Ca Posibles?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.ca_posibles"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
source: "docs/catalogo/actividadestudios/flujos/ca_posibles.md"
estado_revision: "generado"
---

# Ayuda IA - Ca Posibles

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ca Posibles`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ca Posibles?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En `ca_posibles_que`, elegir centro N o AGD, periodo y opciones de filtro.
2. Pulsar **buscar**; el formulario envía a `ca_posibles.php`.
3. El controlador valida que haya centro seleccionado y consulta `ca_posibles_data`.
4. Se muestra el listado o cuadro de posibles CA por alumno.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/ca_posibles_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.ca_posibles`

## Objetivo

Tras elegir centro, periodo y filtros en `ca_posibles_que`, el usuario obtiene el cuadro de posibles CA por alumno: créditos cursables, asignaturas pendientes y enlaces de detalle. Misma lógica que `frontend/actividadestudios/controller/ca_posibles.php`; en modo lista, `pagina_link_spec` lo firma el front.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
