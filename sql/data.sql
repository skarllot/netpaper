INSERT INTO dbversion VALUES
	('0.1');

INSERT INTO language VALUES
	(1, 'pt-BR', 'Português (Brasil)');

INSERT INTO device_type VALUES
	(1, 'Switch'),
	(2, 'Patch Panel'),
	(3, 'Router'),
	(4, 'Hub'),
	(5, 'Access Point'),
	(6, 'Server Machine'),
	(7, 'Desktop Machine');

INSERT INTO device_type_lang VALUES
	-- pt-BR
	(1, 1, 'Switch'),
	(1, 2, 'Patch Panel'),
	(1, 3, 'Roteador'),
	(1, 4, 'Hub'),
	(1, 5, 'Access Point'),
	(1, 6, 'Servidor'),
	(1, 7, 'Estação de Trabalho');

INSERT INTO connection_type VALUES
	(1, 'Electrical Patch Cord'),
	(2, 'Optical Patch Cord'),
	(3, 'Electrical Cable'),
	(4, 'Optical Cable');

INSERT INTO connection_type_lang VALUES
	-- pt-BR
	(1, 1, 'Patch Cord Elétrico'),
	(1, 2, 'Patch Cord Óptico'),
	(1, 3, 'Cabo Elétrico'),
	(1, 4, 'Cabo Óptico');
