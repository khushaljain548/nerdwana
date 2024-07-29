// Define the agreeAndProceed function in the global scope
function agreeAndProceed() {
    // Add logic to proceed to the next page or perform other actions
    alert('You agreed to the guidelines. Proceeding...');
    window.location.href = 'test.php';
}

function showPopup() {
    document.getElementById('popupOverlay').style.display = 'block';
}

// Function to close the popup
function closePopup() {
    document.getElementById('popupOverlay').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    const loginBtn = document.getElementById('login-btn');
    const loginForm = document.querySelector('.login');

    // Function to validate email
    function isValidEmail(email) {
        // Basic email validation regex
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to handle login form submission
    function handleLogin(event) {
        event.preventDefault(); // Prevent default form submission behavior
    
        const loginEmail = document.getElementById('loginEmail').value.trim();
        const loginPassword = document.getElementById('loginPassword').value;
    
        // Validate email
        if (!isValidEmail(loginEmail)) {
            alert('Please enter a valid email address.');
            return;
        }
    
        // Check if password is empty
        if (loginPassword.trim() === '') {
            alert('Please enter a password.');
            return;
        }
    
        // If all validations pass, send data to the server using AJAX
        const formData = {
            email: loginEmail,
            password: loginPassword
        };
    
        // Send an AJAX POST request to the server
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'login.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Response from login.php:', xhr.responseText);
                const responseData = JSON.parse(xhr.responseText);
                if (responseData.status === 'success') {
                    // Show the popup with community guidelines
                    showPopup();
                } else {
                    alert('Invalid email or password.');
                }
            }
        };
        const urlEncodedData = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
        xhr.send(urlEncodedData);
    }
    
    // Attach event listener to the login form submit button
    loginBtn.addEventListener('click', handleLogin);

    // Function to show the popup
   
});
