/*
 * Sistema de Wizard Multi-Etapas
 * Reutilizável para todos os formulários
 */

function initWizard(wizardId, steps) {
    const wizard = document.getElementById(wizardId);
    if (!wizard) return;
    
    let currentStep = 0;
    const totalSteps = steps.length;
    
    // Criar estrutura do wizard
    const wizardHTML = `
        <div class="wizard-container">
            <div class="wizard-steps" id="${wizardId}-steps"></div>
            <div class="wizard-progress">
                <div class="wizard-progress-bar" id="${wizardId}-progress" style="width: ${(1/totalSteps)*100}%"></div>
            </div>
            <div class="wizard-content" id="${wizardId}-content"></div>
            <div class="wizard-actions" id="${wizardId}-actions"></div>
        </div>
    `;
    
    wizard.innerHTML = wizardHTML;
    
    const stepsContainer = document.getElementById(`${wizardId}-steps`);
    const contentContainer = document.getElementById(`${wizardId}-content`);
    const actionsContainer = document.getElementById(`${wizardId}-actions`);
    const progressBar = document.getElementById(`${wizardId}-progress`);
    
    // Criar indicadores de etapas
    steps.forEach((step, index) => {
        const stepHTML = `
            <div class="wizard-step ${index === 0 ? 'active' : ''}" data-step="${index}">
                <div class="wizard-step-number">${index + 1}</div>
                <div class="wizard-step-title">${step.title}</div>
            </div>
        `;
        stepsContainer.innerHTML += stepHTML;
    });
    
    // Criar painéis de conteúdo
    steps.forEach((step, index) => {
        const paneHTML = `
            <div class="wizard-pane ${index === 0 ? 'active' : ''}" data-pane="${index}">
                ${step.content}
            </div>
        `;
        contentContainer.innerHTML += paneHTML;
    });
    
    // Criar botões de ação
    updateActions();
    
    function updateActions() {
        let actionsHTML = '';
        
        if (currentStep > 0) {
            actionsHTML += `<button type="button" class="btn wizard-btn wizard-btn-prev" onclick="wizardPrev('${wizardId}')">
                <i class="fa fa-arrow-left"></i> Voltar
            </button>`;
        } else {
            actionsHTML += `<div></div>`;
        }
        
        if (currentStep < totalSteps - 1) {
            actionsHTML += `<button type="button" class="btn wizard-btn wizard-btn-next" onclick="wizardNext('${wizardId}')">
                Prosseguir <i class="fa fa-arrow-right"></i>
            </button>`;
        } else {
            actionsHTML += `<button type="submit" class="btn wizard-btn wizard-btn-submit" form="${wizardId}-form">
                <i class="fa fa-check"></i> Finalizar
            </button>`;
        }
        
        actionsContainer.innerHTML = actionsHTML;
    }
    
    function updateSteps() {
        const stepElements = stepsContainer.querySelectorAll('.wizard-step');
        stepElements.forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if (index < currentStep) {
                stepEl.classList.add('completed');
            } else if (index === currentStep) {
                stepEl.classList.add('active');
            }
        });
        
        // Atualizar progresso
        const progress = ((currentStep + 1) / totalSteps) * 100;
        progressBar.style.width = progress + '%';
    }
    
    function showPane(index) {
        const panes = contentContainer.querySelectorAll('.wizard-pane');
        panes.forEach((pane, i) => {
            pane.classList.remove('active');
            if (i === index) {
                pane.classList.add('active');
            }
        });
    }
    
    // Funções globais para navegação
    window[`wizardNext_${wizardId}`] = function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                updateSteps();
                showPane(currentStep);
                updateActions();
                
                // Scroll para o topo
                wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    };
    
    window[`wizardPrev_${wizardId}`] = function() {
        if (currentStep > 0) {
            currentStep--;
            updateSteps();
            showPane(currentStep);
            updateActions();
            
            // Scroll para o topo
            wizard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };
    
    // Função de validação (pode ser sobrescrita)
    function validateStep(stepIndex) {
        const pane = contentContainer.querySelector(`.wizard-pane[data-pane="${stepIndex}"]`);
        const requiredFields = pane.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        });
        
        if (!isValid) {
            alert('Por favor, preencha todos os campos obrigatórios antes de prosseguir.');
            return false;
        }
        
        // Validação customizada se existir
        if (steps[stepIndex].validate && typeof steps[stepIndex].validate === 'function') {
            return steps[stepIndex].validate();
        }
        
        return true;
    }
    
    // Armazenar referências globais
    window[`wizard_${wizardId}`] = {
        currentStep: () => currentStep,
        goToStep: (step) => {
            if (step >= 0 && step < totalSteps) {
                currentStep = step;
                updateSteps();
                showPane(currentStep);
                updateActions();
            }
        },
        validate: validateStep
    };
}

// Funções globais simplificadas
function wizardNext(wizardId) {
    if (window[`wizardNext_${wizardId}`]) {
        window[`wizardNext_${wizardId}`]();
    }
}

function wizardPrev(wizardId) {
    if (window[`wizardPrev_${wizardId}`]) {
        window[`wizardPrev_${wizardId}`]();
    }
}

