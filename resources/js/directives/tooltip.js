export default {
    mounted(el, binding) {
        const tooltipText = binding.value || '';
        const position = Object.keys(binding.modifiers)[0] || 'top';

        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.className = `custom-tooltip tooltip-${position}`;
        tooltip.textContent = tooltipText;
        document.body.appendChild(tooltip);

        const positionTooltip = () => {
            requestAnimationFrame(() => {
                // Get element position relative to viewport
                const rect = el.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();

                let top, left;
                switch (position) {
                    case 'top':
                        top = rect.top - tooltipRect.height - 8;
                        left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                        break;
                    case 'bottom':
                        top = rect.bottom + 8;
                        left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                        break;
                    case 'left':
                        top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                        left = rect.left - tooltipRect.width - 8;
                        break;
                    case 'right':
                        top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                        left = rect.right + 8;
                        break;
                    default:
                        top = rect.top - tooltipRect.height - 8;
                        left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                }

                // getBoundingClientRect returns viewport values; add scroll offsets
                tooltip.style.top = `${top + window.scrollY}px`;
                tooltip.style.left = `${left + window.scrollX}px`;
            })
        }

        // Function to update tooltip position on scroll
        const repositionTooltip = () => {
            if (tooltip.style.visibility === 'visible') {
                positionTooltip();
            }
        };

        const showTooltip = () => {
            tooltip.style.opacity = '1';
            tooltip.style.visibility = 'visible';
            positionTooltip();
            window.addEventListener('scroll', repositionTooltip);
        };

        const hideTooltip = () => {
            tooltip.style.opacity = '0';
            tooltip.style.visibility = 'hidden';
            window.removeEventListener('scroll', repositionTooltip);
        };

        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);

        // Store references for cleanup
        el._tooltip = tooltip;
        el._repositionTooltip = repositionTooltip;
    },

    beforeUnmount(el) {
        if (el._tooltip) {
            el._tooltip.remove();
            delete el._tooltip;
        }
        if (el._repositionTooltip) {
            window.removeEventListener('scroll', el._repositionTooltip);
            delete el._repositionTooltip;
        }
    }
};
