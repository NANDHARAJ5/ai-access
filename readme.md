![AI Access for PHP](https://github.com/user-attachments/assets/f9b6702d-6d6b-49fd-96ff-a33c53e26c68)

[![Downloads this Month](https://img.shields.io/packagist/dm/ai-access/ai-access.svg)](https://packagist.org/packages/ai-access/ai-access)
[![Tests](https://github.com/aiaccess/ai-access/workflows/Tests/badge.svg?branch=master)](https://github.com/aiaccess/ai-access/actions)
[![Coverage Status](https://coveralls.io/repos/github/aiaccess/ai-access/badge.svg?branch=master)](https://coveralls.io/github/aiaccess/ai-access?branch=master)
[![Latest Stable Version](https://poser.pugx.org/aiaccess/ai-access/v/stable)](https://github.com/aiaccess/ai-access/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/aiaccess/ai-access/blob/master/license.md)


 <!---->

Unified PHP library providing access to various AI models from different providers through a **single, unified PHP interface**.

<h3>

✅ **Consistent API:** Write once, use everywhere<br>
✅ **Easy Switching:** Change providers with one line of code<br>
✅ **Simplified Workflow:** Focus on your app, not vendor SDKs<br>
✅ **Modern PHP:** Built with strict types and PHP 8.1+

</h3>

 <!---->

Supported Providers
---

<h3>

<img src="https://github.com/user-attachments/assets/fad9ee33-e861-42c6-beb7-f0160deda45c" width="35" valign="middle"> &nbsp; **OpenAI ChatGPT** <br>

<img src="https://github.com/user-attachments/assets/cc815e2d-0bb9-4c8e-984f-95d1b33570b9" width="35" valign="middle"> &nbsp; **Anthropic Claude** <br>

<img src="https://github.com/user-attachments/assets/0ed4b173-1aec-4c1b-abb7-cf7abfc0ea21" width="35" valign="middle"> &nbsp; **Google Gemini** <br>

<img src="https://github.com/user-attachments/assets/c2f76da8-3cfc-4645-8c82-dccd4fbfad98" width="35" valign="middle"> &nbsp; **DeepSeek** <br>

<img src="https://github.com/user-attachments/assets/98d2d4d2-5df2-4ebb-ae50-b62f345d6bcf" width="35" valign="middle"> &nbsp; **Grok (xAI)**

</h3>

 <!---->

Installation
============

Download and install the library using Composer:

```shell
composer require ai-access/ai-access
```

AIAccess requires PHP 8.1 or later.

 <!---->

Initializing the Client
=======================

To start interacting with an AI provider, you first need to create a client instance. The specific class depends on the provider, but the core interface remains consistent.

Get your API keys from the respective providers:

*   **OpenAI:** [OpenAI Platform API Keys](https://platform.openai.com/api-keys)
*   **Anthropic Claude:** [Anthropic Console API Keys](https://console.anthropic.com/settings/keys)
*   **Google Gemini:** [Google AI Studio API Keys](https://aistudio.google.com/app/apikey)
*   **DeepSeek:** [DeepSeek Platform API Keys](https://platform.deepseek.com/api_keys)
*   **Grok (xAI):** [xAI API Console API Keys](https://console.x.ai/team/default/api-keys)

```php
$apiKey = trim(file_get_contents('path/to/your/key.txt'));

// OpenAI Client
$client = new AIAccess\Provider\OpenAI\Client($apiKey);

// Claude Client
$client = new AIAccess\Provider\Claude\Client($apiKey);

// Gemini Client
$client = new AIAccess\Provider\Gemini\Client($apiKey);

// DeepSeek Client
$client = new AIAccess\Provider\DeepSeek\Client($apiKey);

// Grok (xAI) Client
$client = new AIAccess\Provider\Grok\Client($apiKey);
```

In larger applications, you would typically configure and retrieve the client instance from a [Dependency Injection container](https://doc.nette.org/en/dependency-injection) instead of creating it directly in your application code.

Now you can use the `$client` variable to interact with the chosen provider's API.

**Key Points:**

*   Choose the correct client class (`OpenAI\Client`, `Claude\Client`, `Gemini\Client`, `DeepSeek\Client`, `Grok\Client`).
*   Provide the corresponding API key during instantiation.
*   The `$client` object now provides access to the provider's features through a unified interface where possible.

All subsequent examples will assume you have a `$client` variable initialized corresponding to your desired provider.

 <!---->

[Support Me](https://github.com/sponsors/dg)
============

Do you like AI Access? Are you looking forward to new features?

[![Buy me a coffee](https://files.nette.org/icons/donation-3.svg)](https://github.com/sponsors/dg)

Thank you!
