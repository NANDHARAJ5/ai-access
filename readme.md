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

Basic Chat Usage
================

Once you have a `$client` instance, interacting with chat models is straightforward.

```php
// --- Choose a model appropriate for your chosen client ---
// $model = 'gpt-4o-mini';              // OpenAI
// $model = 'claude-3-5-haiku-latest';  // Claude
// $model = 'gemini-2.5-flash';         // Gemini
// $model = 'deepseek-chat';            // DeepSeek
// $model = 'grok-3-fast-latest';       // Grok (xAI)

// Assuming $client is initialized as shown in the previous section

$chat = $client->createChat($model);

// Send the message and get the response
$response = $chat->sendMessage('Write a short haiku about PHP.');

echo $response->getText() ?? 'No content generated';
```

**Switching Providers:** As shown in the "Initializing the Client" section, switching providers mainly involves changing the client instantiation line and selecting an appropriate model name for that provider. The chat interaction code itself (`createChat`, `sendMessage`, `getText`, etc.) remains largely consistent.

In addition to the generated text, it's often useful to check why the model stopped generating:

```php
use AIAccess\Chat\FinishReason;

$reason = $response->getFinishReason();

if ($reason === FinishReason::Complete) {
	echo "✅ Model completed the response successfully.\n";
} else {
	echo "⚠️ Model stopped early (reason: " . ($reason?->name ?? 'Unknown') . ")\n";
}
```

The method `$response->getUsage()` returns an instance of `AIAccess\Chat\Usage`, containing provider-specific token statistics and raw metadata:

```php
$usage = $response->getUsage();

echo "- Input tokens:  " . ($usage->inputTokens ?? 'N/A');
echo "- Output tokens: " . ($usage->outputTokens ?? 'N/A');
echo "- Reasoning:     " . ($usage->reasoningTokens ?? 'N/A');
```

 <!---->

Conversation History
--------------------

Manage multi-turn conversations easily. You can add messages manually using `addMessage()` or let `sendMessage()` handle adding the user prompt and the model's response to the history automatically.

```php
use AIAccess\Chat\Role;

// Assuming $client is initialized and $model is set

$chat = $client->createChat($model);

// Manually add messages to history
$chat->addMessage('What is the capital of France?', Role::User);
$chat->addMessage('The capital of France is Paris.', Role::Model); // Simulate a previous response
$chat->addMessage('What is a famous landmark there?', Role::User); // Add the next question

// Send request based on current history.
// Since the last message was already added, call sendMessage() without arguments.
$response = $chat->sendMessage();

echo $response->getText();

// The model's response is automatically added to the history by sendMessage().
// Full Conversation History:
$allMessages = $chat->getMessages();
foreach ($allMessages as $message) {
	echo "[" . $message->getRole()->name . "]: " . $message->getText();
}
```

 <!---->

System Instructions
-------------------

Guide the model's overall behavior or persona using a system instruction. This instruction is typically considered by the model throughout the conversation.

```php
$chat->setSystemInstruction('You are a helpful assistant that speaks like a pirate.');
```

 <!---->

Model Options
-------------

Fine-tune the model's response generation for specific requests using the `setOptions()` method on the `Chat` object. These options are provider-specific.

Here's a generic example setting the `temperature` (which controls randomness):

```php
// Set a low temperature for less random output
$chat->setOptions(temperature: 0.1);
```

**Provider-Specific Options (Examples):**

*   **OpenAI** [OpenAI API Reference](https://platform.openai.com/docs/api-reference/chat)
	*   `temperature`: Controls randomness (0-2)
	*   `maxOutputTokens`: Max tokens in the response
	*   `topP`: Nucleus sampling threshold
	*   `tools`: Define functions the model can call
	*   `metadata`: Attach custom key-value data

*   **Claude** [Anthropic API Reference](https://docs.anthropic.com/claude/reference/messages_post)
	*   `temperature`: Controls randomness (0-1)
	*   `maxTokens`: Max tokens to generate (*Note: Different name than others*)
	*   `topK`, `topP`: Alternative sampling methods
	*   `stopSequences`: Specify strings that stop generation

*   **Gemini** [Google AI Gemini API Reference](https://ai.google.dev/api/rest/v1beta/models/generateContent).
	*   `temperature`: Controls randomness (0-1)
	*   `maxOutputTokens`: Max tokens in the response
	*   `topK`, `topP`: Alternative sampling methods
	*   `stopSequences`: Specify strings that stop generation
	*   `safetySettings`: Configure content safety filters

*   **DeepSeek** [DeepSeek API Reference](https://api-docs.deepseek.com/api/create-chat-completion)
	*   `temperature`: Controls randomness (0-2, ignored by `deepseek-reasoner`)
	*   `maxOutputTokens`: Max tokens to generate (`max_tokens`)
	*   `topP`: Nucleus sampling (ignored by `deepseek-reasoner`)
	*   `frequencyPenalty`, `presencePenalty`: Control repetition (ignored by `deepseek-reasoner`)
	*   `stop`: Specify strings that stop generation
	*   `responseFormat`: Request JSON output (`['type' => 'json_object']`)
	*   `tools`: Define functions (not supported by `deepseek-reasoner`)

*   **Grok (xAI)** [xAI API Reference](https://docs.x.ai/docs/api-reference#chat-completions)
	*   `temperature`: Controls randomness (0-2)
	*   `maxOutputTokens`: Max tokens in the response (`max_completion_tokens`)
	*   `topP`: Nucleus sampling threshold
	*   `frequencyPenalty`, `presencePenalty`: Control repetition
	*   `stop`: Specify strings that stop generation
	*   `responseFormat`: Request structured output (`['type' => 'json_object']` or `json_schema`)
	*   `tools`: Define functions
	*   `reasoningEffort`: Control thinking effort for reasoning models (`low`, `high`)
	*   `seed`: Attempt deterministic output

Always refer to the specific `Chat` class implementation (`src/<Vendor>/Chat.php`) or the official vendor documentation for the most up-to-date and complete list of available options.

 <!---->

Batch Processing
================

For processing a large number of independent chat requests asynchronously, often at a lower cost, use the Batch API (supported by OpenAI and Claude). This is ideal when you don't need immediate responses, as processing can take significant time (minutes to potentially 24 hours, depending on the provider and queue load).

**Note:** Grok (xAI), DeepSeek, and Gemini do not currently support a batch API via this library.

**Concept:**
1.  Create batch using `Client::createBatch()`
2.  Create multiple `Chat` objects using `Batch::createChat()`, each configured with its own model, messages, system instructions, and options (using `addMessage`, `setSystemInstruction`, `setOptions` just like interactive chat). Assign a unique `customId` to each.
3.  `submit()` the entire `Batch` container at once. This queues the jobs for background processing. **It does not send messages interactively.**
4.  Store the returned `batchId`.
5.  **Handle Asynchronously:** Use a separate mechanism (cron job, queue worker, webhook) to check the job status later using `retrieveBatch($batchId)`.
6.  Once your checking mechanism confirms the job `status` is `Completed`, use `getMessages()` to get the results, mapped by `customId`.

**Example: Preparing and Submitting the Batch**

```php
use AIAccess\Chat\Role;

$model = '...'; // Choose a model compatible with the $client

// 1. Create a batch
$batch = $client->createBatch();

// 2. Add individual chat requests
$chat1 = $batch->addChat($model, 'request-greeting-1');
$chat1->setSystemInstruction('Be brief and friendly.');
$chat1->addMessage('Hi!', Role::User);

$chat2 = $batch->addChat($model, 'request-translate-fr');
$chat2->setSystemInstruction('Translate the user message to French.');
$chat2->addMessage('Hello world', Role::User);

$chat3 = $batch->addChat($model, 'request-code-explain');
$chat3->addMessage('Explain what this PHP code does: `echo "Hello";`', Role::User);

// 3. Submit the batch job
$batchResponse = $batch->submit(); // Returns immediately

$batchId = $batchResponse->getId();
```

Now, store the `$batchId` (e.g., in a database, queue message) associated with the task or user who initiated it.

Handling Asynchronous Completion
--------------------------------

You need a separate process (cron, queue worker, etc.) to check the status later using the stored `batchId`.

```php
use AIAccess\Batch\Status;

// --- In your separate checking script/job ---
// $batchIdToCheck = ...; // Retrieve the ID from storage
// $client = ...; // Re-initialize the appropriate client

$currentBatch = $client->retrieveBatch($batchIdToCheck);
$status = $currentBatch->getStatus();

if ($status === Status::Completed) {
	// Mark job as complete, trigger result processing
	echo "Batch $batchIdToCheck completed.\n";

} elseif ($status === Status::Failed) {
	// Mark job as failed, log error
	$errorDetails = $currentBatch->getError();
	echo "Batch $batchIdToCheck failed: " . ($errorDetails ?? 'Unknown error');

} else { // InProgress or Other
	// Job is still running, check again later based on your schedule
	echo "Batch $batchIdToCheck is still in status: " . $status->name;
}
```

Retrieve Results (After Confirmation)
-------------------------------------

Once your asynchronous checking mechanism confirms that a batch job's status is `AIAccess\Batch\Status::Completed`, you can retrieve the results. This might happen within the checking job itself or in a separate process triggered upon completion.

```php
// Assuming $currentBatch is the completed BatchResponse object

$messages = $currentBatch->getMessages(); // Returns ?array<string, AIAccess\Chat\Message>

echo "Retrieved " . count($messages) . " results:\n\n";
foreach ($messages as $customId => $message) {
	echo "Result for Request ID: '$customId' ---\n";
	echo $message->getText();
	// Process the result
}
```

**Batch API Differences & Abstraction:**

While the underlying mechanisms for batch processing differ significantly between providers, **you don't need to worry about these details when using AIAccess.** The library completely abstracts these differences away. When you call the `$batch->submit()` method:

*   If using the `AIAccess\OpenAI\Client`, the library automatically formats your chat requests into the required JSONL structure, uploads the file to OpenAI, and initiates the batch job using the returned file ID.
*   If using the `AIAccess\Claude\Client`, the library sends the prepared chat payloads directly in the batch creation request.

Thanks to this abstraction, you benefit from a **consistent and simplified workflow** for submitting batch jobs, regardless of the chosen backend provider (among those that support batch).

 <!---->

Embeddings
==========

Embeddings transform text into numerical vectors (arrays of floating-point numbers), capturing semantic meaning. These vectors allow machines to understand relationships between texts. Embeddings are fundamental for tasks like:

*   **Semantic Search:** Find documents relevant by meaning, not just keywords.
*   **Clustering:** Group similar documents together.
*   **Recommendations:** Suggest items based on content similarity.
*   **Retrieval-Augmented Generation (RAG):** Provide relevant context to language models before generating answers.

AIAccess provides a common interface (`calculateEmbeddings`) for generating these vectors using supported providers like OpenAI and Gemini.

**Note:** Claude, DeepSeek, and Grok (xAI) do not currently offer embedding endpoints through this library.

```php
// Assuming $client is initialized (must be OpenAI\Client or Gemini\Client)

// $embeddingModel = 'text-embedding-3-small'; // OpenAI Example
$embeddingModel = 'embedding-001'; // Gemini Example

$textsToEmbed = [
	'The quick brown fox jumps over the lazy dog.',
	'PHP is a popular general-purpose scripting language.',
	'Paris is the capital of France.',
];

// Calculating embeddings

$results = $client->calculateEmbeddings(
	model: $embeddingModel,
	input: $textsToEmbed,
	// Provider-specific options go here as named arguments
);
```

The `calculateEmbeddings()` method returns an array of `AIAccess\Embedding\Vector` objects, one for each input text. Each `Vector` object contains the numerical vector representing the text's semantic meaning. You can then iterate through these results to use the vectors, for example, to calculate similarities or store them for later use.

```php
// Assuming $results is the array returned from calculateEmbeddings

foreach ($results as $index => $vector) {
	// Example: Calculate similarity with the first embedding
	if ($index > 0) {
		$similarity = $results[0]->cosineSimilarity($vector);
		echo "Cosine Similarity with first text: " . number_format($similarity, 4);
	}
}
```

You can serialize embeddings for efficient storage:

```php
use AIAccess\Embedding\Vector;

$binaryData = $results[0]->serialize();
// Store $binaryData in a database (e.g., BLOB column)

// Later, retrieve and deserialize:
$vector = Vector::deserialize($retrievedBinaryData);
```

**Embedding API Options:**

Pass these as additional named arguments to `calculateEmbeddings` when using the specific client:

*   **OpenAI** [OpenAI Embeddings API Reference](https://platform.openai.com/docs/api-reference/embeddings/create)
	*   `dimensions` (int): Optional. Request specific vector size (e.g., 256) for `text-embedding-3-*` models.

*   **Gemini** [Google AI Gemini API Reference (batchEmbedContents)](https://ai.google.dev/api/rest/v1beta/models/batchEmbedContents)
	*   `taskType` (string): Optional. Hint for use case (e.g., `RETRIEVAL_QUERY`, `RETRIEVAL_DOCUMENT`).
	*   `title` (string): Optional. Title when `taskType` is `RETRIEVAL_DOCUMENT`.
	*   `outputDimensionality` (int): Optional. Request specific dimensions.

 <!---->

Error Handling
==============

AIAccess uses a clear exception hierarchy designed around practical error handling needs:

```
ServiceException                (Base for all service-related errors)
├── ApiException                (API returned an explicit error)
├── CommunicationException      (Cannot communicate with API or parse response)
└── UnexpectedResponseException (Response has unexpected structure)
LogicException                  (Programming errors - invalid parameters)
```

- **`ApiException`**: The provider API returned an explicit error response
  - API communication succeeded, but the server returned an error code/message
  - Examples: invalid API key, rate limits, content policy violations, invalid parameters
  - Check `$e->getCode()` for the HTTP status code

- **`CommunicationException`**: Failed to communicate with the API or parse the response
  - Network issues (DNS failures, timeouts, connection resets)
  - Invalid JSON responses that cannot be parsed
  - *Retrying the request may resolve these issues*

- **`UnexpectedResponseException`**: API returned data with an unexpected structure
  - Response was received and parsed but doesn't match the expected schema
  - *Retrying probably won't help - indicates API changes or library issues*

- **`LogicException`**: Indicates programming errors in your usage of the library
  - Invalid parameters, calling methods in wrong order, etc.
  - Should be caught and fixed during development, not in production

Most applications should handle exceptions based on recovery strategy:

```php
try {
	$response = $chat->sendMessage('Write a creative story.');
	echo $response->getText();

} catch (AIAccess\ApiException $e) {
	// API explicitly returned an error
	echo "The AI service returned an error: " . $e->getMessage();

	// You can check the status code for specific handling
	if ($e->getCode() === 429) {
		echo " Please try again later (rate limit reached).";
	}

} catch (AIAccess\CommunicationException $e) {
	// Connection problems or invalid responses - can retry
	echo "Temporarily unable to reach the AI service. Please try again.";
	// Consider automatic retry logic here

} catch (AIAccess\UnexpectedResponseException $e) {
	// Unexpected response structure - log for investigation
	echo "The service response was unexpected. Support has been notified.";
	// Log the error for developers to investigate

} catch (AIAccess\ServiceException $e) {
	// Fallback for any other service-related errors
	echo "An error occurred with the AI service: " . $e->getMessage();
}
```

For simpler applications, you can handle all service errors together:

```php
try {
	$response = $chat->sendMessage('Generate ideas for blog posts.');
	echo $response->getText();

} catch (AIAccess\ServiceException $e) {
	// Handle all service errors in one place
	echo "Error communicating with AI: " . $e->getMessage();

	// Log error details for troubleshooting
	error_log(get_class($e) . ": " . $e->getMessage());
}
```

`LogicException` indicates programming errors and typically shouldn't be caught in production code, as they should be fixed during development.

 <!---->

[Support Me](https://github.com/sponsors/dg)
============

Do you like AI Access? Are you looking forward to new features?

[![Buy me a coffee](https://files.nette.org/icons/donation-3.svg)](https://github.com/sponsors/dg)

Thank you!
