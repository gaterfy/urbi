/*
 * updateProfile.js
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Allows the user to update his profile, that is to say :
 * - his name
 * - his description
 * - his password
 * - his avatar
 * Feedback (= error/success messages) messages are provided
 */

const updateProfileForm = document.getElementById("updateProfile");
const errorMessagesDiv = document.getElementById("error-messages-profile");
const nameInput = document.getElementById("name");
const descriptionInput = document.getElementById("description");
const passwordInput = document.getElementById("password");
const userCurrentlyLogged = document.getElementById("user").value;


/**
 * Handles the response of the request made by sendForm by logging the error
 * @param {Object} e the event created by XMLHttpRequest
 */
const handleError = e => console.log("Something went wrong, here is the error : " + e);


function setup() {

    let inputFile = document.getElementById("input-file");
    inputFile.addEventListener('change', e => {
        let filename = e.target.value.split('\\').pop();
        let label = inputFile.nextElementSibling;
        if (filename) {
            label.innerHTML = '<i class="fa fa-check" aria-hidden="true"></i> ' + filename;
        }
    });
    let req = new XMLHttpRequest();
    req.open('GET', 'services/getUser.php?user=' + userCurrentlyLogged + "&type=long", true);
    req.addEventListener("load", handleGetUserRequest);
    req.addEventListener("error", handleError);
    req.send(null);

    let avatarForm = document.getElementById('avatar');
    avatarForm.addEventListener('submit', sendAvatarRequest);
}


/**
 * Handles the response of the request made in the setup() function
 * @param {Object} e the event created by XMLHttpRequest
 */
function handleGetUserRequest(e) {
    var data = JSON.parse(this.responseText);
    nameInput.value = data.result.name;
    descriptionInput.value = data.result.description;
}


/* UPDATE INFORMATIONS */


/**
 * Initiates the validation of a form
 * @return {boolean} return false to prevent the form from being sent (we want to send data via an asynchronous request)
 */
function initValidateForm() {
    // In case there have beens errors previously, we erase them
    errorMessagesDiv.innerHTML = '';
    let nameValidated = validate(nameInput.value.length > 25 || nameInput.value.length === 0, "Your name should be non-empty and have less than 25 characters");
    let passwordValidated = validate(passwordInput.value.length > 0 && passwordInput.value.length < 6, "Your new password should have at least 6 characters");
    let descriptionValidated = validate(descriptionInput.value.length > 2048)
    let validated = nameValidated && passwordValidated && descriptionValidated;
    if (validated) {
        sendProfileForm();
    }
    return false;
}


/**
 * Sends the form data via an asynchronous POST request
 * @param {boolean} validated tells if the form contains valid values
 */
function sendProfileForm() {
    let req = new XMLHttpRequest();
    req.open(updateProfile.method, updateProfile.action, true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.addEventListener("load", handleUpdateAnswer);
    req.addEventListener("error", handleError);
    let data = "name=" + nameInput.value + "&description=" + descriptionInput.value;
    if (passwordInput.value.length !== 0) {
        data += "&password=" + passwordInput.value;
    }
    req.send(data);
}


/**
 * Validates an input field
 * @param {boolean} errorCondition the error condition
 * @param {String} message the message we want to give to the user if errorCondition is true
 */
const validate = (errorCondition, message) => {
    if (errorCondition) {
        let errorMessage = document.createElement("p");
        errorMessage.innerHTML = message;
        errorMessage.className = "error-message";
        errorMessagesDiv.appendChild(errorMessage);
        return false;
    } else {
        return true;
    }
}


const addMessage = (message, isError) => {
    let feedbackDiv = document.getElementById("feedback");
    feedbackDiv.innerHTML = '';
    feedbackDiv.className= '';
    feedbackDiv.classList.add('feedback');
    feedbackDiv.classList.add('show');
    let feedbackMessage = document.createElement("p");
    feedbackMessage.textContent = message;
    feedbackMessage.classList.add("feedback-message");
    isError ? feedbackDiv.classList.add("error") : feedbackDiv.classList.add("success");
    feedbackDiv.appendChild(feedbackMessage);
}


/* AVATAR */

function sendAvatarRequest(e) {
    e.preventDefault();
    var req = new XMLHttpRequest();
    req.open("POST", "services/uploadAvatar.php");
    req.addEventListener('load', handleUpdateAnswer);
    req.addEventListener('error', handleError);
    var data = new FormData(this);
    req.send(data);
}


function handleUpdateAnswer(e) {
    let data = JSON.parse(this.responseText);
    if (data.status === 'ok') {
        addMessage('Your profile has been successfully updated', false);
    } else {
        addMessage(data.message, true);
    }
}

window.addEventListener('load', setup);
