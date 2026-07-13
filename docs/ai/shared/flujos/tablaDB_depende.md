---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "shared"
titulo: "Desplegable dependiente"
flujo: "shared.tablaDB_depende.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
endpoints: ["/src/shared/tablaDB_depende_datos"]
source: "docs/catalogo/shared/flujos/tablaDB_depende.md"
estado_revision: "generado"
---

# Ayuda IA - Desplegable dependiente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Desplegable dependiente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `shared.pantalla.tablaDB_formulario_ver`

## Objetivo

Actualizar las opciones de un campo hijo cuando cambia el valor del campo padre en un formulario `tablaDB` (p. ej. centro → lugar en inventario).

## Errores Documentados

- `Error AJAX mostrado en alert con json.mensaje.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
