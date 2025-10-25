document.addEventListener('DOMContentLoaded', () => {
    initOrderStepper();
});

function initOrderStepper() {
    const stepper = document.querySelector('[data-stepper]');
    if (!stepper) return;

    const steps = Array.from(stepper.querySelectorAll('[data-step]'));
    const panels = Array.from(document.querySelectorAll('[data-step-panel]'));
    let activeIndex = 0;

    const update = () => {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index <= activeIndex);
        });
        panels.forEach((panel, index) => {
            panel.classList.toggle('d-none', index !== activeIndex);
        });
    };

    stepper.addEventListener('click', (event) => {
        const target = event.target.closest('[data-step-index]');
        if (!target) return;
        activeIndex = Number(target.dataset.stepIndex);
        update();
    });

    document.querySelectorAll('[data-step-next]').forEach((button) => {
        button.addEventListener('click', () => {
            if (activeIndex < steps.length - 1) {
                activeIndex += 1;
                update();
            }
        });
    });

    document.querySelectorAll('[data-step-prev]').forEach((button) => {
        button.addEventListener('click', () => {
            if (activeIndex > 0) {
                activeIndex -= 1;
                update();
            }
        });
    });

    update();
}
