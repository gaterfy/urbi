/*
 * signout.js
 * authors : Thomas Lombart - Martin Vasilev
 *
 * Logs out a user by destroying the session
 * If the process has been successfull, the user is redirected to the homepage
 */


/**
 * Main function that initiates the logout process of the user
 */
function logoutUser() {
  let req = new XMLHttpRequest();
  req.open("GET", "services/logout.php", true);
  req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  req.addEventListener("load", (e) => {
    let data = JSON.parse(e.target.responseText);
    if (data.result !== null) {
      window.location.href = "index.php";
    }
  });
  req.addEventListener("error", (e) => {
    console.log("Something went wrong, here is the error : " + e);
  });
  req.send(null);
}


const logoutButton = document.getElementById("logout");

if (logoutButton) {
  logoutButton.addEventListener("click", logoutUser);
}
