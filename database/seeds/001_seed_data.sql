-- Venda Hoje CRM - Seed Inicial
USE venda_hoje;

-- Planos
INSERT INTO planos (id, nome, descricao, preco_mensal, max_usuarios, max_leads) VALUES
(1, 'Starter', 'Ideal para equipes pequenas', 97.00, 3, 200),
(2, 'Pro', 'Para times comerciais em crescimento', 197.00, 10, 1000),
(3, 'Enterprise', 'Recursos avançados e suporte prioritário', 497.00, 50, 10000);

-- Empresa demo
INSERT INTO empresas (id, nome, cnpj, email, telefone, plano_id, ativo) VALUES
(1, 'Empresa Demo Venda Hoje', '00.000.000/0001-00', 'contato@vendahoje.com', '(11) 99999-0000', 2, 1),
(2, 'Tech Solutions Ltda', '11.111.111/0001-11', 'tech@example.com', '(21) 98888-0000', 1, 1);

-- Assinaturas
INSERT INTO assinaturas (empresa_id, plano_id, status, data_inicio, data_fim) VALUES
(1, 2, 'ativa', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR)),
(2, 1, 'trial', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY));

-- Usuários (senha: password123)
INSERT INTO usuarios (empresa_id, nome, email, senha, perfil, ativo) VALUES
(1, 'Admin SaaS', 'admin@vendahoje.com', '$2y$10$wu/ExP9GgQ0k0AW7x/OjHOvesav7PHX/oxaFKPYbtoeRWgOj62T2m', 'admin_saas', 1),
(1, 'Carlos Gestor', 'gestor@vendahoje.com', '$2y$10$wu/ExP9GgQ0k0AW7x/OjHOvesav7PHX/oxaFKPYbtoeRWgOj62T2m', 'gestor', 1),
(1, 'Ana Closer', 'closer@vendahoje.com', '$2y$10$wu/ExP9GgQ0k0AW7x/OjHOvesav7PHX/oxaFKPYbtoeRWgOj62T2m', 'closer', 1),
(1, 'Pedro SDR', 'sdr@vendahoje.com', '$2y$10$wu/ExP9GgQ0k0AW7x/OjHOvesav7PHX/oxaFKPYbtoeRWgOj62T2m', 'sdr', 1);

-- Etapas do caminho (globais - empresa_id NULL)
INSERT INTO etapas_caminho (empresa_id, etapa_funil, ordem, titulo, descricao) VALUES
(NULL, 'novo_lead', 1, 'Apresentação inicial', 'Apresentar a empresa e entender contexto básico'),
(NULL, 'novo_lead', 2, 'Validar contato', 'Confirmar dados de contato e disponibilidade'),
(NULL, 'em_contato', 1, 'Descoberta de dor', 'Identificar problemas e necessidades'),
(NULL, 'em_contato', 2, 'Qualificar fit', 'Verificar se o lead se encaixa no perfil ideal'),
(NULL, 'qualificado', 1, 'Agendar videochamada', 'Marcar reunião de diagnóstico'),
(NULL, 'videochamada_agendada', 1, 'Confirmar presença', 'Enviar lembrete e confirmar participação'),
(NULL, 'videochamada_realizada', 1, 'Aplicar diagnóstico', 'Preencher score de qualificação'),
(NULL, 'videochamada_realizada', 2, 'Apresentar solução', 'Demonstrar proposta de valor'),
(NULL, 'proposta_enviada', 1, 'Follow-up proposta', 'Acompanhar recebimento e dúvidas'),
(NULL, 'negociacao', 1, 'Tratar objeções', 'Responder objeções e ajustar proposta'),
(NULL, 'negociacao', 2, 'Fechar contrato', 'Formalizar acordo comercial');

-- 13 Critérios de Diagnóstico (globais)
INSERT INTO criterios_diagnostico (empresa_id, bloco, nome, descricao, peso, ordem) VALUES
(NULL, 'Dor/Necessidade', 'Problema claro', 'O lead consegue articular claramente o problema?', 10, 1),
(NULL, 'Dor/Necessidade', 'Dor central', 'A dor é central para o negócio do lead?', 8, 2),
(NULL, 'Dor/Necessidade', 'Tentou outra ferramenta', 'Já tentou resolver com outra solução?', 6, 3),
(NULL, 'Autoridade/Decisão', 'Decisor final', 'Estamos falando com o decisor final?', 9, 4),
(NULL, 'Autoridade/Decisão', 'Envolvidos', 'Quantos stakeholders estão envolvidos na decisão?', 5, 5),
(NULL, 'Orçamento/Fit', 'Orçamento disponível', 'Existe orçamento definido para a solução?', 9, 6),
(NULL, 'Orçamento/Fit', 'Porte compatível', 'O porte da empresa é compatível com nossa solução?', 7, 7),
(NULL, 'Orçamento/Fit', 'Volume justifica', 'O volume/necessidade justifica o investimento?', 6, 8),
(NULL, 'Urgência/Momento', 'Urgência', 'Qual o nível de urgência para resolver?', 8, 9),
(NULL, 'Urgência/Momento', 'Concorrentes', 'Está avaliando concorrentes ativamente?', 5, 10),
(NULL, 'Engajamento', 'Engajamento na call', 'Nível de engajamento durante a videochamada', 7, 11),
(NULL, 'Engajamento', 'Reagiu a gatilho', 'Reagiu positivamente a algum gatilho usado?', 6, 12),
(NULL, 'Engajamento', 'Topou avançar', 'Concordou em avançar para próxima etapa?', 9, 13);

-- Opções padrão para cada critério (valor 1-5)
INSERT INTO criterio_opcoes (criterio_id, valor, rotulo, pergunta_sugerida)
SELECT c.id, v.valor, v.rotulo, v.pergunta
FROM criterios_diagnostico c
CROSS JOIN (
    SELECT 1 AS valor, 'Muito baixo' AS rotulo, 'Pode descrever melhor essa situação?' AS pergunta UNION ALL
    SELECT 2, 'Baixo', 'O que falta para isso ser mais claro?' UNION ALL
    SELECT 3, 'Médio', 'Como você avalia isso hoje?' UNION ALL
    SELECT 4, 'Alto', 'O que reforça essa percepção?' UNION ALL
    SELECT 5, 'Muito alto', 'Excelente! Pode detalhar mais?'
) v
WHERE c.empresa_id IS NULL;

-- Gatilhos de exemplo (25 gatilhos)
INSERT INTO gatilhos (empresa_id, categoria, etapa_funil, titulo, texto) VALUES
(NULL, 'Curiosidade', 'novo_lead', 'Pergunta provocativa', 'Você sabia que 70% das empresas do seu segmento perdem vendas por falta de follow-up estruturado?'),
(NULL, 'Prova Social', 'novo_lead', 'Case de sucesso', 'A empresa X, do mesmo porte que a sua, aumentou conversão em 40% nos primeiros 60 dias.'),
(NULL, 'Urgência', 'em_contato', 'Custo da inação', 'Cada dia sem resolver isso representa aproximadamente R$ X em oportunidades perdidas.'),
(NULL, 'Autoridade', 'em_contato', 'Expertise', 'Trabalhamos com mais de 200 empresas B2B e mapeamos os 5 erros mais comuns nessa fase.'),
(NULL, 'Reciprocidade', 'em_contato', 'Material gratuito', 'Preparei um diagnóstico gratuito do funil comercial da sua empresa. Posso enviar?'),
(NULL, 'Escassez', 'qualificado', 'Vagas limitadas', 'Temos apenas 3 vagas de onboarding este mês com consultoria inclusa.'),
(NULL, 'Compromisso', 'qualificado', 'Micro-compromisso', 'Se eu mostrar como resolver isso em 30 minutos, você consegue participar de uma call rápida?'),
(NULL, 'Afinidade', 'qualificado', 'Espelhamento', 'Entendo perfeitamente — passei pela mesma situação com outro cliente do seu setor.'),
(NULL, 'Contraste', 'videochamada_agendada', 'Antes e depois', 'Antes: leads esfriando no WhatsApp. Depois: pipeline organizado com alertas automáticos.'),
(NULL, 'Ancoragem', 'videochamada_realizada', 'Valor percebido', 'Empresas que investem R$ 5.000/mês em ads sem CRM perdem em média 35% do ROI.'),
(NULL, 'Objeção - Preço', 'negociacao', 'Custo vs Investimento', 'Não é gasto, é investimento com retorno mensurável em 90 dias.'),
(NULL, 'Objeção - Timing', 'negociacao', 'Momento ideal', 'O melhor momento para implementar é antes da alta temporada, não durante.'),
(NULL, 'Objeção - Concorrência', 'negociacao', 'Diferencial', 'Nossa vantagem é o funil com score de diagnóstico integrado — nenhum concorrente tem isso.'),
(NULL, 'Fechamento', 'negociacao', 'Assumir venda', 'Posso preparar o contrato com início na próxima segunda?'),
(NULL, 'Fechamento', 'negociacao', 'Alternativa', 'Prefere começar com o plano Pro ou Enterprise?'),
(NULL, 'Reengajamento', 'em_contato', 'Retomada', 'Vi que conversamos semana passada — conseguiu avaliar o material que enviei?'),
(NULL, 'Reengajamento', 'proposta_enviada', 'Check-in proposta', 'Teve chance de revisar a proposta? Ficou alguma dúvida que posso esclarecer?'),
(NULL, 'Storytelling', 'videochamada_realizada', 'História cliente', 'Um cliente similar estava perdendo 15 leads por mês. Em 45 dias, fechou 8 com follow-up automatizado.'),
(NULL, 'Dados', 'qualificado', 'Estatística', 'Leads respondidos em até 5 minutos têm 21x mais chance de conversão.'),
(NULL, 'Empatia', 'em_contato', 'Validação', 'Faz total sentido você estar cauteloso — é uma decisão importante para o time comercial.'),
(NULL, 'Exclusividade', 'negociacao', 'Condição especial', 'Consigo manter essa condição especial apenas até sexta-feira.'),
(NULL, 'Pergunta', 'novo_lead', 'Descoberta', 'Qual o maior desafio do seu time comercial hoje?'),
(NULL, 'Pergunta', 'qualificado', 'Budget', 'Vocês já reservaram budget para ferramentas comerciais este trimestre?'),
(NULL, 'Pergunta', 'videochamada_realizada', 'Decisor', 'Além de você, quem mais precisa aprovar essa decisão?'),
(NULL, 'Gatilho Final', 'negociacao', 'Última chance', 'Se fecharmos hoje, incluo 2 meses de treinamento para o time sem custo adicional.');

-- Leads de exemplo
INSERT INTO leads (empresa_id, nome, email, telefone, empresa_nome, origem, etapa_funil, sdr_id, closer_id, valor_estimado) VALUES
(1, 'Mariana Costa', 'mariana@startup.io', '(11) 98765-4321', 'Startup IO', 'LinkedIn', 'qualificado', 4, 3, 15000.00),
(1, 'Roberto Alves', 'roberto@techcorp.com', '(21) 97654-3210', 'TechCorp', 'Indicação', 'videochamada_agendada', 4, 3, 25000.00),
(1, 'Fernanda Lima', 'fernanda@agencia.com', '(31) 96543-2109', 'Agência Digital FL', 'Site', 'em_contato', 4, NULL, 8000.00),
(1, 'João Pereira', 'joao@industria.com', '(41) 95432-1098', 'Indústria JP', 'Cold Call', 'novo_lead', 4, NULL, 50000.00),
(1, 'Lucia Mendes', 'lucia@consultoria.com', '(51) 94321-0987', 'Consultoria LM', 'Evento', 'proposta_enviada', 4, 3, 12000.00);

-- Follow-up pendente de exemplo
INSERT INTO followups (lead_id, usuario_id, data_retorno, gatilho_sugerido_id, status, observacao) VALUES
(3, 4, DATE_ADD(NOW(), INTERVAL 1 DAY), 16, 'pendente', 'Retomar conversa sobre material enviado'),
(5, 3, DATE_ADD(NOW(), INTERVAL -1 DAY), 17, 'pendente', 'Verificar proposta enviada - URGENTE');
