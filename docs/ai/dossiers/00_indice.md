---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "dossiers"
flujos: 5
pantallas: 4
endpoints: 6
estado_revision: "generado"
---

# Ayuda IA - dossiers

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Ver la relación de dossiers -> `flujos/dossiers_lista_fichas.md`
- Abrir un dossier -> `flujos/dossiers_ver_pantalla.md`
- Consultar o cambiar los permisos de un dossier -> `flujos/perm_dossier_ver.md`
- Elegir los dossiers a configurar -> `flujos/perm_dossiers.md`
- Guardar o eliminar un tipo de dossier -> `flujos/tipo_dossier.md`

## Pantallas Disponibles

- Dossiers Ver -> `pantallas/dossiers_ver.md`
- Lista Dossiers -> `pantallas/lista_dossiers.md`
- Perm Dossier Ver -> `pantallas/perm_dossier_ver.md`
- Perm Dossiers -> `pantallas/perm_dossiers.md`
