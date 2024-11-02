## Projeto VWTurismo
Projeto criado em HTML, CSS, JAVASCRIPT, PHP E SQL, simulando um sistema de compra de passagens, trabalho desenvolvido para atender a disciplina de Desenvolvimento web 2.
(Estou refatorando).

### Para rodar o projeto no Docker
```
  docker-compose up -d
```
### Para acessar o container
```
  docker exec -it vw_turismo bash
```

### Para rodar as migrations dentro do container
```
  php database/create_all_tables.php
```
