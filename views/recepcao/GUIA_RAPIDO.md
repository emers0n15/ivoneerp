# Guia Rápido - Sistema de Preços por Empresa

## ✅ O que foi criado

O sistema já está **completamente funcional** e pronto para uso! Foi criado:

### 📋 Páginas Web
1. **servicos_clinica.php** - Gerir serviços e procedimentos
2. **empresas.php** - Listar empresas (já existente, melhorado)
3. **tabela_precos.php** - Configurar preços por empresa (já existente)

### 🔧 Scripts Backend
1. **salvar_servico.php** - Criar e editar serviços
2. **excluir_servico.php** - Excluir/inativar serviços
3. **salvar_tabela_precos.php** - Salvar preços por empresa (já existente)

### 📊 Base de Dados
Todas as tabelas já existem:
- `servicos_clinica` - Serviços/Procedimentos
- `empresas_seguros` - Empresas/Seguradoras
- `tabelas_precos` - Tabelas de preços
- `tabela_precos_servicos` - Preços específicos

## 🚀 Como Usar (Passo a Passo)

### 1️⃣ Aceder ao Sistema
1. Abra o navegador
2. Aceda: `http://localhost/ivoneerp`
3. Faça login com suas credenciais de recepção

### 2️⃣ Cadastrar Serviços/Procedimentos

**PRIMEIRA VEZ - Cadastrar os Serviços Base:**

1. No menu lateral, clique em **"Serviços/Procedimentos"**
2. Clique no botão **"+ Novo Serviço"**
3. Preencha os dados:
   - **Código**: Ex: `CONS-GERAL`
   - **Categoria**: Ex: `Consulta`
   - **Nome**: Ex: `Consulta Médica Geral`
   - **Descrição**: Ex: `Consulta médica de rotina`
   - **Preço Padrão**: Ex: `1200.00`
   - **Status**: `Ativo`
4. Clique **"Salvar Serviço"**

**Repita para todos os seus procedimentos!**

### 3️⃣ Cadastrar Empresas/Seguradoras

1. No menu lateral, clique em **"Empresas/Seguros"** → **"Nova Empresa"**
2. Preencha os dados:
   - **Nome**: Ex: `Vulcan Seguros`
   - **NUIT**: Número fiscal
   - **Contacto**: Telefone
   - **Email**: Email da empresa
   - **Contrato**: Número do contrato
   - **Data Início/Fim**: Datas de validade
   - **Desconto Geral**: Ex: `5` (para 5%)
3. Salve a empresa

**Repita para todas as empresas dos seus ficheiros Excel!**

### 4️⃣ Configurar Preços Específicos por Empresa

1. No menu **"Empresas/Seguros"** → **"Ver Empresas"**
2. Localize a empresa (ex: Vulcan)
3. Clique no botão **"📊 Preços"**
4. Verá uma tabela com TODOS os serviços
5. Para cada serviço, configure:
   - **Preço Contratado**: Preço específico desta empresa
   - **Desconto (%)**: Desconto adicional
6. Clique **"Salvar Tabela de Preços"**

## 📌 Exemplo Prático

### Cenário: Configurar preços para Vulcan e Monte Sinai

**Passo 1: Cadastrar o serviço base**
- Código: `CONS-CARD`
- Nome: `Consulta Cardiologia`
- Preço Padrão: `2.500,00 MT`

**Passo 2: Configurar preço para Vulcan**
1. Ir em Empresas → Ver Empresas
2. Clicar em "Preços" na linha da Vulcan
3. Localizar "Consulta Cardiologia"
4. Definir: Preço = `2.200,00 MT`, Desconto = `0%`
5. Salvar

**Passo 3: Configurar preço para Monte Sinai**
1. Ir em Empresas → Ver Empresas
2. Clicar em "Preços" na linha da Monte Sinai
3. Localizar "Consulta Cardiologia"
4. Definir: Preço = `2.000,00 MT`, Desconto = `5%`
5. Salvar

**Resultado:**
- **Vulcan paga**: 2.200,00 MT
- **Monte Sinai paga**: 2.000,00 - 5% = 1.900,00 MT
- **Particular (sem empresa)**: 2.500,00 MT (preço padrão)

## 💡 Dicas Importantes

### ✔️ Ordem de Prioridade de Preços
1. **Preço Específico** (configurado na tabela de preços)
2. **Desconto Geral** (da empresa, aplicado ao preço padrão)
3. **Preço Padrão** (do serviço)

### ✔️ Boas Práticas
- Use códigos claros e únicos para serviços
- Mantenha as categorias organizadas
- Revise os contratos antes do vencimento
- Não exclua serviços em uso (o sistema inativa automaticamente)
- Atualize preços quando houver renovação de contrato

### ✔️ Gestão de Alterações
- **Editar Serviço**: Clique no botão ✏️ na lista de serviços
- **Editar Empresa**: Clique no botão ✏️ na lista de empresas
- **Atualizar Preços**: Aceda à tabela de preços da empresa

## 🔍 Verificar se Está a Funcionar

### Teste Rápido
1. Cadastre 1 serviço de teste
2. Cadastre 1 empresa de teste
3. Configure o preço para esta empresa
4. Volte à lista de empresas
5. Deve aparecer status **"Ativa"** (com tabela de preços)

## 📁 Seus Ficheiros Excel

Você tem 2 ficheiros Excel com listas de preços:
1. `Cópia de Vulcan Prices - Final to providers.xlsx`
2. `Clínica Médica Monte Sinai fidelidade para arranjos.xlsx`

### Como Usar Esses Dados

**Manualmente (Recomendado para começar):**
1. Abra os ficheiros Excel
2. Para cada linha do Excel:
   - Se o procedimento não existe → Cadastre em "Serviços/Procedimentos"
   - Vá em "Empresas" → "Preços" da empresa correspondente
   - Configure o preço específico

**Automaticamente (Opcional - requer desenvolvimento):**
- Pode ser criado um script PHP para importar automaticamente
- Requer biblioteca para ler Excel (PHPSpreadsheet)
- Contacte o desenvolvedor se precisar dessa funcionalidade

## ❓ Resolução de Problemas

### Problema: Não aparece o menu "Serviços/Procedimentos"
**Solução:** Faça logout e login novamente no sistema

### Problema: Erro ao salvar serviço
**Solução:** Verifique se o código é único (não pode repetir)

### Problema: Empresa não aparece como "Ativa"
**Solução:** Configure pelo menos 1 preço na tabela de preços da empresa

### Problema: Preço não está correto na fatura
**Solução:** Verifique a configuração de preços da empresa específica

## 📞 Suporte

Se precisar de ajuda adicional:
1. Consulte o arquivo `SISTEMA_PRECOS_EMPRESAS.md` (documentação completa)
2. Verifique as mensagens de erro na tela
3. Contacte o administrador do sistema

---

**✅ Tudo Pronto!**  
O sistema está 100% funcional. Comece cadastrando seus serviços e empresas!
