/*
 * signup.js
 *
 * authors : Berfy
 *
 * Manages the inscription of auser
 * this script validates first if the fields are correct
 * then makes a asynchronous request to the createUser service to create a new user
 * If some values were wrong (for example, weak password), feedback is provided to the user thanks to the validate function
 */

const signupForm = document.getElementById("signup");
const errorMessagesDiv = document.getElementById("error-messages");
const loginInput = document.getElementById("login");
const nameInput = document.getElementById("name");
const descriptionInput = document.getElementById("description");
const passwordInput = document.getElementById("password");

/**
 * Initiates the validation of a form
 * @return {boolean} return false to prevent the form from being sent (we want to send data via an asynchronous request)
 */
function validateForm(e) {
  e.preventDefault();
  // In case there have beens errors previously, we erase them
  errorMessagesDiv.innerHTML = "";
  let req = new XMLHttpRequest();
  req.open("POST", "services/getUser.php?user=" + loginInput.value, true);
  req.addEventListener("load", handleValidationRequest);
  req.addEventListener("error", e =>
    console.log("Something went wrong, here is the error : " + e)
  );
  req.send(null);
}

/**
 * Validates an input field
 * @param {boolean} errorCondition the error condition
 * @param {String} message the message we want to give to the user if errorCondition is true
 */
function validate(errorCondition, message) {
  if (errorCondition) {
    var errorMessage = document.createElement("p");
    errorMessage.innerHTML = message;
    errorMessage.className = "error-message";
    errorMessagesDiv.appendChild(errorMessage);
    return false;
  } else {
    return true;
  }
}

/**
 * Handles the response of the request made above by keeping on validating the form
 * @param {Object} e the event created by XMLHttpRequest
 */
function handleValidationRequest(e) {
  let data = JSON.parse(e.target.responseText);
  let name = nameInput.value;
  let password = passwordInput.value;
  let login = loginInput.value;
  // If there is a user in database, then the login is already used
  let loginValueValidated = validate(
    login.length > 20 || login.length < 2,
    "Your login must have between 2 and 20 characters"
  );
  let loginInDBValidated = validate(
    data.result !== null,
    "Login is already used"
  );
  let loginValidated = loginValueValidated && loginInDBValidated;
  let nameValidated = validate(name.length === 0, "Your name is empty");
  let passwordValidated = validate(
    password.length < 6,
    "Your password should have at least 6 characters"
  );
  if (loginValidated && nameValidated && passwordValidated) {
    sendForm();
  }
}

/**
 * Sends the form data via an asynchronous POST request
 */
function sendForm() {
  let req = new XMLHttpRequest();
  req.open("POST", "services/createUser.php", true);
  req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  req.addEventListener("load", e => (window.location.href = "profile.php"));
  req.addEventListener("error", e =>
    console.log("Something went wrong, here is the error : " + e)
  );
  let data =
    "name=" +
    nameInput.value +
    "&ident=" +
    loginInput.value +
    "&password=" +
    passwordInput.value;
  if (descriptionInput.value !== "") {
    data += "&description=" + descriptionInput.value;
  }
  req.send(data);
}

window.addEventListener("load", () => {
  signupForm.addEventListener("submit", validateForm);
});
