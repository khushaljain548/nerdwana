document.addEventListener("DOMContentLoaded", function() {
    const signupBtn = document.getElementById('signup-btn');
    const signupForm = document.querySelector('.signup');

    // Function to validate if username is empty
    function isUsernameEmpty(username) {
        return username.trim() === '';
    }

    // Function to validate if username has blank spaces
    function hasBlankSpaces(username) {
        return /\s/.test(username);
    }

    // Function to validate email
    function isValidEmail(email) {
        // Basic email validation regex
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Function to validate password
    function isValidPassword(password) {
        // Password should be at least 8 characters long and contain at least one digit, one lowercase letter, one uppercase letter, and one special character
        var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9\s]).{8,}$/;
        return passwordRegex.test(password);
    }

    // Function to handle signup form submission
    function handleSignup(event) {
        event.preventDefault(); // Prevent default form submission behavior
    
        const username = document.getElementById('username').value.trim(); // Trim whitespace
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
    
        // Check if username is empty
        if (isUsernameEmpty(username)) {
            alert('Please enter a username.');
            return;
        }
    
        // Check if username has blank spaces
        if (hasBlankSpaces(username)) {
            alert('Username should not contain blank spaces.');
            return;
        }
    
        // Validate email
        if (!isValidEmail(email)) {
            alert('Please enter a valid email address.');
            return;
        }
    
        // Validate password
        if (!isValidPassword(password)) {
            alert('Password should be at least 8 characters long and contain at least one digit, one lowercase letter, one uppercase letter, and one special character.');
            return;
        }
    
    
        // If all validations pass, send data to the server using AJAX
        const formData = {
            username: username,
            email: email,
            password: password
        };
    
        // Send an AJAX POST request to the server
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'process.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log('Response from process.php:', xhr.responseText);
                const data = JSON.parse(xhr.responseText);
                if (data.status === 'success') {
                    // Display success message
                    alert(data.message);
                    // Clear form fields
                    document.getElementById('username').value = '';
                    document.getElementById('email').value = '';
                    document.getElementById('password').value = '';
                } else {
                    // Display error message
                    alert(data.message);
                }
            }
        };
        const urlEncodedData = Object.keys(formData).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(formData[key])).join('&');
        xhr.send(urlEncodedData);
    }

    // Attach event listener to
    signupBtn.addEventListener('click', handleSignup);
});