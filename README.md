# Jornada
Um projeto em desenvolvimento para auxiliar na organização do ponto pessoal, controlando os horários de entrada e saída, horas extras, faltas e banco de horas.

### Execução
Para executar e desenvolver, é necessário o Docker e o Docker Compose.<br>
Windows: `winget install --id Docker.DockerDesktop`<br>
macOS: `brew install --cask docker`<br>

Com o docker instalado, é só rodar o build:
```bash
git clone https://github.com/joaovrsoares/jornada.git

cd jornada

docker compose -f compose.dev.yaml up --build -d
```