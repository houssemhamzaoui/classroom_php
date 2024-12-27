function hasNoNumber(str) {
    for (let i = 0; i < str.length; i++) {
        if (str[i] >= '0' && str[i] <= '9') {
            return false;
        }
    }
    return true;
}
function isValidEmail(email) {
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(email);
}
function validation() {
    const result = document.getElementById("result");
    if (document.FormFill.Username.value == "") {
        result.innerHTML = "Enter Username";
        result.classList.remove("dis");
        return false;
    }
    if (document.FormFill.Username.value.length < 6) {
        result.innerHTML = "At least six letters";
        result.classList.remove("dis");
        return false;
    }
    if (!hasNoNumber(document.FormFill.Username.value)) {
        result.innerHTML = "Your username should not contain any numbers";
        result.classList.remove("dis");
        return false;
    }
    if (document.FormFill.Email.value == "") {
        result.innerHTML = "Enter your email";
        result.classList.remove("dis");
        return false;
    }
    if (!isValidEmail(document.FormFill.Email.value)) {
        result.innerHTML = "Invalid email format";
        result.classList.remove("dis");
        return false;
    }
    if (document.FormFill.Password.value == "") {
        result.innerHTML = "Enter your password";
        result.classList.remove("dis");
        return false;
    }
    if (document.FormFill.Password.value.length < 8) {
        result.innerHTML = "Password must be at least 8 characters long";
        result.classList.remove("dis");
        return false;
    }
    if (document.FormFill.Password.value != document.FormFill.Cpassword.value) {
        result.innerHTML = "Passwords do not match";
        result.classList.remove("dis");
        return false;
    }
    const popup = document.getElementById("popup");
    popup.classList.add("open-slide");
    return false
}

function CloseSlide() {
    const popup = document.getElementById("popup");
    popup.classList.remove("open-slide");
}
