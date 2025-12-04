# Como os Serviços são Salvos na Base de Dados

## 📋 Estrutura de Dados

O sistema usa **3 tabelas principais** para gerenciar serviços e preços:

### 1. Tabela: `servicos_clinica`
**Armazena os serviços/procedimentos da clínica (preço padrão)**

```sql
CREATE TABLE `servicos_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL UNIQUE,        -- Ex: 'CONS-GERAL'
  `nome` varchar(255) NOT NULL,                -- Ex: 'Consulta Geral'
  `descricao` text DEFAULT NULL,               -- Descrição do serviço
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00, -- PREÇO PADRÃO BASE
  `categoria` varchar(100) DEFAULT NULL,       -- Ex: 'Consulta', 'Exame'
  `ativo` tinyint(1) NOT NULL DEFAULT 1,       -- 1=Ativo, 0=Inativo
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

**Campos salvos:**
- `codigo` - Código único do serviço (obrigatório, único)
- `nome` - Nome do serviço (obrigatório)
- `descricao` - Descrição detalhada (opcional)
- `preco` - **Preço padrão/base** (obrigatório)
- `categoria` - Categoria do serviço (obrigatório)
- `ativo` - Status ativo/inativo (1 ou 0)
- `usuario_criacao` - ID do usuário que criou
- `data_criacao` - Data/hora de criação (automático)

---

### 2. Tabela: `tabelas_precos`
**Armazena tabelas de preços vinculadas a empresas**

```sql
CREATE TABLE `tabelas_precos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL,              -- Referência à empresa
  `nome` varchar(255) NOT NULL,                -- Ex: 'Tabela Padrão'
  `descricao` text DEFAULT NULL,
  `validade_inicio` date DEFAULT NULL,
  `validade_fim` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

---

### 3. Tabela: `tabela_precos_servicos`
**Armazena os preços específicos de cada serviço por empresa**

```sql
CREATE TABLE `tabela_precos_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabela_precos_id` int(11) NOT NULL,        -- Referência à tabela de preços
  `servico_id` int(11) NOT NULL,              -- Referência ao serviço
  `preco` decimal(10,2) NOT NULL,             -- PREÇO ESPECÍFICO DA EMPRESA
  `desconto_percentual` decimal(5,2) DEFAULT 0.00,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`tabela_precos_id`, `servico_id`)
);
```

**Campos salvos:**
- `tabela_precos_id` - ID da tabela de preços da empresa
- `servico_id` - ID do serviço da tabela `servicos_clinica`
- `preco` - **Preço específico para aquela empresa**
- `desconto_percentual` - Desconto adicional (opcional)

---

## 🔄 Fluxo de Salvamento

### **Cenário 1: Criar/Editar Serviço (Preço Padrão)**

**Arquivo:** `views/recepcao/daos/salvar_servico.php`

**Para CRIAR novo serviço:**
```php
INSERT INTO servicos_clinica 
    (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
VALUES 
    ('CONS-GERAL', 'Consulta Geral', 'Descrição...', 1200.00, 'Consulta', 1, 1)
```

**Para EDITAR serviço existente:**
```php
UPDATE servicos_clinica SET
    codigo = 'CONS-GERAL',
    nome = 'Consulta Geral',
    descricao = 'Descrição...',
    preco = 1200.00,
    categoria = 'Consulta',
    ativo = 1
WHERE id = 1
```

**Validações:**
- ✅ Verifica se o código já existe (não pode duplicar)
- ✅ Todos os campos obrigatórios devem estar preenchidos
- ✅ Preço deve ser numérico e positivo

---

### **Cenário 2: Salvar Preços por Empresa**

**Arquivo:** `views/recepcao/daos/salvar_tabela_precos.php`

**Processo:**
1. **Cria ou busca a tabela de preços da empresa:**
   ```php
   SELECT * FROM tabelas_precos WHERE empresa_id = X AND ativo = 1
   ```
   Se não existir, cria uma nova.

2. **Remove todos os preços antigos:**
   ```php
   DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = X
   ```

3. **Insere os novos preços:**
   ```php
   INSERT INTO tabela_precos_servicos 
       (tabela_precos_id, servico_id, preco, desconto_percentual) 
   VALUES 
       (1, 1, 1000.00, 0),
       (1, 2, 1800.00, 0),
       ...
   ```

4. **Vincula a tabela à empresa:**
   ```php
   UPDATE empresas_seguros 
   SET tabela_precos_id = X 
   WHERE id = Y
   ```

---

## 📝 Exemplo Prático

### Passo 1: Criar Serviço
```sql
-- Serviço salvo na tabela servicos_clinica
INSERT INTO servicos_clinica 
    (codigo, nome, preco, categoria, ativo, usuario_criacao)
VALUES 
    ('CONS-GERAL', 'Consulta Geral', 1200.00, 'Consulta', 1, 1);
-- Resultado: id = 1, preco = 1200.00 (preço padrão)
```

### Passo 2: Configurar Preço para Empresa
```sql
-- 1. Criar tabela de preços para empresa
INSERT INTO tabelas_precos (empresa_id, nome, ativo, usuario_criacao)
VALUES (1, 'Tabela Padrão', 1, 1);
-- Resultado: id = 1 (tabela_precos_id)

-- 2. Salvar preço específico para essa empresa
INSERT INTO tabela_precos_servicos 
    (tabela_precos_id, servico_id, preco, desconto_percentual)
VALUES 
    (1, 1, 1000.00, 0);
-- Resultado: preco = 1000.00 (preço específico da empresa)
```

### Passo 3: Consultar Preço
```sql
-- Buscar preço da empresa (se existir) ou usar preço padrão
SELECT 
    sc.id,
    sc.nome,
    sc.preco as preco_padrao,
    COALESCE(tps.preco, sc.preco) as preco_final
FROM servicos_clinica sc
LEFT JOIN tabela_precos_servicos tps ON sc.id = tps.servico_id
LEFT JOIN tabelas_precos tp ON tps.tabela_precos_id = tp.id
WHERE sc.id = 1 AND (tp.empresa_id = 1 OR tp.empresa_id IS NULL)
ORDER BY tp.empresa_id DESC
LIMIT 1;
```

---

## 🎯 Resumo

| Ação | Tabela | Campo de Preço |
|------|--------|----------------|
| **Criar Serviço** | `servicos_clinica` | `preco` (padrão) |
| **Editar Serviço** | `servicos_clinica` | `preco` (padrão) |
| **Configurar Preço por Empresa** | `tabela_precos_servicos` | `preco` (específico) |
| **Consultar Preço** | Ambas | Usa preço da empresa ou padrão |

---

## ⚠️ Pontos Importantes

1. **Preço Padrão** é sempre salvo em `servicos_clinica.preco`
2. **Preço por Empresa** é salvo em `tabela_precos_servicos.preco`
3. Se uma empresa não tiver preço específico, usa o preço padrão
4. O código do serviço deve ser **único** (não pode repetir)
5. Ao salvar preços por empresa, **todos os preços antigos são removidos** e substituídos pelos novos

---

## 📂 Arquivos Relacionados

- **Salvar Serviço:** `views/recepcao/daos/salvar_servico.php`
- **Salvar Preços por Empresa:** `views/recepcao/daos/salvar_tabela_precos.php`
- **Tela de Serviços:** `views/recepcao/servicos_clinica.php`
- **Tela de Preços:** `views/recepcao/tabela_precos.php`


