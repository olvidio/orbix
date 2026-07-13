---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta"
flujo: "notas.acta.gestionar.flujo"
preguntas: ["Como crear en Acta?", "Como eliminar en Acta?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_select", "notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_nueva"]
source: "docs/catalogo/notas/flujos/acta.md"
estado_revision: "generado"
---

# Ayuda IA - Acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Acta?
- Como eliminar en Acta?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. En `acta_select`, pulsar **añadir acta** (`fnjs_nuevo`).
2. Se abre `acta_ver` en modo nuevo; rellenar asignatura, actividad, fechas y tribunal.
3. Guardar (`fnjs_guardar_acta` → `acta_nueva`).

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_nueva`
- `/src/notas/acta_ver_form_data`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_eliminar`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_ver`

## Objetivo

Ciclo completo de actas: listar en `acta_select`, abrir `acta_ver`, crear (`acta_nueva`), modificar (`acta_modificar`) o eliminar (`acta_eliminar`), con PDF e impresión.

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
