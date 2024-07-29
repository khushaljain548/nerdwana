document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form');
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const changePasswordBtn = document.getElementById('change-password-btn');

    const validatePasswordStrength = (password) => {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        return password.length >= minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars;
    };

    form.addEventListener('submit', (event) => {
        let errorMessage = "";

        if (!validatePasswordStrength(newPassword.value)) {
            errorMessage = "New password must be at least 8 characters long and include uppercase letters, lowercase letters, numbers, and special characters.";
        } else if (newPassword.value !== confirmPassword.value) {
            errorMessage = "New passwords do not match.";
        }

        if (errorMessage) {
            event.preventDefault(); // Prevent form submission
            document.getElementById('popupMessage').innerText = errorMessage;
            document.getElementById('popupOverlay').style.display = 'block';
        }
    });
});

function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
}
