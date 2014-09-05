Wordpress Plugin for Auth0
====

Single Sign On for Enterprises + Social Login + User/Passwords. For all your WorpdPress instances. Powered by Auth0.

Demo: <http://auth0-wp.azurewebsites.net>

## Installation

Before you start, **make sure the admin user has a valid email that you own**, read the Technical Notes for more information.

1. Install from the WordPress Store or upload the entire wp-auth0 folder to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create an account in Auth0 (https://auth0.com) and add a new Application.
4. Copy the Client ID, Client Secret and Domain from the Settings of the Application.
5. On the Settings of the Auth0 application change the Callback URL to be: http://your-domain/index.php?auth0=1. Using TLS/SSL is recommended for production.
6. Go back to WordPress Settings - Auth0 Settings edit the Domain, Client ID and Client Secret with the ones you copied from Auth0 Dashboard.

## Technical Notes

**IMPORTANT**: By using this plugin you are delegating the site authentication to Auth0. That means that you won't be using the WordPress database to authenticate users anymore and the default WP login box won't show anymore. However, we can still associate your existing users by merging them by email. This section explains how.

When you install this plugin you have at least one existing user in the database (the admin user). If the site is already being used, you probably have more than just the admin. We want you to keep those users, of course.

### Migrating Existing Users

Auth0 allows multiple authentication providers. You can have social providers like Facebook, Twitter, Google+, etc., you can have a database of users/passwords (just like WordPress but hosted in Auth0) or you can use an Enterprise directory like Active Directory, LDAP, Office365, SAML and others. All those authentication providers might give you an email and a flag indicating whether the email was verified or not. We use that email (only if its verified) to associate a previous existing user with the one coming from Auth0.

If the email was not verified (`email_verified` property of the user is `false`) and there is an account with that email in WordPress, the user will be presented with a page saying that the email was not verified and a link to "Re-send the verification email".

For both scenarios you may configure in the WP admin whether is mandatory that the user has a verified email or not.

### Accesing Profile Information

You can access the rich user profile information coming from the Identity Providers. WordPress defines a function called `get_currentuserinfo` to populate the global variable `current_user` with the logged in `WP_User`. Similary we define `get_currentauth0userinfo` that populates `current_user` and `currentauth0_user` with the information of the Normalized profile.

## FAQs

### What should I do if I end up with two accounts for the same user?

Under some situations, you may end up with a user with two accounts. Wordpress allows you to do merge users. You just delete one of the accounts and then attribute its contents to the user you want to merge with. Go to Users, select the account you want to delete, and in the confirmation dialog select another user to transfer the content.

### Can I customize the Login Widget?

You can style the login form by adding a filter like this

    add_filter( 'auth0_login_css', function() {
        return "form a.a0-btn-small { background-color: red }";
    } );

The Login Widget is Open Source. For more information about it: https://github.com/auth0/widget

### Can I access the user profile information?

The Auth0 plugin transparently handles login information for your Wordpress site and the plugins you use, so that it looks like any other login.

### When I install this plugin, will existing users still be able to login?

Yes. Read more about the requirements for that to happen in the Technical Notes.

### What authentication providers do you support?

For a complete list look at https://docs.auth0.com/identityproviders

### "This account does not have an email associated..."

If you get this error, make sure you are requesting the Email attribute from each provider in the Auth0 Dashboard under Connections -> Social (expand each provider). Take into account that not all providers return Email addresses for users (e.g. Twitter). If this happens, you can always add an Email address to any logged in user through the Auth0 Dashbaord (pr API). See Users -> Edit.

## Screenshots

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-1.png)

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-2.png)

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-3.png)

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-4.png)

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-5.png)

![](https://raw.githubusercontent.com/auth0/wp-auth0/master/screenshot-6.png)

Contributed by
=====

* [LaunchPeople](http://launchpeople.dk/)
