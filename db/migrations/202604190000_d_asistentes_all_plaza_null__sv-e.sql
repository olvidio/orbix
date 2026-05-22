-- d_asistentes_all.plaza: 0 no es PlazaId válido → NULL (sv-e, datos).
UPDATE publicv.d_asistentes_all SET plaza = NULL WHERE plaza = 0;
