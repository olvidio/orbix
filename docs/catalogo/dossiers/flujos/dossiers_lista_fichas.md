---
id: "dossiers.dossiers_lista_fichas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Dossiers Lista Fichas"
capacidad: "dossiers.dossiers_lista_fichas.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.lista_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
estado_revision: "revisado"
---

# Flujo - Dossiers Lista Fichas

## Objetivo De Usuario

Mostrar la tabla de carpetas de dossiers disponibles para la entidad actual, con iconos de permiso y enlace a cada ficha (`href_ver` firmado en frontend).

## Punto De Entrada

Sin entrada de menú directa; acceso embebido desde home persona/ubi, actividad u otras pantallas que enlazan `dossiers_ver.php`.

## Escenarios

### Listar carpetas disponibles

1. Con `id_dossier` vacío, `dossiers_ver` solicita filas a `dossiers_lista_fichas_data`.
2. El frontend firma `href_ver`/`href_abrir` con `DossiersListaSupport::signFilas`.
3. La vista pinta icono, descripción y símbolo de permiso (deny/eye/pencil).

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.lista_dossiers`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_lista_fichas_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
