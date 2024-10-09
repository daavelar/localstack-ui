# LocalStack UI

LocalStack UI é uma interface de usuário intuitiva projetada para simplificar o gerenciamento de recursos do LocalStack, com foco em serviços de mensageria como SNS (Simple Notification Service) e SQS (Simple Queue Service).

## Características

- **Gerenciamento de Tópicos**: Crie, visualize, edite e exclua tópicos SNS.
- **Gerenciamento de Filas**: Crie, visualize, edite e exclua filas SQS.
- **Gerenciamento de Inscrições**: Associe filas a tópicos e gerencie inscrições.
- **Visualização de Mensagens**: Veja mensagens em filas e tópicos em tempo real.
- **Interface Amigável**: Design intuitivo para facilitar operações complexas.

## Pré-requisitos

- Node.js (versão 14 ou superior)
- LocalStack rodando em sua máquina local

## Instalação

1. Clone o repositório:
   ```
   git clone https://github.com/daavelar/localstack-ui.git
   ```

2. Entre no diretório do projeto:
   ```
   cd localstack-ui
   ```

3. Instale as dependências:
   ```
   npm install
   ```

4. Inicie a aplicação:
   ```
   npm start
   ```

5. Acesse a aplicação em seu navegador em `http://localhost:3000`

## Uso

1. **Gerenciar Tópicos**:
   - Navegue até a seção "Tópicos"
   - Use os botões para criar, editar ou excluir tópicos
   - Visualize detalhes dos tópicos existentes

2. **Gerenciar Filas**:
   - Vá para a seção "Filas"
   - Crie novas filas ou gerencie as existentes
   - Veja atributos e configurações das filas

3. **Gerenciar Inscrições**:
   - Na seção "Inscrições", associe filas a tópicos
   - Gerencie inscrições existentes

4. **Visualizar Mensagens**:
   - Selecione uma fila ou tópico para ver suas mensagens
   - Acompanhe mensagens em tempo real

## Contribuindo

Contribuições são bem-vindas! Por favor, leia o arquivo CONTRIBUTING.md para detalhes sobre nosso código de conduta e o processo para enviar pull requests.

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo LICENSE.md para detalhes.

## Contato

Seu Nome - [@seutwitter](https://twitter.com/seutwitter) - email@exemplo.com

Link do Projeto: [https://github.com/daavelar/localstack-ui](https://github.com/daavelar/localstack-ui)
