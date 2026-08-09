function togglePassword() {

    const password = document.getElementById("password");
    const button = document.querySelector(".show-password");

    if (password.type === "password") {

        password.type = "text";
        button.textContent = "Hide";

    } else {

        password.type = "password";
        button.textContent = "Show";
    }
}


function checkPasswordStrength() {

    const password = document.getElementById("password");
    const strength = document.getElementById("password-strength");

    const value = password.value;

    if (value.length === 0) {

        strength.textContent = "";

    } else if (value.length < 8) {

        strength.textContent = "Weak password";

    } else if (
        /[A-Z]/.test(value) &&
        /[a-z]/.test(value) &&
        /[0-9]/.test(value)
    ) {

        strength.textContent = "Strong password";

    } else {

        strength.textContent = "Medium password";
    }
}