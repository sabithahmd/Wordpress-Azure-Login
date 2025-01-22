# WordPress Login with Azure Plugin

A lightweight WordPress plugin that enables secure Single Sign-On (SSO) with Azure Active Directory. Users can log in to your WordPress site using their Azure AD credentials for seamless and secure authentication.

## Features

- Azure AD Single Sign-On (SSO)
- Keep your credentials securely in ENV
- Option to disable password login
- Easy configuration through WordPress admin
- Secure and scalable for enterprise use

## Installation

1. Download the plugin files and upload them to the `/wp-content/plugins/` directory.
2. Activate the plugin through the WordPress admin under **Plugins**.
3. Configure the Azure AD settings in the Azure Configuration page under settings menu.

## Configuration

1. Register an application in the [Azure Portal](https://portal.azure.com/).
2. Obtain the following details:
   - **Client ID**
   - **Client Secret**
   - **Tenant ID**
3. Enter these details in the Azure Configuration page under settings menu.

## Requirements

- WordPress 5.5 or higher
- PHP 7.4 or higher
- Azure Active Directory account

## Contribution

Contributions are welcome! Feel free to open issues or submit pull requests to improve the plugin.

## License

This plugin is licensed under the GPLv2.

---

*Simplify your WordPress login process with secure Azure AD integration.*