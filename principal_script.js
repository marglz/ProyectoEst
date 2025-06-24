    const stars = document.querySelectorAll('.star-rating span');
    const ratingInput = document.getElementById('calificacion');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;

            stars.forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
            let prev = this.previousElementSibling;
            while (prev) {
                prev.classList.add('selected');
                prev = prev.previousElementSibling;
            }
        });
    });
