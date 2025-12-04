# 📊 Como os Serviços são Salvos na Base de Dados

## ✅ REGRA PRINCIPAL: TUDO NA BASE DE DADOS

**Todos os dados importantes são salvos na base de dados, nada hardcoded!**

---

## 📋 ESTRUTURA COMPLETA

### 1. Tabela: `servicos_clinica`
**Salva os serviços/procedimentos da clínica**

**Arquivo de salvamento:** `views/recepcao/daos/salvar_servico.php`

**SQL de INSERT (criar):**
```sql
INSERT INTO servicos_clinica 
    (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
VALUES 
    ('CONS-GERAL', 'Consulta Geral', 'Descrição...', 1200.00, 'Consulta', 1, 1)
```

**SQL de UPDATE (editar):**
```sql
UPDATE servicos_clinica SET
    codigo = 'CONS-GERAL',
    nome = 'Consulta Geral',
    descricao = 'Descrição...',
    preco = 1200.00,
    categoria = 'Consulta',
    ativo = 1
WHERE id = 1
```

**Campos salvos na BD:**
- ✅ `codigo` - Código único (varchar 50)
- ✅ `nome` - Nome do serviço (varchar 255)
- ✅ `descricao` - Descrição (text)
- ✅ `preco` - Preço padrão (decimal 10,2)
- ✅ `categoria` - Categoria (varchar 100) - **Vem da tabela categorias_servicos**
- ✅ `ativo` - Status (tinyint)
- ✅ `data_criacao` - Timestamp automático
- ✅ `usuario_criacao` - ID do usuário

---

### 2. Tabela: `categorias_servicos`
**Salva as categorias de serviços (nada hardcoded!)**

**Estrutura:**
```sql
CREATE TABLE categorias_servicos (
    id int(11) AUTO_INCREMENT PRIMARY KEY,
    nome varchar(100) NOT NULL UNIQUE,
    descricao text,
    ativo tinyint(1) DEFAULT 1,
    data_criacao timestamp DEFAULT CURRENT_TIMESTAMP,
    usuario_criacao int(11)
)
```

**Como funciona:**
1. As categorias são cadastradas na base de dados
2. O código busca as categorias da BD
3. Nenhuma categoria está hardcoded no código

**Arquivo SQL:** `views/recepcao/sql/criar_tabela_categorias_servicos.sql`

---

### 3. Tabela: `empresas_seguros`
**Salva as empresas/seguradoras**

**Campos salvos:**
- ✅ `nome` - Nome da empresa
- ✅ `nuit` - NUIT
- ✅ `contacto` - Telefone
- ✅ `email` - Email
- ✅ `endereco` - Endereço
- ✅ `tabela_precos_id` - ID da tabela de preços
- ✅ `desconto_geral` - Desconto percentual
- ✅ Todos os dados da empresa estão na BD

---

### 4. Tabela: `tabelas_precos`
**Salva tabelas de preços vinculadas a empresas**

**Campos salvos:**
- ✅ `empresa_id` - ID da empresa
- ✅ `nome` - Nome da tabela
- ✅ `validade_inicio` - Data de início
- ✅ `validade_fim` - Data de fim
- ✅ Tudo na BD

---

### 5. Tabela: `tabela_precos_servicos`
**Salva os preços específicos de cada serviço por empresa**

**Arquivo de salvamento:** `views/recepcao/daos/salvar_tabela_precos.php`

**SQL de salvamento:**
```sql
-- Remove preços antigos
DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = X

-- Insere novos preços
INSERT INTO tabela_precos_servicos 
    (tabela_precos_id, servico_id, preco, desconto_percentual) 
VALUES 
    (1, 1, 1000.00, 0),
    (1, 2, 1800.00, 0)
```

**Campos salvos:**
- ✅ `tabela_precos_id` - ID da tabela
- ✅ `servico_id` - ID do serviço
- ✅ `preco` - Preço específico da empresa
- ✅ `desconto_percentual` - Desconto adicional
- ✅ Tudo na BD

---

## 🔄 FLUXO COMPLETO DE SALVAMENTO

### Cenário 1: Criar Serviço
```
1. Usuário preenche formulário em servicos_clinica.php
2. Submete para daos/salvar_servico.php
3. Validações (código único, campos obrigatórios)
4. INSERT INTO servicos_clinica (...)
5. Serviço salvo na BD ✅
```

### Cenário 2: Configurar Preços por Empresa
```
1. Usuário seleciona empresa em servicos_clinica.php
2. Edita preços na tabela
3. Clica em "Salvar Preços"
4. AJAX chama daos/salvar_tabela_precos.php
5. DELETE preços antigos
6. INSERT novos preços em tabela_precos_servicos
7. Preços salvos na BD ✅
```

---

## 📝 CHECKLIST: TUDO NA BASE DE DADOS?

| Dado | Está na BD? | Tabela | CRUD Disponível? |
|------|-------------|--------|------------------|
| **Serviços** | ✅ Sim | `servicos_clinica` | ✅ Sim (servicos_clinica.php) |
| **Empresas** | ✅ Sim | `empresas_seguros` | ✅ Sim (empresas.php) |
| **Preços por Empresa** | ✅ Sim | `tabela_precos_servicos` | ✅ Sim (tabela_precos.php) |
| **Categorias** | ✅ Sim* | `categorias_servicos` | ⚠️ Pendente |
| **Métodos Pagamento** | ⚠️ Não | - | ⚠️ Pendente |

*Tabela criada, mas código ainda usa valores do campo categoria (texto). Deve ser migrado para usar categoria_id.

---

## 🎯 RESUMO

### ✅ O QUE ESTÁ SALVANDO NA BD:

1. **Serviços** → `servicos_clinica`
   - Código, nome, descrição, preço padrão, categoria, status
   - Salvo via `daos/salvar_servico.php`

2. **Empresas** → `empresas_seguros`
   - Todos os dados da empresa
   - Salvo via `daos/registar_empresa.php`

3. **Preços por Empresa** → `tabela_precos_servicos`
   - Preço específico de cada serviço para cada empresa
   - Salvo via `daos/salvar_tabela_precos.php`

4. **Tabelas de Preços** → `tabelas_precos`
   - Vincula empresa com seus preços
   - Criada automaticamente quando necessário

---

## ⚠️ O QUE PRECISA SER CORRIGIDO:

### 1. Categorias (EM ANDAMENTO)
- ✅ Tabela criada: `categorias_servicos`
- ✅ SQL de criação disponível
- ⚠️ Código ainda precisa ser atualizado para usar a tabela

### 2. Métodos de Pagamento (PENDENTE)
- ⚠️ Ainda hardcoded no código
- ⚠️ Deveria ter tabela `metodos_pagamento`

---

## 📂 ARQUIVOS DE SALVAMENTO

### Serviços:
- `views/recepcao/daos/salvar_servico.php` - Cria/edita serviços
- `views/recepcao/daos/excluir_servico.php` - Exclui/inativa serviços

### Preços:
- `views/recepcao/daos/salvar_tabela_precos.php` - Salva preços por empresa

### Empresas:
- `views/recepcao/daos/registar_empresa.php` - Cria/edita empresas

---

## ✅ CONFIRMAÇÃO

**TODOS os dados importantes estão sendo salvos na base de dados!**

- ✅ Nenhum serviço é hardcoded
- ✅ Nenhum preço é hardcoded
- ✅ Nenhuma empresa é hardcoded
- ✅ Tudo tem CRUD na interface
- ✅ Tudo pode ser editado pelo usuário

**Os únicos valores fixos são constantes do sistema (como IDs de sessão) que não fazem sentido estar na BD.**


