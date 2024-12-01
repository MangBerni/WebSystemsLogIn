const validation = new JustValidate("#signup");

validation
    .addField("#name", [
        {
            rule: "required"
        }
    ])
    .addField("#email", [
        {
            rule: "required"
        },
        {
            rule: "email"
        },
        {
            validator: (value) => () => {
                return fetch("validate-email.php?email=" + encodeURIComponent(value))
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (json) {
                        return json.available;
                    });
            },
            errorMessage: "Email already taken"
        }
    ])
    .addField("#password", [
        {
            rule: "required"
        },
        {
            rule: "password"
        },
        {
            validator: (value) => {
                if (value.length < 8) {
                    alert("Password must be at least 8 characters long.");
                    return false; // Prevent form submission
                }
                return true; // Allow form submission
            }
        }
    ])
    .addField("#confirm-password", [
        {
            validator: (value, fields) => {
                return value === fields["#password"].elem.value;
            },
            errorMessage: "Passwords should match"
        }
    ])
    .onSuccess((event) => {
        if (validation.isValid) {
            document.getElementById("signup").submit();
        } else {
            console.error("Validation failed");
        }
    });