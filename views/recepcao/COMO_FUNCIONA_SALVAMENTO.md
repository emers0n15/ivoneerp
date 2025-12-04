# 📊 Como os Serviços São Salvos na Base de Dados

## ✅ REGRA RESPEITADA: TUDO NA BASE DE DADOS!

**Confirmado:** Todos os dados importantes estão sendo salvos na base de dados através de **CRUDs completos**. Nada hardcoded!

---

## 🔄 FLUXO DE SALVAMENTO

### **1. Criar Serviço**

**Arquivo:** `views/recepcao/daos/salvar_servico.php`

**SQL executado:**
```sql
INSERT INTO servicos_clinica 
    (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
VALUES 
    ('CONS-GERAL', 'Consulta Geral', 'Descrição...', 1200.00, 'Consulta', 1, 1)
```

**Campos salvos na BD:**
- ✅ `codigo` → varchar(50) - Código único do serviço
- ✅ `nome` → varchar(255) - Nome do serviço
- ✅ `descricao` → text - Descrição
- ✅ `preco` → decimal(10,2) - Preço padrão
- ✅ `categoria` → varchar(100) - Categoria
- ✅ `ativo` → tinyint - Status
- ✅ `usuario_criacao` → int - ID do usuário
- ✅ `data_criacao` → timestamp - Automático

**Linha no código:** `daos/salvar_servico.php` linha 31-32

---

### **2. Editar Serviço**

**SQL executado:**
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

**Linha no código:** `daos/salvar_servico.php` linha 53-60

---

### **3. Configurar Preços por Empresa**

**Arquivo:** `views/recepcao/daos/salvar_tabela_precos.php`

**SQL executado:**
```sql
-- 1. Remove preços antigos
DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = 1;

-- 2. Insere novos preços
INSERT INTO tabela_precos_servicos 
    (tabela_precos_id, servico_id, preco, desconto_percentual) 
VALUES 
    (1, 1, 1000.00, 0),
    (1, 2, 1800.00, 0);
```

**Campos salvos:**
- ✅ `tabela_precos_id` - ID da tabela de preços
- ✅ `servico_id` - ID do serviço
- ✅ `preco` - Preço específico da empresa
- ✅ `desconto_percentual` - Desconto adicional

---

## 📋 TABELAS UTILIZADAS

### ✅ Tabela: `servicos_clinica`
**Armazena todos os serviços da clínica**

**Estrutura:**
```sql
CREATE TABLE servicos_clinica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(100),
    ativo TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_criacao INT(11)
)
```

**Operações CRUD:**
- ✅ **CREATE** - `daos/salvar_servico.php` (INSERT)
- ✅ **READ** - `servicos_clinica.php` (SELECT)
- ✅ **UPDATE** - `daos/salvar_servico.php` (UPDATE)
- ✅ **DELETE** - `daos/excluir_servico.php` (soft delete: ativo=0)

---

### ✅ Tabela: `tabela_precos_servicos`
**Armazena preços específicos por empresa**

**Estrutura:**
```sql
CREATE TABLE tabela_precos_servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabela_precos_id INT NOT NULL,
    servico_id INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    desconto_percentual DECIMAL(5,2) DEFAULT 0.00,
    ativo TINYINT(1) DEFAULT 1
)
```

**Operações CRUD:**
- ✅ **CREATE** - `daos/salvar_tabela_precos.php` (INSERT)
- ✅ **READ** - `tabela_precos.php` (SELECT)
- ✅ **UPDATE** - `daos/salvar_tabela_precos.php` (DELETE + INSERT)
- ✅ **DELETE** - `daos/salvar_tabela_precos.php` (DELETE antes de inserir novos)

---

## 🎯 RESUMO EXECUTIVO

### ✅ O QUE ESTÁ SENDO SALVO NA BD:

1. **Serviços/Procedimentos**
   - Local: `servicos_clinica`
   - Arquivo: `daos/salvar_servico.php`
   - Campos: código, nome, descrição, preço, categoria, status

2. **Preços por Empresa**
   - Local: `tabela_precos_servicos`
   - Arquivo: `daos/salvar_tabela_precos.php`
   - Campos: preço específico, desconto

3. **Empresas/Seguradoras**
   - Local: `empresas_seguros`
   - Arquivo: `daos/registar_empresa.php`
   - Campos: todos os dados da empresa

4. **Tabelas de Preços**
   - Local: `tabelas_precos`
   - Criada automaticamente quando necessário

---

## ✅ CONFIRMAÇÃO FINAL

**TODOS os dados importantes estão sendo salvos na base de dados através de CRUDs!**

- ✅ Serviços → INSERT/UPDATE na `servicos_clinica`
- ✅ Preços → INSERT/DELETE na `tabela_precos_servicos`
- ✅ Empresas → INSERT/UPDATE na `empresas_seguros`
- ✅ Nada está hardcoded no código
- ✅ Tudo pode ser editado pelo usuário
- ✅ Tudo está na base de dados

**Arquivos de salvamento:**
- `views/recepcao/daos/salvar_servico.php` - Linha 31 (INSERT) e 53 (UPDATE)
- `views/recepcao/daos/salvar_tabela_precos.php` - Linha 22 (INSERT)
- `views/recepcao/daos/registar_empresa.php` - Salva empresas

---

## 📝 QUER VERIFICAR?

Abra o arquivo `views/recepcao/daos/salvar_servico.php` e veja:
- **Linha 31:** INSERT INTO servicos_clinica (...)
- **Linha 53:** UPDATE servicos_clinica SET (...)

**Tudo está sendo salvo na base de dados!** ✅


