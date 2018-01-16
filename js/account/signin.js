/*
 * signin.js
 *
 * authors : Berfy
 *
 * Scripts that manages the signin of a user
 * this script makes a asynchronous request to the login service in order to log a user
 * if an error occur, feedback is provided to the user
 */

const signinForm = document.getElementById("signin");
const errorMessagesDiv = document.getElementById("error-messages");
const loginInput = document.getElementById("login");
const passwordInput = document.getElementById("password");

/**
 * Sends the form data via an asynchronous POST request
 *
 * @param {Object} e the event created by XMLHttpRequest
 */
function sendForm(e) {
  // In case there have beens errors previously, we erase them
  errorMessagesDiv.innerHTML = "";
  e.preventDefault();
  let req = new XMLHttpRequest();
  req.open("POST", "services/login.php", true);
  req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  req.addEventListener("load", handleLoginResponse);
  req.addEventListener("error", e =>
    console.log("Something went wrong, here is the error : " + e)
  );
  req.send("login=" + loginInput.value + "&password=" + passwordInput.value);
}

/**
 * Handles the response of the request made above by redirecting user to the index page
 * or by giving feedback to the user of what is wrong
 * @param {Object} e the event created by XMLHttpRequest
 */
function handleLoginResponse(e) {
  let data = JSON.parse(e.target.responseText);
  if (data.result === null) {
    switch (data.message) {
      case "invalid password":
        createErrorMessage("Your password is incorrect");
        break;
      case "user is not in the database":
        createErrorMessage("Your username is incorrect");
        break;
      case "user already logged":
        createErrorMessage("You are already logged");
        break;
      default:
        createErrorMessage("Parameters are not set");
    }
  } else {
    window.location.href = "index.php";
  }
}

/**
 * Creates an error message in the errorMessagesDiv element :
 * the message inserted has the class 'error-message' and its content is
 * the parameter of the function
 * @param {String} message the content of the message
 */
function createErrorMessage(message) {
  let errorMessage = document.createElement("p");
  errorMessage.innerHTML = message;
  errorMessage.className = "error-message";
  errorMessagesDiv.appendChild(errorMessage);
}

window.addEventListener("load", () => {
  signinForm.addEventListener("submit", sendForm);
});
