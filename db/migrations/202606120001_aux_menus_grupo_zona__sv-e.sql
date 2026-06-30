-- aux_menus:  (sv-e, datos, todos los esquemas *v).

UPDATE *.aux_menus SET menu = 'grupos', parametros='clase_info=src\zonassacd\domain\InfoZonaGrupo' WHERE menu = 'zonas geogr.';

UPDATE *.aux_menus SET parametros = 'que=ver&ssfsv=sv&sasistentes=sssc' WHERE parametros = 'que=ver&ssfsv=sv&sasistentes=sss+';
UPDATE *.aux_menus SET parametros = 'sasistentes=sssc&sactividad=cv&que=list_cjto' WHERE parametros = 'sasistentes=sss+&sactividad=cv&que=list_cjto';
