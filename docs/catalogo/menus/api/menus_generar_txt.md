---
id: "menus.menus_generar_txt"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_generar_txt"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_generar_txt.php"
entrada: []
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
errores: []
frontend_referencias: []
casos_uso: []
tags: ["menus", "generar", "txt", "traducciones"]
estado_revision: "revisado"
---

# Generar fichero de textos para gettext

Regenera `frontend/menus/view/traducir_menu.phtml` con llamadas `_()` por cada etiqueta de menú activo y
tipos de repetición de actividades.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Escribe PHP con `_('texto')` para extracción gettext. **No usa `ContestarJson`**; imprime error si el fichero
no es escribible.

## Salida

- Fichero en disco; respuesta HTTP vacía o mensajes `print` de error.

## Permisos

- Menú `sistema > traducciones > menus a texto` (`_referencia_menus.md`).

## Frontend Relacionado

- Entrada vía menú traducciones (URL legacy `src/menus/menus_generar_txt`).
