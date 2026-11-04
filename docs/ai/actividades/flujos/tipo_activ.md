---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver los tipos de actividad"
flujo: "actividades.tipo_activ.gestionar.flujo"
preguntas: ["Como ver los tipos de actividad en Ver los tipos de actividad?", "Como crear o modificar un tipo de actividad en Ver los tipos de actividad?", "Como eliminar un tipo de actividad en Ver los tipos de actividad?"]
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
source: "docs/catalogo/actividades/flujos/tipo_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los tipos de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los tipos de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los tipos de actividad en Ver los tipos de actividad?
- Como crear o modificar un tipo de actividad en Ver los tipos de actividad?
- Como eliminar un tipo de actividad en Ver los tipos de actividad?

## Donde Entrar

- Gestión de tipos de actividad (`actividades.pantalla.tipo_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los tipos de actividad

1. Abrir la gestión de tipos de actividad: aparece la tabla con todos los tipos.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Crear o modificar un tipo de actividad

1. Pulsar **nuevo**, o seleccionar un tipo y pulsar **modificar**.
2. Rellenar el formulario y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Eliminar un tipo de actividad

1. Seleccionar el tipo y pulsar **eliminar**.
2. Confirmar el aviso.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.tipo_activ`

## Objetivo

Ver los tipos de actividad, crear uno nuevo, cambiar el nombre de uno existente o eliminarlo.

## Errores Documentados

- `tipo de actividad no encontrado`
- `Id incorrecto (alta)`
- `hay un error, no se ha guardado / hay un error, no se ha eliminado`
- `Aviso: IMPORTANTE: Debe añadir un proceso… (con procesos instalado)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
