document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (e) {
        const username = form.username.value.trim();
        const password = form.password.value.trim();

        let errors = [];

        // Username validation
        if (username === "") {
            errors.push("Username is required.");
        } else if (username.length < 3) {
            errors.push("Username must be at least 3 characters.");
        }

        // Password validation
        if (password === "") {
            errors.push("Password is required.");
        } else if (password.length < 4) {
            errors.push("Password must be at least 4 characters.");
        }

        // Show errors
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
});