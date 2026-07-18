-- Equivalente sf de 202606120001_aux_menus_grupo_zona__sv-e.sql (sin réplica; esquemas *f / publicf).
-- aux_menus:  (sf, datos, todos los esquemas *f).

UPDATE *.aux_menus SET menu = 'grupos', parametros='clase_info=src\zonassacd\domain\InfoZonaGrupo' WHERE menu = 'zonas geogr.';

UPDATE *.aux_menus SET parametros = 'que=ver&ssfsv=sv&sasistentes=sssc' WHERE parametros = 'que=ver&ssfsv=sv&sasistentes=sss+';
UPDATE *.aux_menus SET parametros = 'sasistentes=sssc&sactividad=cv&que=list_cjto' WHERE parametros = 'sasistentes=sss+&sactividad=cv&que=list_cjto';
