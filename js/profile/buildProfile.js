/*
 * buildProfile.js
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Constructs the profile of a user by putting all the informations in the correct HTML elements
 * Basically, 5 elements are constructed :
 * - User informations (profile picture, name, login, description, statistics)
 * - His mentioned thread
 * - His authored thread (that is to say, messages he wrote)
 * - His followers
 * - Users he follow
 * All informations are get through a web service and are made in an asynchronous way
 */

const login = document.getElementById("user-connected").value;
const followButton = document.getElementById("follow");
const nameElement = document.getElementById("name");
const descriptionElement = document.getElementById("description");
const loginElement = document.getElementById("login");
const totalMessagesElement = document.getElementById("total-messages");
const totalFollowersElement = document.getElementById("total-followers");
const mainContainerElement = document.getElementsByClassName('main-container')[0];

let nbOfMessages = 15;

/**
 * Initializes the building of a page
 */
function init() {
  // All asynchronous requests
  let promises = [
    get("services/getUser.php?user=" + login + "&type=long"),
    get("services/findMessages.php?author=" + login),
    get("services/findMessages.php?mentioned=" + login),
    get("services/getFollowing.php?user=" + login),
    get("services/getFollowing.php?user=" + login + "&r=true")
  ];
  get('services/getUser.php?user=' + login)
  .then(data => {
    if (data.message === 'user is not in the database') {
      buildNotFound();
    } else {
      // We handle all the asynchronous requests
      Promise.all(promises)
        .then(data => buildPage(data))
        .catch(err =>
          console.log(
            "Une erreur est survenue lors de la récupérations des informations : " +
              err
          )
        );

      if (followButton) {
        followButton.addEventListener("click", sendFollowQuery);
      }

      let mentionedButton = document.getElementById("mentioned-button");
      mentionedButton.addEventListener("click", changeTab);
    }
  });
}

/**
 * Gets via a XMLHttpRequest the data of a service
 * @param {String} url the url of the service
 */
function get(url) {
  return new Promise(function(resolve, reject) {
    var req = new XMLHttpRequest();
    req.open("GET", url, true);
    req.onload = () => resolve(JSON.parse(req.response));
    req.onerror = () => reject(Error("Network Error"));
    req.send(null);
  });
}

function buildNotFound() {
  mainContainerElement.innerHTML = '';
  let notFound = document.createElement('div');
  notFound.className = 'not-found-user';
  notFound.innerHTML = 'User not found';
  mainContainerElement.appendChild(notFound);
}

/**
 * Builds the pages of the user
 * @param {Object} user the informations of the user
 * @param {Object} messages the messages of the user
 * @param {Object} mentions the mentions messages of the user
 * @param {Object} followers the followers of the user
 * @param {Object} following the list of the users this user is following
 */
function buildPage([user, messages, mentions, followers, following]) {
  buildProfile(user.result);
  buildMessages(document.getElementById("authored-thread"), messages.result);
  buildMessages(document.getElementById("mentioned-thread"), mentions.result);
  buildFollow(
    document.querySelector(".followers"),
    followers.result,
    followers.args
  );
  buildFollow(
    document.querySelector(".following"),
    following.result,
    following.args
  );
}

/**
 * Builds the profile of the user by filling the HTML elements
 * @param {String} ident the login of the user
 * @param {String} name the name of the user
 * @param {String} description the description of the user
 * @param {Array} stats the stats of the user
 */
function buildProfile({ ident, name, description, stats }) {
  const { messages, followers } = stats;
  nameElement.innerHTML = name;
  descriptionElement.innerHTML = description;
  loginElement.innerHTML = ident;
  totalFollowersElement.innerHTML =
    followers + ' <i class="fa fa-users" aria-hidden="true"></i>';
  totalMessagesElement.innerHTML =
    messages + ' <i class="fa fa-envelope-o" aria-hidden="true"></i>';
}

/**
 * Builds the messages of the users
 * @param {HTMLElement} element the HTML element to fill
 * @param {Object} messages the messages informations
 */
function buildMessages(element, messages) {
  element.innerHTML = '';
  if (messages.list.length > 0) {
    messages.list.forEach(message =>
      element.appendChild(new Message(message).toHTML())
    );
  } else {
    element.innerHTML =
      "<p class='not-found'><i class='fa fa-2x fa-frown-o' aria-hidden='true'></i>This user doesn't have any messages!</p>";
  }
  if (messages.hasMore) {
    let loadMoreMessages = document.createElement("button");
    loadMoreMessages.className = "load-more";
    loadMoreMessages.innerHTML = "Load more messages";
    loadMoreMessages.addEventListener("click", () => {
      let typeOfMessage = element.id === 'authored-thread' ? 'author' : 'mentioned';
      nbOfMessages += 15;
      get("services/findMessages.php?" + typeOfMessage + "=" + login + "&count=" + nbOfMessages)
      .then(messages => buildMessages(document.getElementById(element.id), messages.result))
    });
    element.appendChild(loadMoreMessages);
  }
}



/**
 * Changes the tab (Mentioned -> Authored and vice-versa)
 * @param {Object} e the element clicked
 */
function changeTab(e) {
  let authored = document.getElementById("authored");
  let mentioned = document.getElementById("mentioned");
  if (e.target.id == "authored-button") {
    // Give the selected class
    e.target.classList.add("active");
    document.getElementById("mentioned-button").classList.remove("active");
    // Display the correct block & hide the other
    authored.style.display = "block";
    mentioned.style.display = "none";
    // Change the event listener
    e.target.removeEventListener("click", changeTab);
    document
      .getElementById("mentioned-button")
      .addEventListener("click", changeTab);
  } else {
    // Give the selected class
    e.target.classList.add("active");
    document.getElementById("authored-button").classList.remove("active");
    // Display the correct block & hide the other
    authored.style.display = "none";
    mentioned.style.display = "block";
    // Change the event listener
    e.target.removeEventListener("click", changeTab);
    document
      .getElementById("authored-button")
      .addEventListener("click", changeTab);
  }
}

/**
 * Builds the following and followers list of the user
 * @param {HTMLElement} element the element to fill
 * @param {Array} follow the list of the follow service
 * @param {Object} args the arguments passed to the service
 */
function buildFollow(element, follow, args) {
  element.innerHTML = "";
  const viewer = document.getElementById("viewer").value;
  const viewerName = document.getElementById("viewer_name").value;
  if (follow.length > 0) {
    follow.forEach(profile => {
      // If we find a profile in the followers, the button is changed to unfollow
      if (
        profile.login === viewer &&
        followButton &&
        !args.hasOwnProperty("r")
      ) {
        followButton.innerHTML = "UNFOLLOW";
      }
      let builtProfile = new Profile(
        profile.login,
        profile.name
      ).profileToHTML();
      element.appendChild(builtProfile);
    });
  } else {
    element.innerHTML +=
      "<p class='not-found'><i class='fa fa-2x fa-frown-o' aria-hidden='true'></i>No users found!</p>";
  }
}

/**
 * Allow a user to follow another one
 * @param {Object} e the event
 */
function sendFollowQuery(e) {
  let action;
  if (followButton.innerHTML === "UNFOLLOW") {
    action = "unfollow";
    followButton.innerHTML = "FOLLOW";
  } else {
    action = "follow";
    followButton.innerHTML = "UNFOLLOW";
  }
  get("services/setRelation.php?followed=" + login + "&action=" + action).then(
    rebuildAfterFollow
  );
}

/**
 * Rebuilds the elements modified after a user have followed another one
 */
function rebuildAfterFollow() {
  get("services/getUser.php?user=" + login + "&type=long").then(user =>
    buildProfile(user.result)
  );
  get("services/getFollowing.php?user=" + login).then(followers =>
    buildFollow(
      document.querySelector(".followers"),
      followers.result,
      followers.args
    )
  );
}

window.addEventListener("load", init);
