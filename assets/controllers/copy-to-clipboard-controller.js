import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'copyIcon', 'successIcon', 'errorIcon', 'status'];

    static values = {
        value: String,
        copiedLabel: String,
        errorLabel: String,
    };

    connect() {
        this.pendingCopy = null;
        this.reset();
    }

    async copy() {
        if (this.pendingCopy || this.buttonTarget.disabled || !this.hasValueValue || this.valueValue === '') {
            return;
        }

        this.reset();
        const operation = {};
        this.pendingCopy = operation;
        this.buttonTarget.setAttribute('aria-busy', 'true');

        try {
            await navigator.clipboard.writeText(this.valueValue);

            if (this.pendingCopy === operation) {
                this.showFeedback(this.successIconTarget, this.copiedLabelValue);
            }
        } catch (error) {
            if (this.pendingCopy === operation) {
                this.showFeedback(this.errorIconTarget, this.errorLabelValue, true);
            }
        } finally {
            if (this.pendingCopy === operation) {
                this.pendingCopy = null;
                this.buttonTarget.removeAttribute('aria-busy');
            }
        }
    }

    disconnect() {
        this.pendingCopy = null;
        clearTimeout(this.resetTimer);

        if (this.hasButtonTarget) {
            this.buttonTarget.removeAttribute('aria-busy');
        }
    }

    showFeedback(icon, message, isError = false) {
        this.reset();
        this.copyIconTarget.classList.add('d-none');
        icon.classList.remove('d-none');
        this.statusTarget.classList.toggle('visually-hidden', !isError);
        this.statusTarget.textContent = message;

        this.resetTimer = setTimeout(() => this.reset(), 2000);
    }

    reset() {
        clearTimeout(this.resetTimer);
        this.resetTimer = null;

        this.copyIconTarget.classList.remove('d-none');
        this.successIconTarget.classList.add('d-none');
        this.errorIconTarget.classList.add('d-none');
        this.statusTarget.classList.add('visually-hidden');
        this.statusTarget.textContent = '';
    }
}
