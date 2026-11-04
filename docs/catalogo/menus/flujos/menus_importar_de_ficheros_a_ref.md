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

- `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` (sin entrada de menú propia).

## Escenarios

### Confirmar (`seguro=2`)

1. Abrir la pantalla: salen los mensajes de advertencia y el enlace **continuar**.
2. Si la delegación es dlb, aparece además el enlace **Poner todas las dl igual**.

### Ejecutar (`seguro=1`)

1. El proceso recorre las delegaciones (salta la `H-Hv`).
2. Copia los menús de referencia a los de cada delegación; en la sf no toca los grupos de menú por rol.

## Endpoints Del Flujo

- `/src/menus/menus_importar_de_ficheros_a_ref`

## Ruta de menú

sin entrada de menú en el índice
