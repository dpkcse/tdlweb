function initAccordion($scope, $) {

    function toggleAccordion1() {
        const accordions = document.querySelectorAll(".wpte-trips-accordion.layout-1");
        if (accordions.length === 0) return;

        accordions.forEach((accordion) => {
            const handleEvent = (e) => {
                const activePanel = e.target.closest(".wpte-card");
                if (!activePanel) return;
                openAccordion(activePanel)
            }
            accordion.addEventListener("mouseover", handleEvent);
            accordion.addEventListener("click", handleEvent);
        })


        function openAccordion(panelToActivate) {
            const accordion = panelToActivate.parentElement;
            const buttons = accordion.querySelectorAll(".wpte-card__trigger");
            const contents = accordion.querySelectorAll(".wpte-card__content-wrap");

            buttons.forEach((button) => {
                button.setAttribute("aria-expanded", "false");
            })

            contents.forEach((content) => {
                content.setAttribute("aria-hidden", "true");
            })

            panelToActivate.querySelector(".wpte-card__trigger").setAttribute("aria-expanded", "true");
            panelToActivate.querySelector(".wpte-card__content-wrap").setAttribute("aria-hidden", "false");
        }
    }

    function toggleAccordion2() {
        const accordions2 = document.querySelectorAll(".wpte-trips-accordion.layout-3");
        if (accordions2.length === 0) return;

        function setContentHeight() {
            accordions2.forEach((accordion2) => {
                const tours = accordion2.querySelectorAll('.wpte-card');
                tours.forEach(tour => {
                    const trigger = tour.querySelector('.wpte-card__trigger');
                    const content = tour.querySelector('.wpte-card__content-wrap');

                    if (trigger.getAttribute('aria-expanded') === 'true') {
                        tour.style.setProperty('--mobile-content-height', `${content.scrollHeight}px`);
                    } else {
                        tour.style.setProperty('--mobile-content-height', '5rem');
                    }
                });
            });
        }

        const debouncedSetContentHeight = debounce(setContentHeight, 200);

        window.addEventListener("resize", debouncedSetContentHeight);

        accordions2.forEach((accordion2) => {

            accordion2.addEventListener("click", (e) => {
                const activePanel = e.target.closest(".wpte-card");
                if (!activePanel) return;

                const buttons = accordion2.querySelectorAll(".wpte-card__trigger");
                const contents = accordion2.querySelectorAll(".wpte-card__content-wrap");

                buttons.forEach((button) => {
                    button.setAttribute("aria-expanded", "false");
                })

                contents.forEach((content) => {
                    content.setAttribute("aria-hidden", "true");
                })

                activePanel.querySelector(".wpte-card__trigger").setAttribute("aria-expanded", "true");
                activePanel.querySelector(".wpte-card__content-wrap").setAttribute("aria-hidden", "false");
                setContentHeight();
            });
            setContentHeight();
        })
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(this, args);
            }, wait)
        }
    }

    toggleAccordion1();
    toggleAccordion2();

}

jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/wptravelengine-trips-accordion.default",
        initAccordion
    );
});
