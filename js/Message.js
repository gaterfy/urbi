/*
 * Message.js
 *
 * authors : Thomas Lombart - Martin Vasilev
 *
 * description : transforms the message's text (where @ et backlash are escaped but not mentions)
 * in a HTML/DOM element printable
 *
 * The message handles also the popup of a user (in case somebody hovers it)
 * and builds the date
 *
 * (cc) bruno.bogaert -at- univ-lille1.fr  Creative Commons BY-NC-ND
 */

class Message {

    constructor({id, author, datetime, content}) {
        const reg = /(\\([@\\]))|(@([a-zA-Z0-9_]+))/g;
        const replacement = (found, p1, p2, p3, p4) => {
            if (p1) // escaped @ or backslash. (escaped char is p2)
                return p2;
            if (p3) // unescaped @ followed by ident (ident is p4)
                return this.mentionToHTML(p4);
        };
        this.author = author;
        this.datetime = datetime;
        this.mentioned = [];
        let html = content.replace(reg, replacement);
        this.content = document.createElement("p");
        this.content.classList.add("message-content");
        this.content.innerHTML = html;
        this.initMentions(this.content);
    }

    mentionToHTML(ident) {
        this.mentioned.push(ident);
        return '<span class="mention user-popover" data-ident="' + ident + '">@' + ident + '</span>';
    }

    initMentions() {
        let mentions = this.content.querySelectorAll("span.mention");
        for (let m of mentions) {
            m.addEventListener("mouseover", e => {
              let previousPopover = document.querySelector('.popover');
              if (previousPopover) {
                previousPopover.parentNode.removeChild(previousPopover);
              }
              let loginUser = m.textContent.substring(1);
              let popover = document.createElement('span');
              popover.classList.add('popover');
              this.getNameOfMentionedUser(loginUser).then(data => {
                popover.appendChild(new Profile(loginUser, data.result.name).profileToHTML());
                m.appendChild(popover);
              })
            });
            m.addEventListener("click", () => window.location.href = "profile.php?user=" + m.dataset.ident);
        }
    }

    getNameOfMentionedUser(loginUser) {
      return new Promise(function(resolve, reject) {
        var req = new XMLHttpRequest();
        req.open("GET", "services/getUser.php?user=" + loginUser, true);
        req.onload = () => resolve(JSON.parse(req.response));
        req.onerror = () => reject(Error("Network Error"));
        req.send(null);
      });
    }

    // Builds the date of a message into the form "[number] [time] ago"
    buildDate() {
        let deltaSeconds = Math.abs(Math.floor((new Date().getTime()/1000) - (new Date(this.datetime).getTime()/1000)));
        if (deltaSeconds < 60) {
            let spelling = deltaSeconds === 1 ? " second " : " seconds ";
            return deltaSeconds + spelling + "ago";
        } else if (deltaSeconds < 3600) {
            let spelling = deltaSeconds < 120 ? " minute " : " minutes ";
            return Math.floor(deltaSeconds/60) + spelling + "ago";
        } else if (deltaSeconds < 86400) {
            let spelling = deltaSeconds < 7200 ? " hour " : " hours ";
            return Math.floor(deltaSeconds/3600) + spelling + "ago";
        } else if (deltaSeconds < 2628002) {
            // According to Google 1 month = 30.4167 days
            let spelling = deltaSeconds < 172800 ? " day " : " days ";
            return Math.floor(deltaSeconds/86400) + spelling + "ago";
        } else if (deltaSeconds < 31536024) {
            let spelling = deltaSeconds < 5256004 ? " month " : " months ";
            return Math.floor(deltaSeconds/2628002) + spelling + "ago";
        } else {
            let spelling = deltaSeconds < 63072048 ? " year " : " years ";
            return Math.floor(deltaSeconds/31536024) + spelling + " ago";        }
    }

    // Returns the HTML code corresponding to a message
    toHTML() {
        let message = document.createElement("div");
        message.className = "message";
        let author = new Profile(this.author);
        let date = document.createElement("span");
        date.innerHTML = this.buildDate();
        date.className = 'date';
        message.appendChild(date);
        message.appendChild(author.shortProfileToHTML());
        message.appendChild(this.content);
        return message;
    }
};
