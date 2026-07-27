---
id: "encargossacd.pantalla.propuestas_aprobar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Propuestas Aprobar"
controller: "frontend/encargossacd/controller/propuestas_aprobar.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/encargossacd/propuestas_aprobar"]
capacidades: []
campos: []
acciones: []
estado_revision: "revisado"
---

# Propuestas Aprobar

Proxy que llama a `/src/encargossacd/propuestas_aprobar` e imprime el texto de resultado
(«Hecho!») en `#main`. Invocado tras confirmación desde el menú de propuestas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/propuestas_aprobar.php`

## Vistas Relacionadas

Sin vista propia (echo de texto).

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/propuestas_aprobar`

## Capacidades Relacionadas

Ninguna ficha de capacidad aún.

## Campos Detectados

Ninguno.

## Acciones Detectadas

Ninguna.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (acceso desde Encargos > propuestas → «aprobar las propuestas»)
- **Pills2:** sin entrada de menú en el índice
