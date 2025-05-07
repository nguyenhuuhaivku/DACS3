document.addEventListener("DOMContentLoaded", function() {
    let observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounting(entry.target.querySelector('.counter'));
                observer.unobserve(entry.target);
            }
        });
    }, {threshold: 0.5});

    document.querySelectorAll('.counter-box').forEach(box => {
        observer.observe(box);
    });
});

function startCounting(counter) {
    const target = parseInt(counter.getAttribute('data-count'));
    let count = 0;
    const duration = 2000; // 2 seconds
    const steps = 50; // Update every 50ms
    const increment = target / (duration / steps);

    const timer = setInterval(() => {
        count += increment;
        if (count >= target) {
            counter.textContent = target;
            clearInterval(timer);
        } else {
            counter.textContent = Math.floor(count);
        }
    }, steps);
} 