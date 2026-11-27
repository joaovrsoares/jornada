-- Migração para separar nome e sobrenome em bancos já existentes.
-- Execute uma única vez em cada banco criado antes desta versão:
--   docker compose -f compose.dev.yaml exec -T app \
--     mariadb -u jornada -pjornada_dev jornada < docker/db/migrations/001_nome_sobrenome.sql

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS sobrenome VARCHAR(100) NOT NULL DEFAULT '' AFTER nome;

-- Nomes já conhecidos: mantém o primeiro nome composto e move o restante
-- para sobrenome, conforme a definição do projeto.
UPDATE usuarios SET nome = 'João Vitor', sobrenome = 'Soares da Rosa'
WHERE nome = 'João Vitor Soares da Rosa';

UPDATE usuarios SET nome = 'Isaias', sobrenome = 'Morais'
WHERE nome = 'Isaias Morais';

UPDATE usuarios SET nome = 'Marcos', sobrenome = 'Vinicius Dutra'
WHERE nome = 'Marcos Vinicius Dutra';

-- Demais nomes: primeiro token vira nome e o restante vira sobrenome.
UPDATE usuarios
SET sobrenome = TRIM(SUBSTRING(nome, LOCATE(' ', nome) + 1))
WHERE sobrenome = '' AND LOCATE(' ', nome) > 0;

UPDATE usuarios
SET nome = SUBSTRING_INDEX(nome, ' ', 1)
WHERE sobrenome <> ''
  AND nome NOT IN ('João Vitor', 'Isaias', 'Marcos')
  AND LOCATE(' ', nome) > 0;
