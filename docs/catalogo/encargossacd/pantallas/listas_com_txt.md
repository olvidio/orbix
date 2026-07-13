---
id: "encargossacd.pantalla.listas_com_txt"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas Com Txt"
controller: "frontend/encargossacd/controller/listas_com_txt.php"
vistas: ["frontend/encargossacd/view/listas_com_txt.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/listas_com_txt_get.php", "frontend/encargossacd/controller/listas_com_txt_update.php"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
capacidades: ["encargossacd.listas_com_txt.gestionar"]
campos: ["form.clave", "form.comunicacion", "form.idioma", "html.comunicacion"]
acciones: ["fnjs_get_texto", "fnjs_guardar"]
estado_revision: "revisado"
---

# Listas Com Txt

Pantalla para editar los textos de comunicacion de los encargos a los SACD.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_com_txt.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas_com_txt.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/listas_com_txt_get.php`
- `frontend/encargossacd/controller/listas_com_txt_update.php`

## Endpoints Usados

- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Capacidades Relacionadas

- `encargossacd.listas_com_txt.gestionar`

## Campos Detectados

- `form.clave`
- `form.comunicacion`
- `form.idioma`
- `html.comunicacion`

## Acciones Detectadas

- `fnjs_get_texto`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

