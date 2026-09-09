import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['copyIcon', 'successIcon', 'errorIcon', 'status'];

    static values = {
        value: String,
        copiedLabel: String,
        errorLabel: String,
    };

    async copy() {
        this.reset();

        try {
            await navigator.clipboard.writeText(this.valueValue);
            this.showFeedback(this.successIconTarget, this.copiedLabelValue);
        } catch (error) {
            this.showFeedback(this.errorIconTarget, this.errorLabelValue);
        }
    }

    disconnect() {
        clearTimeout(this.resetTimer);
    }

    showFeedback(icon, message) {
        this.copyIconTarget.classList.add('d-none');
        icon.classList.remove('d-none');
        this.statusTarget.textContent = message;

        this.resetTimer = setTimeout(() => this.reset(), 2000);
    }

    reset() {
        clearTimeout(this.resetTimer);
        this.resetTimer = null;

        this.copyIconTarget.classList.remove('d-none');
        this.successIconTarget.classList.add('d-none');
        this.errorIconTarget.classList.add('d-none');
        this.statusTarget.textContent = '';
    }
}
