# ⚠️ REGRAS IMPORTANTES DE DESENVOLVIMENTO

## 🎯 REGRA PRINCIPAL: TUDO NA BASE DE DADOS

**NENHUM dado importante da empresa deve ser hardcoded no código!**

### ✅ DEVE SER SALVO NA BD:
- Serviços/Procedimentos
- Empresas/Seguradoras
- Preços e tabelas de preços
- Categorias de serviços
- Configurações importantes
- Métodos de pagamento
- Status/Estados
- Qualquer dado que possa mudar ou ser configurável

### ❌ NÃO DEVE ESTAR NO CÓDIGO:
- Arrays de valores fixos
- Configurações hardcoded
- Listas de opções fixas
- Valores padrão importantes
- Dados que o usuário precisa alterar

---

## 📋 EXEMPLOS

### ❌ ERRADO:
```php
// NÃO FAZER ISSO!
$categorias = ['Consulta', 'Exame', 'Procedimento'];
$metodos_pagamento = ['Dinheiro', 'M-Pesa', 'Transferência'];
```

### ✅ CORRETO:
```php
// BUSCAR DA BASE DE DADOS
$sql = "SELECT * FROM categorias_servicos WHERE ativo = 1";
$rs = mysqli_query($db, $sql);
while($categoria = mysqli_fetch_array($rs)) {
    // usar categoria
}
```

---

## 🔧 CORREÇÕES NECESSÁRIAS

1. **Categorias de Serviços** - Criar tabela `categorias_servicos`
2. **Métodos de Pagamento** - Criar tabela `metodos_pagamento` (se aplicável)
3. **Status/Estados** - Criar tabelas quando necessário
4. **Configurações** - Criar tabela `configuracoes` para settings

---

## 📝 CHECKLIST ANTES DE COMMIT

- [ ] Não há arrays de valores fixos no código?
- [ ] Todos os dados importantes estão na BD?
- [ ] Existe CRUD para gerenciar esses dados?
- [ ] O usuário pode alterar via interface?
- [ ] Não há valores hardcoded que o cliente precisa mudar?


