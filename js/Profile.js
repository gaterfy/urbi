/*
 * Profile.js
 *
 * authors : Berfy
 *
 * Constructs a new profile, this script ie meant to be included
 * in other pages just to simplify the construction of the HTML Code of a profile 
 */

class Profile {
  constructor(login, name = '', description = '') {
    this.login = login;
    this.name = name;
    this.description = description;
  }

  shortProfileToHTML() {
    let profile = document.createElement('div');
    profile.className = "user";
    let avatar = "<img class='avatar' src='services/getAvatar.php?user=" + this.login + "'>";
    let nameAndLogin = "<div><a href='profile.php?user=" + this.login + "'><span class='mention author'>@" + this.login + "</span></a></div>";
    profile.innerHTML = avatar + nameAndLogin;
    return profile;
  }

  profileToHTML() {
    let profile = document.createElement('div');
    profile.className = "user";
    let avatar = "<img class='avatar' src='services/getAvatar.php?user=" + this.login + "'>";
    let nameAndLogin = "<div><span>" + this.name + "</span><a href='profile.php?user=" + this.login + "'><span class='mention'>@" + this.login + "</span></a></div>";
    profile.innerHTML = avatar + nameAndLogin;
    return profile;
  }
}
