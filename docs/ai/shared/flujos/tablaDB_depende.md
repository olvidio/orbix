---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Cambiar un campo que condiciona otro"
flujo: "shared.tablaDB_depende.gestionar.flujo"
preguntas: ["Como cambiar un campo que condiciona otro en Cambiar un campo que condiciona otro?"]
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_depende_datos"]
source: "docs/catalogo/shared/flujos/tablaDB_depende.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar un campo que condiciona otro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar un campo que condiciona otro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar un campo que condiciona otro en Cambiar un campo que condiciona otro?

## Donde Entrar

- Mantenimiento genérico de tablas (formulario) (`shared.pantalla.tablaDB_formulario_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar un campo que condiciona otro

1. En el formulario, cambiar el desplegable principal (por ejemplo el centro).
2. El desplegable dependiente (por ejemplo el lugar) se rellena con las opciones válidas para ese valor.

Referencias tecnicas para verificar la respuesta:
- `/src/shared/tablaDB_depende_datos`

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`
- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Actualizar las opciones de un campo cuando cambia el valor de otro del que depende en un formulario de mantenimiento (por ejemplo, al cambiar el centro se actualizan los lugares en inventario).

## Errores Documentados

- `Error AJAX mostrado en alert con json.mensaje.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
