(function () {
    'use strict';

    /**
     * Modal Helper
     * Handles toggling classes and body scroll prevention
     */
    const toggleModal = (modal, force) => {
        if (!modal) return;

        const isActive = force !== undefined ? force : !modal.classList.contains('is-active');
        modal.classList.toggle('is-active', isActive);
        modal.setAttribute('aria-hidden', !isActive);

        // Prevent body scroll when modal is open
        document.body.style.overflow = isActive ? 'hidden' : '';
    };

    /**
     * OPEN MODAL
     * Finds the modal ID from the button's data attribute
     */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.wprr-view-more-btn');
        if (!btn || !btn.dataset.wprrModal) return;

        e.preventDefault();

        const modalId = btn.dataset.wprrModal;
        const modal = document.getElementById(modalId);

        if (modal) {
            toggleModal(modal, true);
        } else {
            console.error(`[WPRR] Modal not found: ${modalId}`);
        }
    });

    /**
     * CLOSE MODAL
     * Handles overlay and close button clicks
     */
    document.addEventListener('click', function (e) {
        if (
            e.target.classList.contains('wprr-modal-overlay') ||
            e.target.classList.contains('wprr-modal-close')
        ) {
            const activeModal = e.target.closest('.wprr-modal');
            if (activeModal) {
                toggleModal(activeModal, false);
            }
        }
    });

    /**
     * CLOSE MODAL (ESC key)
     */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.wprr-modal.is-active');
            if (activeModal) {
                toggleModal(activeModal, false);
            }
        }
    });

})();
