---
id: "actividadtarifas.pantalla.tarifa_ubi_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Ubi Lista"
controller: "frontend/actividadtarifas/controller/tarifa_ubi_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tarifa_ubi_lista_data"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
campos: ["post.id_ubi", "post.year"]
acciones: ["fnjs_copiar_tarifas", "fnjs_modificar"]
estado_revision: "revisado"
---

# Tarifa Ubi Lista

Fragmento AJAX: tabla de `TarifaUbi` para la casa/año seleccionados; emite `token_copiar` (HashB).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_lista_data`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Campos Detectados

- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_copiar_tarifas`
- `fnjs_modificar`

## Manual De Usuario

Recibe `id_ubi` y `year` del formulario principal. Muestra botón copiar tarifas del año anterior
si `puede_anadir`; pasa `token_copiar` a `fnjs_copiar_tarifas`.

## Ruta de menú

Sin entrada propia; fragmento de `tarifa_ubi.php`.
