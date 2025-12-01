# Módulo de Recepção - Sistema de Gestão da Clínica

## Funcionalidades Implementadas

### ✅ Requisitos Funcionais (RF)

#### RF01 - Registo de Paciente ✅
- **Arquivo:** `novo_paciente.php`
- **DAO:** `daos/registar_paciente.php`
- Permite registar novos pacientes com todos os dados pessoais, contactos, documento e número de processo único

#### RF02 - Pesquisa de Paciente ✅
- **Arquivo:** `pacientes.php`
- **DAO:** `daos/pesquisar_paciente.php`
- Pesquisa em tempo real por nome, apelido, número de processo, documento ou contacto
- Interface com DataTables para melhor experiência

#### RF03 - Atualização de Dados do Paciente ✅
- **Arquivo:** `editar_paciente.php`
- **DAO:** `daos/atualizar_paciente.php`
- Permite atualizar dados do paciente (contacto, endereço, etc.)
- Número de processo não pode ser alterado (regra de negócio)

#### RF04 - Criação de Fatura de Atendimento ✅
- **Arquivo:** `nova_fatura.php`
- **DAO:** `daos/criar_fatura.php`
- Gera automaticamente fatura com número único (formato: FAT-ANO-NUMERO)
- Integrado com seleção de paciente e serviços

#### RF05 - Seleção de Tipo de Serviço ✅
- **Arquivo:** `nova_fatura.php`
- Interface visual para seleção de serviços
- Serviços organizados por categoria
- Quantidade configurável por serviço

#### RF06 - Cálculo Automático de Preço ✅
- **Arquivo:** `nova_fatura.php`
- Cálculo automático do subtotal baseado nos serviços selecionados
- Suporte a desconto
- Cálculo do total final

#### RF07 - Emissão de Recibo ✅
- **Arquivo:** `imprimir_recibo.php`
- Gera recibo em PDF com todos os dados do paciente, serviços e pagamento
- Formato profissional para impressão

#### RF08 - Histórico de Atendimentos ✅
- **Arquivo:** `historico_paciente.php`
- Histórico completo de atendimentos por paciente
- Mostra faturas, serviços realizados e status
- Link para detalhes da fatura

#### RF11 - Controle de Caixa da Recepção ✅
- **Arquivo:** `caixa.php`
- Relatório diário de caixa
- Total de entradas por tipo de serviço
- Total recebido por método de pagamento
- Lista de faturas do dia

#### RF13 - Dashboard da Recepção ✅
- **Arquivo:** `dashboard.php`
- Estatísticas em tempo real:
  - Pacientes registrados hoje
  - Faturas criadas hoje
  - Faturas pendentes
  - Faturas pagas hoje
  - Total recebido hoje
  - Total de pacientes cadastrados
- Lista de faturas recentes
- Ações rápidas

#### RF14 - Cancelamento de Fatura ✅
- **Arquivo:** `cancelar_fatura.php`
- Permite cancelar faturas pendentes
- Registra usuário e motivo do cancelamento
- Apenas faturas pendentes podem ser canceladas

#### RF15 - Pagamento Integrado ✅
- **Arquivo:** `pagar_fatura.php`
- **DAO:** `daos/registar_pagamento.php`
- Suporte a múltiplos métodos de pagamento:
  - Dinheiro
  - M-Pesa
  - Emola
  - POS
- Campo para referência de pagamento (M-Pesa, Emola)
- Atualiza automaticamente o status da fatura para "paga"

### 📊 Estrutura de Banco de Dados

**Arquivo SQL:** `sql/create_tables_recepcao.sql`

Tabelas criadas:
1. `pacientes` - Dados dos pacientes
2. `servicos_clinica` - Tipos de serviços disponíveis
3. `faturas_atendimento` - Faturas de atendimento
4. `fatura_servicos` - Itens/serviços de cada fatura
5. `pagamentos_recepcao` - Registro de pagamentos
6. `historico_atendimentos` - Histórico de atendimentos
7. `caixa_recepcao` - Controle diário de caixa

### 📁 Estrutura de Arquivos

```
views/recepcao/
├── includes/
│   ├── head.php          # Head HTML (CSS, meta tags)
│   ├── header.php        # Cabeçalho com logo e menu usuário
│   ├── side_bar.php      # Menu lateral
│   └── footer.php        # Scripts JavaScript
├── daos/
│   ├── registar_paciente.php
│   ├── atualizar_paciente.php
│   ├── pesquisar_paciente.php
│   ├── criar_fatura.php
│   └── registar_pagamento.php
├── sql/
│   └── create_tables_recepcao.sql
├── assets/               # CSS, JS, fonts (copiados do admin)
├── dashboard.php         # Dashboard principal
├── pacientes.php         # Lista de pacientes
├── novo_paciente.php     # Formulário novo paciente
├── editar_paciente.php   # Formulário editar paciente
├── historico_paciente.php # Histórico de atendimentos
├── faturas.php           # Lista de faturas
├── nova_fatura.php       # Criar nova fatura
├── detalhes_fatura.php   # Detalhes da fatura
├── pagar_fatura.php      # Registrar pagamento
├── cancelar_fatura.php   # Cancelar fatura
├── imprimir_recibo.php   # Gerar recibo PDF
└── caixa.php             # Controle de caixa
```

### 🔐 Segurança

- Verificação de sessão em todas as páginas
- Verificação de categoria de usuário (apenas "recepcao")
- Redirecionamento automático se não autorizado
- Proteção contra SQL Injection (mysqli_real_escape_string)
- Validação de dados nos formulários

### 🎨 Interface

- Mesmo estilo visual do módulo admin (farmacia)
- Responsivo e moderno
- DataTables para listagens
- Bootstrap para componentes
- Font Awesome para ícones

### 📝 Próximos Passos

1. Executar o script SQL para criar as tabelas
2. Configurar serviços padrão na tabela `servicos_clinica`
3. Testar todas as funcionalidades
4. Adicionar mais serviços conforme necessário
5. Configurar permissões de acesso

### ⚠️ Importante

**Antes de usar o sistema, execute o script SQL:**
```sql
-- Executar o arquivo: views/recepcao/sql/create_tables_recepcao.sql
```

Este script criará todas as tabelas necessárias e inserirá alguns serviços padrão.

