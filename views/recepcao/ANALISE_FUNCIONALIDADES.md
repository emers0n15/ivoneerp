# Análise de Funcionalidades - Módulo de Recepção
## Baseado no Documento de Requisitos Oficial

---

## ✅ REQUISITOS FUNCIONAIS IMPLEMENTADOS (REC-RF)

### Implementados Completamente
- **REC-RF-006** ✅ - Pesquisa rápida de paciente (nome, telefone, NID, código)
- **REC-RF-009** ✅ - Emissão de fatura/recibo em PDF

### Implementados Parcialmente
- **REC-RF-004** ⚠️ - Faturar procedimentos (SIM) mas sem VDS/cotação integrados
- **REC-RF-008** ⚠️ - Pagamentos: Dinheiro, POS, M-Pesa, Emola (SIM) | Falta: Transferência, Fatura para empresa
- **REC-RF-010** ⚠️ - Estados: Pago, Pendente (SIM) | Falta: Parcial, Vencido, pagamentos parciais
- **REC-RF-011** ⚠️ - Histórico por paciente (SIM) | Falta: Histórico por empresa
- **REC-RF-012** ⚠️ - Edição de dados (SIM) | Falta: Histórico de alterações

---

## ❌ REQUISITOS FUNCIONAIS NÃO IMPLEMENTADOS (REC-RF)

### Alta Prioridade (Críticos)
- **REC-RF-001** ❌ - Cadastrar paciente com empresa (caixa de seleção)
- **REC-RF-002** ❌ - Combobox de empresas ao cadastrar paciente
- **REC-RF-003** ❌ - Aplicar preços contratados pela empresa automaticamente
- **REC-RF-005** ❌ - Gerar VDS e cotações a partir do painel de faturação
- **REC-RF-007** ❌ - Cadastrar empresas (tabelas de preços, contratos, validade, descontos)
- **REC-RF-013** ❌ - Interface rápida one-click load para faturar sem nova procura
- **REC-RF-014** ❌ - Integração com Farmácia, Laboratório, Contabilidade

### Média Prioridade
- **REC-RF-015** ❌ - Histórico de empresas associadas ao paciente (log temporal)

---

## ❌ REQUISITOS NÃO FUNCIONAIS (REC-RNF)

### Implementados
- **REC-RNF-003** ✅ - Geração de PDF compatível

### Não Implementados
- **REC-RNF-001** ❌ - Interface ≤ 1 minuto para faturamento (não medido/otimizado)
- **REC-RNF-002** ❌ - Pesquisa ≤ 2 segundos (não medido/otimizado)
- **REC-RNF-004** ❌ - Logs de auditoria de ações críticas
- **REC-RNF-005** ❌ - Backup diário e exportação de dados
- **REC-RNF-006** ❌ - Controle de acesso por perfis (só verifica categoria básica)

---

## ❌ REGRAS DE NEGÓCIO (REC-RN)

### Implementadas
- **REC-RN-001** ✅ - Código único do paciente (numero_processo UNIQUE)
- **REC-RN-006** ✅ - Faturas pagas não podem ser editadas

### Não Implementadas
- **REC-RN-002** ❌ - Aplicar tabela da empresa automaticamente se paciente pertence a empresa
- **REC-RN-003** ❌ - Recepção não altera preços contratuais (controle de permissão)
- **REC-RN-004** ❌ - Faturas corporativas identificadas para cobrança posterior
- **REC-RN-005** ❌ - Histórico de alterações de associação empresa↔paciente

---

## 📊 RESUMO ESTATÍSTICO

### Requisitos Funcionais (REC-RF)
- **Total**: 15 requisitos
- **Implementados Completamente**: 2 (13%)
- **Implementados Parcialmente**: 5 (33%)
- **Não Implementados**: 8 (54%)

### Requisitos Não Funcionais (REC-RNF)
- **Total**: 6 requisitos
- **Implementados**: 1 (17%)
- **Não Implementados**: 5 (83%)

### Regras de Negócio (REC-RN)
- **Total**: 6 regras
- **Implementadas**: 2 (33%)
- **Não Implementadas**: 4 (67%)

### Taxa Geral de Implementação
- **Total de Requisitos**: 27
- **Implementados**: 5 (19%)
- **Parciais**: 5 (19%)
- **Não Implementados**: 17 (62%)

---

## 🎯 PRIORIDADES PARA IMPLEMENTAÇÃO

### CRÍTICO (Alta Prioridade)
1. **REC-RF-001, REC-RF-002** - Sistema de empresas e associação com pacientes
2. **REC-RF-007** - Cadastro de empresas com tabelas de preços
3. **REC-RF-003** - Aplicação automática de preços contratados
4. **REC-RF-010** - Estados Parcial/Vencido e pagamentos parciais
5. **REC-RF-005** - Geração de VDS e cotações
6. **REC-RF-008** - Adicionar Transferência e Fatura para empresa

### IMPORTANTE (Média Prioridade)
7. **REC-RF-013** - Interface one-click load
8. **REC-RF-012** - Histórico de alterações de dados
9. **REC-RF-015** - Histórico de associações empresa↔paciente
10. **REC-RNF-004** - Logs de auditoria
11. **REC-RNF-006** - Controle de acesso por perfis

### DESEJÁVEL (Baixa Prioridade)
12. **REC-RF-014** - Integração com outros módulos
13. **REC-RNF-001, REC-RNF-002** - Otimização de performance
14. **REC-RNF-005** - Backup e exportação

---

## 💡 SUGESTÕES DE MELHORIAS

### Funcionalidades Essenciais Faltantes
1. **Módulo de Empresas**: Tabela `empresas` com campos: nome, NUIT, contacto, tabela_precos_id, contrato, validade, desconto
2. **Tabelas de Preços**: Tabela `tabelas_precos` e `tabela_precos_servicos` para preços por empresa
3. **Associação Paciente-Empresa**: Tabela `paciente_empresa` com histórico temporal
4. **Estados de Fatura**: Adicionar 'parcial' e 'vencido' ao enum, campo `valor_pago` para rastrear pagamentos parciais
5. **VDS e Cotações**: Integrar geração desses documentos no fluxo de faturação
6. **Pagamentos Parciais**: Permitir múltiplos pagamentos na mesma fatura
7. **Interface One-Click**: Cache de última empresa do paciente para faturação rápida

### Melhorias Técnicas
8. **Logs de Auditoria**: Tabela `auditoria` para registrar todas ações críticas
9. **Controle de Acesso**: Sistema de permissões granular (não só categoria)
10. **Performance**: Índices otimizados, cache de pesquisas frequentes
11. **Backup Automático**: Script de backup diário
12. **Exportação**: Função para exportar dados em Excel/CSV

### Melhorias de UX
13. **Validação de Duplicatas**: Alertar pacientes com mesmo NID/contacto
14. **Dashboard Empresas**: Estatísticas por empresa
15. **Notificações**: Alertas de faturas vencidas, contratos próximos do vencimento
16. **Filtros Avançados**: Por empresa, período, status, método de pagamento

---

## ⚠️ OBSERVAÇÕES IMPORTANTES

1. **Falta Estrutura de Empresas**: O sistema atual não suporta empresas/planos corporativos, que é requisito crítico
2. **Falta Sistema de Preços Contratados**: Não há como aplicar preços diferentes por empresa
3. **Faturação Limitada**: Não gera VDS/cotações, apenas faturas simples
4. **Estados Incompletos**: Falta suporte a pagamentos parciais e faturas vencidas
5. **Sem Integração**: Não há comunicação com outros módulos (Farmácia, Laboratório, etc.)
6. **Auditoria Inexistente**: Não há logs de ações críticas
7. **Controle de Acesso Básico**: Apenas verifica categoria, sem permissões granulares

---

**Conclusão**: O sistema atual cobre funcionalidades básicas de recepção, mas **falta a estrutura completa de empresas e planos corporativos**, que é o diferencial crítico do sistema conforme os requisitos.
