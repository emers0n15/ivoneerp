# ✅ RESUMO: Como os Serviços são Salvos na Base de Dados

## 🎯 REGRA SEGUIDA: TUDO NA BASE DE DADOS

**Confirmado:** Todos os dados importantes da empresa estão sendo salvos na base de dados, **nada hardcoded no código!**

---

## 📊 ESTRUTURA DE SALVAMENTO

### 1. **SERVIÇOS** → Tabela: `servicos_clinica`

**Arquivo PHP:** `views/recepcao/daos/salvar_servico.php`

**SQL INSERT (criar novo):**
```sql
INSERT INTO servicos_clinica 
    (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
VALUES 
    ('CONS-GERAL', 'Consulta Geral', 'Descrição...', 1200.00, 'Consulta', 1, 1)
```

**SQL UPDATE (editar existente):**
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

**Campos salvos:**
- ✅ Código (único)
- ✅ Nome
- ✅ Descrição
- ✅ Preço padrão
- ✅ Categoria (vem da BD)
- ✅ Status ativo/inativo
- ✅ Usuário que criou
- ✅ Data de criação (automático)

---

### 2. **PREÇOS POR EMPRESA** → Tabela: `tabela_precos_servicos`

**Arquivo PHP:** `views/recepcao/daos/salvar_tabela_precos.php`

**Processo:**
1. Remove preços antigos: `DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = X`
2. Insere novos preços: `INSERT INTO tabela_precos_servicos (...)`

**SQL completo:**
```sql
-- Limpar preços antigos
DELETE FROM tabela_precos_servicos WHERE tabela_precos_id = 1;

-- Inserir novos preços
INSERT INTO tabela_precos_servicos 
    (tabela_precos_id, servico_id, preco, desconto_percentual) 
VALUES 
    (1, 1, 1000.00, 0),
    (1, 2, 1800.00, 0),
    (1, 3, 1200.00, 0);
```

**Campos salvos:**
- ✅ ID da tabela de preços
- ✅ ID do serviço
- ✅ Preço específico da empresa
- ✅ Desconto percentual

---

### 3. **TABELAS DE PREÇOS** → Tabela: `tabelas_precos`

**Criada automaticamente quando:**
- Uma empresa é selecionada pela primeira vez
- Uma tabela de preços é configurada

**SQL de criação:**
```sql
INSERT INTO tabelas_precos 
    (empresa_id, nome, ativo, usuario_criacao) 
VALUES 
    (1, 'Tabela Padrão', 1, 1)
```

---

### 4. **EMPRESAS** → Tabela: `empresas_seguros`

**Arquivo PHP:** `views/recepcao/daos/registar_empresa.php`

**Todos os dados da empresa são salvos:**
- ✅ Nome, NUIT, Contacto, Email, Endereço
- ✅ Dados do contrato
- ✅ Desconto geral
- ✅ Tudo na BD

---

## 🔄 FLUXO COMPLETO

### Quando você cria um serviço:
```
1. Preenche formulário em servicos_clinica.php
2. Clica em "Salvar Serviço"
3. Dados enviados para daos/salvar_servico.php
4. Validação: código único? ✅
5. INSERT INTO servicos_clinica (...) ← SALVO NA BD
6. Redireciona com mensagem de sucesso
```

### Quando você configura preços por empresa:
```
1. Seleciona empresa em servicos_clinica.php
2. Edita preços na tabela
3. Clica em "Salvar Preços da Empresa"
4. AJAX chama daos/salvar_tabela_precos.php
5. DELETE preços antigos
6. INSERT novos preços ← SALVO NA BD
7. Mensagem de sucesso
```

---

## ✅ CONFIRMAÇÃO FINAL

| Item | Está na BD? | Pode ser editado? | CRUD disponível? |
|------|-------------|-------------------|------------------|
| **Serviços** | ✅ SIM | ✅ SIM | ✅ SIM |
| **Preços por Empresa** | ✅ SIM | ✅ SIM | ✅ SIM |
| **Empresas** | ✅ SIM | ✅ SIM | ✅ SIM |
| **Categorias** | ✅ SIM* | ✅ SIM* | ⚠️ Pendente |

*Tabela criada, código atualizado para buscar da BD

---

## 📝 QUER VER O SQL EXATO?

Abra o arquivo: `views/recepcao/daos/salvar_servico.php`

Linhas 31-32 (criar):
```php
$sql = "INSERT INTO servicos_clinica (codigo, nome, descricao, preco, categoria, ativo, usuario_criacao) 
        VALUES ('$codigo', '$nome', '$descricao', $preco, '$categoria', $ativo, $usuario_id)";
```

Linhas 53-60 (editar):
```php
$sql = "UPDATE servicos_clinica SET 
        codigo = '$codigo',
        nome = '$nome',
        descricao = '$descricao',
        preco = $preco,
        categoria = '$categoria',
        ativo = $ativo
        WHERE id = $id";
```

---

## 🎯 CONCLUSÃO

✅ **TODOS os serviços são salvos na base de dados!**
✅ **TODOS os preços são salvos na base de dados!**
✅ **TODA informação importante está na BD!**
✅ **Nada está hardcoded no código!**


