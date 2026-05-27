document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to section
    document.querySelectorAll('.wpte-sticky-tabs a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = this.getAttribute('href');
            const targetElement = document.querySelector(target);
            const stickyTabs = document.querySelector('.wpte-sticky-tabs');
            
            // Remove active class from all links
            document.querySelectorAll('.wpte-sticky-tabs a').forEach(a => a.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Smooth scroll to target
            if( null !== stickyTabs ) {
                const headerHeight = stickyTabs.offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
            
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Update active tab on scroll
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        const stickyTabs = document.querySelector('.wpte-sticky-tabs');

        if( null !== stickyTabs ) {
            const headerHeight = stickyTabs.offsetHeight;
            document.querySelectorAll('.wpte-tab-content').forEach(content => {
                const top = content.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
                const bottom = top + content.offsetHeight;

                if (scrollPosition >= top && scrollPosition < bottom) {
                    const id = content.getAttribute('id');
                    document.querySelectorAll('.wpte-sticky-tabs a').forEach(a => a.classList.remove('active'));
                    document.querySelector(`.wpte-sticky-tabs a[href="#${id}"]`).classList.add('active');
                }
            });
        }
    });
});