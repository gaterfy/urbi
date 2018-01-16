/*
 * sendMessage.js
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Allows the user to post a message whose characters are limited
 * the message cannot be empty and its length cannot be more than 140 characters 
 */

const postMessageForm = document.getElementById("postMessage");
const errorMessagesDiv = document.getElementById("error-messages");
const messageInput = document.getElementById("message");

/**
 * Initiates the sending of a form
 */
function initSendForm(e) {
  e.preventDefault();
  // In case there have beens errors previously, we erase them
  errorMessagesDiv.innerHTML = "";
  if (messageInput.value.length === 0) {
    createErrorMessage("Your message is empty");
  } else if (messageInput.value.length > 140) {
    createErrorMessage("Your message is too long (140 characters max)");
  } else {
    sendMessageForm().then(rebuildAfterPost);
  }
}

/**
 * Sends the form data via an asynchronous POST request
 */
function sendMessageForm() {
  return new Promise(function(resolve, reject) {
    let req = new XMLHttpRequest();
    req.open("POST", "services/postMessage.php", true);
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    req.onload = () => resolve(JSON.parse(req.response));
    req.onerror = () => reject(Error("Network Error"));
    req.send("source=" + messageInput.value);
  });
}

/**
 * Rebuilds the messages after a post
 */
function rebuildAfterPost() {
  messageInput.value = "";
  get("services/getUser.php?user=" + login + "&type=long").then(user =>
    buildProfile(user.result)
  );
  get("services/findMessages.php?author=" + login).then(messages =>
    buildMessages(document.getElementById("authored-thread"), messages.result)
  );
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
  postMessageForm.addEventListener("submit", initSendForm);
});
