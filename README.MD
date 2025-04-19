# WordPress Login with Microsoft Entra ID

A lightweight WordPress plugin that enables secure Single Sign-On (SSO) with Microsoft Entra ID. Users can log in to your WordPress site using their Microsoft Entra ID credentials for seamless and secure authentication.

## Features

- Microsoft Entra ID Single Sign-On (SSO)
- Keep your credentials securely in ENV
- Option to disable password login
- Easy configuration through WordPress admin
- Secure and scalable for enterprise use

## Installation

1. Download the plugin files and upload them to the `/wp-content/plugins/` directory.
2. Activate the plugin through the WordPress admin under **Plugins**.
3. Configure the App registration credentials in the Microsoft Entra ID configuration page under settings menu.

## Configuration

1. Register an application in the [Azure Portal](https://portal.azure.com/).
2. Obtain the following details:
   - **Client ID**
   - **Client Secret**
   - **Tenant ID**
3. Enter these details in the Azure Configuration page under settings menu.


## Contribution

Contributions are welcome! Feel free to open issues or submit pull requests to improve the plugin.

## License

This plugin is licensed under the GPLv2.

---

*Simplify your WordPress login process with secure Microsoft Entra ID integration.*