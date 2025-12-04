# Sistema de Gestão de Preços por Empresa/Seguradora

## Visão Geral

Este sistema permite cadastrar procedimentos clínicos e atribuir preços diferentes para cada empresa/seguradora. Isso significa que o mesmo procedimento pode ter preços distintos dependendo do contrato com cada empresa.

## Estrutura do Sistema

### 1. Tabelas de Base de Dados

#### `servicos_clinica`
Armazena todos os serviços/procedimentos disponíveis na clínica:
- **id**: Identificador único
- **codigo**: Código do serviço (ex: CONS-GERAL)
- **nome**: Nome do serviço (ex: Consulta Geral)
- **descricao**: Descrição detalhada
- **preco**: Preço padrão base
- **categoria**: Categoria (Consulta, Exame, Procedimento, etc.)
- **ativo**: Status (ativo/inativo)
- **data_criacao**: Data de criação
- **usuario_criacao**: Usuário que criou

#### `empresas_seguros`
Cadastro de empresas e seguradoras:
- **id**: Identificador único
- **nome**: Nome da empresa
- **nuit**: NUIT da empresa
- **contacto**: Telefone
- **email**: Email
- **endereco**: Endereço completo
- **tabela_precos_id**: Referência à tabela de preços ativa
- **contrato**: Número do contrato
- **data_inicio_contrato**: Data de início
- **data_fim_contrato**: Data de fim
- **desconto_geral**: Desconto percentual geral (quando não há preços específicos)
- **ativo**: Status
- **observacoes**: Observações
- **data_criacao**: Data de criação
- **usuario_criacao**: Usuário que criou

#### `tabelas_precos`
Tabelas de preços vinculadas a empresas:
- **id**: Identificador único
- **empresa_id**: Referência à empresa
- **nome**: Nome da tabela (ex: Tabela Padrão)
- **descricao**: Descrição
- **validade_inicio**: Data de início da validade
- **validade_fim**: Data de fim da validade
- **ativo**: Status
- **data_criacao**: Data de criação
- **usuario_criacao**: Usuário que criou

#### `tabela_precos_servicos`
Preços específicos de cada serviço por tabela:
- **id**: Identificador único
- **tabela_precos_id**: Referência à tabela de preços
- **servico_id**: Referência ao serviço
- **preco**: Preço contratado para este serviço
- **desconto_percentual**: Desconto percentual aplicado
- **ativo**: Status
- **data_criacao**: Data de criação

## Como Usar o Sistema

### 1. Cadastrar Serviços/Procedimentos

1. Aceda ao menu **Serviços/Procedimentos** na barra lateral
2. Clique em **Novo Serviço**
3. Preencha os campos:
   - **Código**: Código único do serviço (ex: CONS-GERAL)
   - **Categoria**: Selecione a categoria apropriada
   - **Nome**: Nome descritivo do serviço
   - **Descrição**: Descrição detalhada (opcional)
   - **Preço Padrão**: Preço base do serviço em MT
   - **Status**: Ativo ou Inativo
4. Clique em **Salvar Serviço**

**Nota:** O preço padrão é o valor base. Cada empresa pode ter um preço diferente.

### 2. Cadastrar Empresas/Seguradoras

1. Aceda ao menu **Empresas/Seguros** → **Nova Empresa**
2. Preencha os dados da empresa:
   - Nome, NUIT, Contacto, Email, Endereço
   - Dados do contrato (número, datas)
   - Desconto geral (opcional - aplicado quando não há preços específicos)
3. Salve a empresa

### 3. Configurar Preços por Empresa

1. Aceda ao menu **Empresas/Seguros** → **Ver Empresas**
2. Na lista de empresas, clique no botão **Preços** da empresa desejada
3. Será apresentada uma tabela com todos os serviços disponíveis
4. Para cada serviço, configure:
   - **Preço Contratado**: Preço específico para esta empresa
   - **Desconto (%)**: Desconto percentual aplicado
5. Clique em **Salvar Tabela de Preços**

**Importante:** 
- Se não configurar preços específicos, será usado o preço padrão do serviço
- Se a empresa tiver um desconto geral configurado, este será aplicado automaticamente
- Preços específicos têm prioridade sobre o desconto geral

### 4. Como o Sistema Calcula Preços

O sistema segue esta ordem de prioridade:

1. **Preço Específico da Empresa**: Se existir preço na `tabela_precos_servicos`
2. **Desconto Geral da Empresa**: Se não houver preço específico, aplica o desconto geral sobre o preço padrão
3. **Preço Padrão**: Se não houver nem preço específico nem desconto geral

**Exemplo:**
- Serviço: Consulta Geral
- Preço Padrão: 1.200,00 MT
- Empresa A (Vulcan): Preço específico de 1.000,00 MT
- Empresa B (Monte Sinai): Sem preço específico, desconto geral de 15%
  - Preço final = 1.200,00 - 15% = 1.020,00 MT

## Fluxo de Trabalho Recomendado

### Primeira Configuração

1. **Cadastrar todos os serviços/procedimentos** oferecidos pela clínica
2. **Cadastrar as empresas/seguradoras** com as quais tem contrato
3. **Configurar as tabelas de preços** para cada empresa

### Manutenção Regular

- **Atualizar preços**: Quando houver revisão de contrato
- **Adicionar novos serviços**: Quando a clínica oferecer novos procedimentos
- **Verificar validade de contratos**: Monitorar datas de fim de contrato
- **Inativar serviços descontinuados**: Em vez de excluir

## Funcionalidades Especiais

### Importação de Dados (Futura)

Atualmente, os ficheiros Excel que possui podem ser usados como referência para:
1. Listar todos os serviços a serem cadastrados
2. Identificar os preços específicos de cada empresa
3. Configurar manualmente no sistema através das interfaces criadas

### Relatórios

O sistema permite:
- Ver todos os serviços cadastrados
- Ver todas as empresas e seus status
- Editar preços a qualquer momento
- Histórico de criação e alterações

### Segurança

- Apenas usuários autenticados podem aceder
- Serviços em uso não podem ser excluídos (apenas inativados)
- Validação de códigos únicos
- Proteção contra SQL injection

## Estrutura de Ficheiros

```
views/recepcao/
├── servicos_clinica.php          # Gestão de serviços
├── empresas.php                  # Lista de empresas
├── nova_empresa.php              # Criar empresa
├── editar_empresa.php            # Editar empresa
├── tabela_precos.php             # Configurar preços por empresa
└── daos/
    ├── salvar_servico.php        # Criar/editar serviço
    ├── excluir_servico.php       # Excluir/inativar serviço
    ├── salvar_tabela_precos.php  # Salvar preços da empresa
    └── buscar_precos_empresa.php # Buscar preços via AJAX
```

## Próximos Passos Sugeridos

1. **Importar dados dos ficheiros Excel**:
   - Criar script PHP para ler os ficheiros Excel
   - Importar automaticamente serviços e preços

2. **Relatórios**:
   - Relatório de preços por empresa
   - Comparação de preços entre empresas
   - Histórico de alterações de preços

3. **Notificações**:
   - Alertas de contratos próximos ao vencimento
   - Notificações de alterações de preços

4. **Validações**:
   - Impedir faturação com contratos vencidos
   - Validar preços antes de gerar documentos

## Suporte

Para questões ou problemas:
1. Verifique a documentação de cada módulo
2. Consulte os logs de erro do sistema
3. Entre em contato com o administrador do sistema

---

**Versão:** 1.0  
**Data:** Dezembro 2025  
**Autor:** Sistema IvoneERP
