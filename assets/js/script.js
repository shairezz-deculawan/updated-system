document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to permanently delete this tourism spot?')) {
                e.preventDefault();
            }
        });
    });
});
