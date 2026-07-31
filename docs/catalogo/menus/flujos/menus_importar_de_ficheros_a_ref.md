---
id: "menus.menus_importar_de_ficheros_a_ref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Restaurar menús ref→DL"
capacidad: "menus.menus_importar_de_ficheros_a_ref.gestionar"
pantallas_principales: ["menus.pantalla.menus_importar_de_ficheros_a_ref"]
fragmentos: []
acciones: ["confirmar", "ejecutar"]
endpoints: ["/src/menus/menus_importar_de_ficheros_a_ref"]
estado_revision: "revisado"
---

# Flujo - Restaurar menús por defecto (ref→DL)

Copia menús de referencia (BD pública) a `aux_*` del esquema. No usa ficheros; el nombre histórico
«de ficheros» es engañoso (ver flujo `menus_exportar_ref_a_ficheros` para SQL).

## Objetivo De Usuario

Dejar los menús del esquema (o de todas las DL si dlb) como la referencia por defecto.

## Punto De Entrada

- URL directa `/src/menus/menus_importar_de_ficheros_a_ref` (sin entrada de menú propia).

## Escenarios

### Confirmar (`seguro=2`)

1. Abrir endpoint → mensajes de advertencia + enlace «continuar» (`seguro=1`).
2. Si `miDele()==='dlb'`: enlace extra «Poner todas las dl igual» (`seguro=1&todos=1`).

### Ejecutar (`seguro=1`)

1. Recorre esquema(s); salta `H-Hv`.
2. Copia ref→aux; en sf no toca `aux_grupmenu_rol`.

## Endpoints Del Flujo

- `/src/menus/menus_importar_de_ficheros_a_ref`

## Ruta de menú

sin entrada de menú en el índice
