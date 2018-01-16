/*
 * index.js
 * authors : berfy
 *
 * Used for the index.php file
 * This page gets the most recent messages and display it
 * the user can also search other users on this page, and to see on the same page,
 * what are its recent posts
 */

var nbOfMessages = 15;
var activeQuery = 'services/findMessages.php?';

function get(url) {
  return new Promise(function(resolve, reject) {
    var req = new XMLHttpRequest();
    req.open("GET", url, true);
    req.onload = () => resolve(JSON.parse(req.response));
    req.onerror = () => reject(Error("Network Error"));
    req.send(null);
  });
}


function getNewMessages() {
  get('services/isLogged.php')
    .then(data => {
      if (data.result) {
        console.log(data.result)
        activeQuery += "follower=" + data.result;
      }
      get(activeQuery)
        .then(data => handleFindMessagesRequest(data))
    });
}


/**
 * Handles the response of the request made in the getNewMessages() function
 * @param {Object} e the event created by XMLHttpRequest
 */
function handleFindMessagesRequest(data) {
  let foundUsers = document.getElementById("found-users");
  foundUsers.innerHTML = '';
  let userInput = document.getElementById("user");
  userInput.placeholder = 'Search users here...';
  let messageThread = document.getElementById("message-thread");
  messageThread.innerHTML = '';
  if (data.result.list.length > 0) {
    data.result.list.forEach(message => messageThread.appendChild(new Message(message).toHTML()));
  } else {
    let notFound = document.createElement("p");
    notFound.innerHTML = "<p class='not-found'><i class='fa fa-2x fa-frown-o' aria-hidden='true'></i>You are currently not following any users!</p>";
    messageThread.appendChild(notFound);
  }
  if (data.result.hasMore) {
    let loadMoreMessages = document.createElement("button");
    loadMoreMessages.innerHTML = "Load more messages";
    loadMoreMessages.addEventListener('click', loadMore);
    messageThread.appendChild(loadMoreMessages);
  }
}

/* CONSTRUCT MESSAGE */

function loadMore() {
  this.parentNode.removeChild(this);
  nbOfMessages += 15;
  activeQuery += '&count=' + nbOfMessages;
  get(activeQuery).then(data => handleFindMessagesRequest(data));
}


/* SEARCH USERS */

let userInput = document.getElementById("user");

userInput.addEventListener("keyup", retrieveUsers);

function retrieveUsers() {
  if (this.value.length > 1) {
    let messageThread = document.getElementById("message-thread");
    messageThread.innerHTML = '';
    get('services/findUsers.php?searched=' + this.value).then(data => handleFindUsersRequest(data));
  }
}


function handleFindUsersRequest(data) {
  let foundUsers = document.getElementById("found-users");
  foundUsers.innerHTML = '';
  data.result.list.forEach(user => {
    let userProfile = new Profile(user.ident, user.name);
    let userElement = userProfile.profileToHTML();
    userElement.addEventListener("click", function() {
      let req = new XMLHttpRequest();
      let messageThread = document.getElementById("message-thread");
      messageThread.innerHTML = '';
      nbOfMessages = 15;
      activeQuery = 'services/findMessages.php?author=' + user.ident;
      get(activeQuery).then(data => handleFindMessagesRequest(data));
    });
    foundUsers.appendChild(userElement);
  });
}

/* END SEARCH USERS */

window.addEventListener("load", getNewMessages);
