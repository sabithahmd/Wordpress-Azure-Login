=== Login with Azure ===

Contributors: sabithahmd
Tags: azure, login, sso, authentication
Tested up to: 6.7
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight plugin to enable secure Single Sign-On (SSO) with Azure Active Directory.

== Description ==

Login with Azure simplifies the process of integrating Microsoft Azure Active Directory (Azure AD) with your WordPress site. This plugin enables secure Single Sign-On (SSO), allowing users to log in with their Azure AD credentials.

Key Features

* Azure AD Single Sign-On (SSO)
* Keep your credentials securely in ENV
* Option to disable password login
* Easy configuration through WordPress admin
* Secure and scalable for enterprise use

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the Azure AD settings in the Azure Configuration page under settings menu.

== Frequently Asked Questions ==

= How do I get Azure AD credentials? =
You need to register an app in the Azure Portal.

== External Services ==

This plugin connects to Microsoft Azure services to facilitate authentication and user data retrieval. The integration enables seamless access to Microsoft accounts and related data for the intended functionality of the plugin.

Services Used

1. Microsoft Identity Platform (OAuth 2.0 Authentication)

Purpose: This service is used for authentication and authorization, allowing users to sign in using their Microsoft accounts.
Data Sent: The plugin sends authentication requests to Microsoft, including tenant ID and authorization parameters, when a user attempts to log in.
Endpoints Used:
https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/authorize (authorization request)
https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token (token exchange)

Terms & Privacy:
<a target="_blank" href="https://www.microsoft.com/en-us/servicesagreement/">Microsoft Terms of Use</a>
<a target="_blank" href="https://privacy.microsoft.com/en-us/privacystatement">Microsoft Privacy Policy</a>

2. Microsoft Graph API

Purpose: This API is used to fetch user profile information after authentication.
Data Sent: The plugin sends requests to Microsoft Graph API with an authorization token to retrieve user details (such as name and email).
Endpoint Used:
https://graph.microsoft.com/v1.0/me (retrieves authenticated user information)

Terms & Privacy:
<a target="_blank" href="https://developer.microsoft.com/en-us/graph/terms-of-use">Microsoft Graph API Terms</a>

By using this plugin, users acknowledge that authentication and data retrieval depend on Microsoft services, and their data is subject to Microsoft's terms and privacy policies.

== Changelog ==

= 1.0.0 =

* Initial release.


== Upgrade Notice ==

= 1.0.0 =

Initial release.
