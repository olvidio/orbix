---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Consultar o cambiar los permisos de un dossier"
flujo: "dossiers.perm_dossier_ver.gestionar.flujo"
preguntas: ["Como consultar o cambiar los permisos de un dossier en Consultar o cambiar los permisos de un dossier?"]
pantallas_principales: ["dossiers.pantalla.perm_dossier_ver"]
fragmentos: ["dossiers.pantalla.perm_dossier_ver"]
endpoints: ["/src/dossiers/perm_dossier_ver_data"]
source: "docs/catalogo/dossiers/flujos/perm_dossier_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar o cambiar los permisos de un dossier

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar o cambiar los permisos de un dossier`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar o cambiar los permisos de un dossier en Consultar o cambiar los permisos de un dossier?

## Donde Entrar

- Perm Dossier Ver (`dossiers.pantalla.perm_dossier_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar o cambiar los permisos de un dossier

1. En la lista de **perm_dossiers**, abrir el tipo de dossier.
2. Revisar su descripción, el código y las máscaras de permiso. «depende_modificar» activo implica que solo modificará la oficina correspondiente (agd para los agd, sm para los n, sg para los s).
3. El formulario también muestra campos técnicos editables (**tabla_from**, **tabla_to**, **clase**, **código**). Es intencionado que un administrador de delegación pueda cambiarlos, pero conviene no tocarlos salvo que se sepa lo que se hace.
4. Pulsar **guardar cambios**; después se vuelve a la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/dossiers/perm_dossier_ver_data`

## Pantallas Y Fragmentos Relacionados

- `dossiers.pantalla.perm_dossier_ver`
- `dossiers.pantalla.perm_dossier_ver`

## Objetivo

Ver o cambiar la definición y los permisos de un tipo de dossier, y volver a la lista.

## Errores Documentados

- `No se encuentra el dossier: <id>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
