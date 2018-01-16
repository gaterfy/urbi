# Projet Urbik : Application web

---

## Table des matières

1. [Fonctionnalités](#fonctions)
2. [Base de données](#base)
3. [Structuration du projet](#structuration)
4. [Remarques](#remarques)
5. [Test du projet](#test)

---

### <a name="fonctions">1 Fonctionnalités</a>

Le projet implante toutes les fonctionnalités demandées, c'est-à-dire :

*Voir l'agenda
*ecrire a un employé
*Voir le menu cantine en Pdf
*suivre le news de l'entreprise
* Voir sur la page d'accueil la liste des messages les plus récents
* Rechercher un utilisateur
  * Lorsque l'on clique sur cet utilisateur, la liste des messages de la page d'accueil devient celle de l'utilisateur recherché
  * Lorsque l'on clique sur le login (mis en évidence), on va sur le profil de l'utilisateur
* Voir le profil d'un utilisateur
  * Nom, login, avatar et description
  * Sa liste de messages mais aussi de mentions
* Follow et unfollow un employé
* Créer un compte, se logger, se délogger
* Mettre à jour son profil (informations )
* Popup de l'utilisateur mentionné lorsque l'on survole une mention
**BONUS :**
* Voir la liste de followers sur le profil d'un utilisateur
* Voir quelque stats : le nombre de followers et de messages
* La date a été améliorée, elle est sous la forme "*nombre* *temps* ago" pour plus de clarté
* Messages d'erreurs lorsque l'on se logge ou s'inscrit
---

### <a name="base">2 Base de données</a>

Les bases de données sont structurées en 6 tables :

* `users` :
  * `login` : le login de l'utilisateur, celui-ci sert de clé primaire
  * `name` : le nom complet de l'utilisateur
  * `description` : la courte description de l'utilisateur
  * `password` : le mot de passe de l'utilisateur
* `messages` :
  * `id` : un entier étant la clé primaire de la table
  * `content` : le contenu du message
  * `author` : l'auteur du message
  * `date` : la date de publication du message (par défaut la date est celle à laquelle le message est enregistré dans la base de données)
* `mentions` :
  * `message_id` : l'identifiant du message comportant la citation, c'est donc une clé étrangère (messages -> id)
  * `users_id` : l'utilisateur qui a été cité, c'est une clé étrangère également (users -> login)
* `followers` :
  * `user_followed` : la personne suivie, clé étrangère (users -> login)
  * `user_following` : la personne qui suit, clé étrangère (users -> login)
* `avatars` :
  * `login` : le login de l'utilisateur, clé primaire et étrangère (users -> login)
* `news` : le news de l entreprise


Le fichier permettant de créer ces tables est : `db.sql`

### <a name="structuration">3 Structuration du projet</a>

* `css` :
  * `feedback.css`
  * `footer.css`
  * `form.css`
  * `header.css`
  * `index.css`
  * `popover.css`
  * `profile.css`
* `helpers` :
  * `connection.php`
  * `crypt.php`
  * `default.png`
  * `initData.php`
* `js/`
  * `account` :
    * `signin.js`
    * `signout.js`
    * `signup.js`
  * `profile` :
    * `buildProfile.js`
    * `sendMessage.js`
    * `updateProfile.js`
  * `index.js`
  * `Message.js`
  * `Profile.js`
* `lib/`
  * `MessageMentions.class.php`
* `services/`
  * `createUser.php`
  * `findMessages.php`
  * `findUsers.php`
  * `getAvatar.php`
  * `getFollowing.php`
  * `getMessage.php`
  * `getUser.php`
  * `isLogged.php`
  * `login.php`
  * `logout.php`
  * `postMessage.php`
  * `setProfile.php`
  * `setRelation.php`
  * `uploadAvatar.php`
* `db.sql`
* `footer.php`
* `header.php`
* `index.php`
* `profile.php`
* `RAPPORT.md`
* `README.md`
* `settings.php`
* `signin.php`
* `signup.php`



### <a name="test">5 Test du projet</a>

Voici comment vous pouvez tester les fonctionnalités du de l application :

1. Cliquez sur index puis cliquez sur *Sign in* pour vous connectez.
2. Vous vous rendez compte que vous n'avez pas de compte, cliquez sur *No account ? Click here !*
3. Vous voici sur la page vous permettant de créer votre compte. Testez par vous même les limites (mot de passe trop petit, ...), des erreurs seront affichés.
4. Vous êtes redirigés sur votre profil qui est un peu triste, pas d'avatar, pas de messages, pas de followers, mais nous allons y remédier. Cliquez sur *settings* tout en haut de la page.
5. Mince ! Vous avez peut être mal tapé votre nom ou choisi un mot de passe trop faible, éditez vos informations et cliquez sur *Update my profile*
6. Ajoutez une nouvelle photo de profil et cliquez sur *Upload*. Si une erreur arrive, vous le saurez par l'intermédiaire d'un message sur la page.
7. Votre profil a bien été modifié ? Parfait ! Cliquez sur *Go back to my profile*
8. Que s'est-il passé dans votre journée ? Comment vous sentez vous ? Postez un message pour exprimer vos pensées !
9. Pourquoi ne pas dire aux créateurs de ce projet qu'ils ont bien travaillé ? Postez un message en citant nos logins (@thomlom et @ikinz)
10. Bon, votre profil est plus vivant ! Cependant vous ne suivez toujours personne... Retourner sur la page d'accueil en cliquant sur la maison.
11. Oui, vous l'avez remarqué, votre fil des messages est vide... Recherchez un utilisateur et cliquez sur l'un des résultats. Vous pouvez voir sur la page d'accueil tous ses messages. Peut être en a-t-il écrit beaucoup ? Dans ce cas, cliquez sur *Load More* tout en bas.
12. Si l'un des messages mentionne un autre utilisateur, survolez le login de cet utilisateur et vous verrez de plus amples informations.
13. Rendez-vous sur le profil de la personne que vous avez recherché et cliquez sur *Follow* ! Super, vous pouvez voir directement les informations de ce profil mis à jour ! Il a un follower en plus : VOUS !
14. Vous avez déjà touché à l'informatique et vous remarquez que l'URL a un paramètre `user` sur le profil de l'utilisateur que vous suivez, vous avez un esprit vicieux et vous vous dites "essayons de voir ce que ça donne si je rentre n'importe quoi" et constatez le résultat.
15. Malheureusement vous etes assez peu connu et personne ne vous écris. Pourtant, vous avez envie que quelqu'un vous follow. Vous décidez donc de créer un deuxième compte. Cliquez sur *Logout* et créez vous un autre compte.
16. Avec cet autre compte écrivez un message en vous citant vous même, et suivez vous.
17. Déconnectez vous à nouveau et revenez sur votre compte d'origine. Allez sur votre profil, génial ! Vous avez un follower ! Cliquez sur mentioned, que se passe-t-il ? Quelqu'un vous a mentionné ? C'est le début de la popularité. 
18. Faites ce que vous voulez maintenant !
19. cliquez sur agenda et voir les événements avenir.
20. cliquez sur menu cantine afin d afficher le menu cantine.


---

## Auteurs
##### KUNSANGABO NDONGALA
