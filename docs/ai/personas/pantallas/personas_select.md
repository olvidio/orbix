---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "personas"
titulo: "Resultado búsqueda personas"
pantalla: "personas.pantalla.personas_select"
preguntas: ["Que se puede hacer en Resultado búsqueda personas?", "Que campos tiene Resultado búsqueda personas?", "Que acciones hay en Resultado búsqueda personas?"]
capacidades: ["personas.personas_select.gestionar"]
endpoints: ["/src/personas/personas_select_data"]
source: "docs/catalogo/personas/pantallas/personas_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Resultado búsqueda personas

## Resumen

Tabla `web\Lista` con personas que cumplen los criterios de `personas_que`. Botones contextuales según colectivo, `permiso`, módulos instalados (`asistentes`, `notas`, `actividadestudios`, etc.) y ámbito (`rstgr` simplifica botones).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `form.que`
- `form.id_dossier`

## Acciones Detectadas

- `fnjs_home`
- `fnjs_ficha`
- `fnjs_dossiers`
- `fnjs_modificar`
- `fnjs_modificar_ctr`
- `fnjs_actividades`
- `fnjs_tessera`
- `fnjs_notas`
- `fnjs_matriculas`
- `fnjs_posibles_ca`
- `fnjs_peticion_activ`
- `fnjs_lista_activ`
- `fnjs_ficha_profe`
- `fnjs_imp_tessera`
- `fnjs_copiar_tessera`
- `fnjs_imp_certificado`
- `fnjs_upload_certificado`

## Capacidades Relacionadas

- `personas.personas_select.gestionar`

## Endpoints Relacionados

- `/src/personas/personas_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
