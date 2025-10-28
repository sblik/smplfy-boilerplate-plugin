# smplfy-boilerplate-plugin

SMPLFY Core Plugin
Core abstraction layer for Gravity Forms business automation solutions.
This plugin provides reusable base classes, field mapping utilities, Datadog logging, and Gravity Flow integration for building maintainable, readable business automation plugins on top of Gravity Forms.

🎯 Purpose
When building complex Gravity Forms solutions, you typically work with numeric form and field IDs throughout your code:
php// Hard to read and maintain
$email = rgar($entry, '2');
$first_name = rgar($entry, '1.3');
GFAPI::update_entry_property($entry_id, '2', $new_email);
This plugin transforms that into:
php// Self-documenting and maintainable
$entity = $repository->get_one(2, $entry_id);
$email = $entity->email;
$first_name = $entity->nameFirst;
$entity->email = $new_email;
$repository->update($entity);
```

**This plugin eliminates:**
- Form/field ID hardcoding throughout your codebase
- Guesswork when troubleshooting ("What is field 2? What is field 1.3?")
- Scattered logging across multiple plugins
- Repetitive CRUD code for every form

---

## 🏗️ Architecture

This plugin uses the **Repository and Entity patterns** alongside the **Use Case pattern** to provide:

- **Form Entities**: Object representations of form entries with named properties via magic `@property` declarations
- **Form Repositories**: CRUD operations and entry management for each form
- **Use Cases**: Business logic containers triggered by form submissions, workflow steps, or other actions
- **Property Mapping System**: Convert GF numeric field IDs to human-readable property names
- **Datadog Integration**: Centralized logging for all operations
- **Gravity Flow Integration**: Programmatic workflow step transitions
- **WordPress Heartbeat Integration**: Real-time data updates without page refreshes
- **WordPress Integration**: User role and meta management
- **Security Utilities**: Prevent direct script execution
- **Require Utilities**: Recursive file loading for organized code structure

---

## 📋 Requirements

### Required
- **WordPress**: 6.0+
- **PHP**: 7.3+
- **Gravity Forms**: 2.8.4+
- **Gravity Flow**: 2.x+ (for workflow features)

### Optional
- Datadog account (for logging features)
- WP-CLI (recommended for debugging)

---

## 🚀 Installation

1. Install this plugin in `/wp-content/plugins/smplfy-core-plugin/`
2. Activate via WordPress Admin → Plugins
3. Configure Datadog (optional):
   - Navigate to WordPress Admin → SMPLFY Settings
   - Toggle "Send logs to Datadog"
   - Enter your Datadog API URL
   - Enter your Datadog API Key

**Note**: This plugin provides no functionality on its own. It requires a companion client-specific plugin that extends its base classes.

---

## 🔧 Usage with Client Plugins

Each client site requires a companion plugin built on this core. See the [SMPLFY Boilerplate Plugin](https://github.com/sblik/smplfy-boilerplate-plugin) for a complete starting point.

### Typical Client Plugin Structure
```
client-plugin/
├── public/
│   └── php/
│       ├── entities/          # Extend SMPLFY_BaseEntity for each form
│       ├── repositories/      # Extend SMPLFY_BaseRepository for each form
│       ├── usecases/         # Business logic for actions/workflows
│       ├── adapters/         # Hook use cases to GF/WP/GFlow events
│       └── types/            # Constants for form IDs, field IDs, step IDs

📝 Creating Form Entities and Repositories
Step 1: Define Form ID Constants
Create a constants file to organize form IDs:
php<?php
namespace SMPLFY\ClientName;

class FormIds {
    const CONTACT_FORM_ID = 5;
    const ORDER_FORM_ID = 12;
    const REGISTRATION_FORM_ID = 18;
}
Step 2: Create a Form Entity
Entities represent a single form entry with strongly-typed properties.
php<?php
namespace SMPLFY\ClientName;

use SmplfyCore\SMPLFY_BaseEntity;

/**
 * Contact Form Entity
 * 
 * @property string $nameFirst
 * @property string $nameLast
 * @property string $email
 * @property string $phone
 * @property string $company
 * @property string $message
 */
class ContactFormEntity extends SMPLFY_BaseEntity {
    
    public function __construct($formEntry = array()) {
        parent::__construct($formEntry);
        $this->formId = FormIds::CONTACT_FORM_ID;
    }
    
    /**
     * Map entity property names to Gravity Forms field IDs
     * 
     * Field IDs come from Gravity Forms admin.
     * Use sub-field notation for name fields (e.g., '1.3' for first name)
     */
    protected function get_property_map(): array {
        return array(
            'nameFirst' => '1.3',  // Name (First) field
            'nameLast'  => '1.6',  // Name (Last) field
            'email'     => '2',    // Email field
            'phone'     => '3',    // Phone field
            'company'   => '4',    // Single line text field
            'message'   => '5',    // Paragraph text field
        );
    }
}
Key Points:

@property PHPDoc declarations enable IDE autocomplete
Property names are camelCase for readability
get_property_map() maps properties to GF field IDs
Field IDs are strings (including sub-fields like '1.3')
The parent constructor handles property initialization from GF entry data

Step 3: Create a Form Repository
Repositories handle CRUD operations for a specific form.
php<?php
namespace SMPLFY\ClientName;

use SmplfyCore\SMPLFY_BaseRepository;
use SmplfyCore\SMPLFY_GravityFormsApiWrapper;
use WP_Error;

/**
 * Contact Form Repository
 * 
 * PHPDoc annotations enable IDE autocomplete for typed returns
 * 
 * @method static ContactFormEntity|null get_one($fieldId, $value)
 * @method static ContactFormEntity|null get_one_for_current_user()
 * @method static ContactFormEntity|null get_one_for_user($userId)
 * @method static ContactFormEntity[] get_all($fieldId = null, $value = null, string $direction = 'ASC')
 * @method static int|WP_Error add(ContactFormEntity $entity)
 */
class ContactFormRepository extends SMPLFY_BaseRepository {
    
    public function __construct(SMPLFY_GravityFormsApiWrapper $gravityFormsApi) {
        $this->entityType = ContactFormEntity::class;
        $this->formId     = FormIds::CONTACT_FORM_ID;
        parent::__construct($gravityFormsApi);
    }
}
Key Points:

Set $this->entityType to your entity class
Set $this->formId to the corresponding form ID constant
PHPDoc @method annotations provide IDE autocomplete with proper typing
All base repository methods automatically return your specific entity type


🔌 Core Repository Methods
The SMPLFY_BaseRepository provides these methods (automatically typed to your entity):
Retrieve Entries
php// Get single entry by field value
$entity = $repository->get_one(2, 'john@example.com'); // Field ID 2 = email
$entity = $repository->get_one('email', 'john@example.com'); // Also works with property names

// Get entry for current logged-in user (searches created_by field)
$entity = $repository->get_one_for_current_user();

// Get entry for specific user ID
$entity = $repository->get_one_for_user($user_id);

// Get all entries (optionally filtered)
$all_entities = $repository->get_all(); // All entries
$filtered = $repository->get_all(2, 'john@example.com'); // Filtered by field
$sorted = $repository->get_all(null, null, 'DESC'); // All, sorted descending
Create Entries
php// Create new entity
$entity = new ContactFormEntity();
$entity->nameFirst = 'John';
$entity->nameLast = 'Doe';
$entity->email = 'john@example.com';
$entity->phone = '555-0123';

// Save to Gravity Forms
$entry_id = $repository->add($entity);

if (is_wp_error($entry_id)) {
    // Handle error
    SMPLFY_Log::error('Failed to create entry', [
        'error' => $entry_id->get_error_message()
    ]);
} else {
    // Success - $entry_id is the new GF entry ID
    SMPLFY_Log::info('Entry created', ['entry_id' => $entry_id]);
}
Update Entries
php// Load existing entry
$entity = $repository->get_one(2, 'john@example.com');

// Modify properties
$entity->phone = '555-9999';
$entity->company = 'Acme Corp';

// Save changes
$result = $repository->update($entity);

if (is_wp_error($result)) {
    SMPLFY_Log::error('Failed to update entry', [
        'error' => $result->get_error_message()
    ]);
}
Delete Entries
php$entity = $repository->get_one(2, 'john@example.com');
$result = $repository->delete($entity);

🎭 Using Entities
Entities provide clean, readable access to form data:
php// Access properties directly (magic properties via __get)
echo $entity->nameFirst;  // Returns value of field '1.3'
echo $entity->email;      // Returns value of field '2'

// Set properties directly (magic properties via __set)
$entity->nameFirst = 'Jane';
$entity->email = 'jane@example.com';

// Get the Gravity Forms entry ID
$entry_id = $entity->get_entry_id();

// Get the full GF entry array (if needed for advanced use)
$gf_entry = $entity->get_entry();

// Access the formEntry property directly (needed for WorkflowStep)
$entity->formEntry; // The raw GF entry array

// Convert entity to array
$data = $entity->to_array();
Property Access Pattern:

Entities use PHP magic methods (__get, __set)
Property names are mapped via get_property_map()
IDE autocomplete works via @property PHPDoc declarations
You never work with numeric field IDs in your business logic


🎯 Use Case Pattern
The boilerplate uses a Use Case pattern to organize business logic. A use case represents a specific action or workflow triggered by user interaction.
What is a Use Case?
A use case:

Represents a specific human action or its result (e.g., "Submit Contact Form", "Approve Application")
Contains the business logic that should execute as a result
Is triggered by hooks (Gravity Forms, WordPress, Gravity Flow)
Coordinates between repositories, entities, and external services
Keeps business logic separate from WordPress/GF hooks

Creating a Use Case
php<?php
namespace SMPLFY\ClientName;

use SmplfyCore\SMPLFY_Log;
use SmplfyCore\WorkflowStep;

class ContactFormSubmissionUsecase {
    
    private ContactFormRepository $contactRepository;
    private CustomerRepository $customerRepository;
    
    public function __construct(
        ContactFormRepository $contactRepository,
        CustomerRepository $customerRepository
    ) {
        $this->contactRepository = $contactRepository;
        $this->customerRepository = $customerRepository;
    }
    
    /**
     * Executes when contact form is submitted
     * 
     * @param array $entry The Gravity Forms entry array
     */
    public function handle_submission($entry) {
        // Create entity from submitted entry
        $contactEntity = new ContactFormEntity($entry);
        
        // Log to Datadog
        SMPLFY_Log::info("Contact form submitted", [
            'email' => $contactEntity->email,
            'name' => $contactEntity->nameFirst . ' ' . $contactEntity->nameLast,
            'entry_id' => $entry['id']
        ]);
        
        // Business logic: Check if customer exists
        $existingCustomer = $this->customerRepository->get_one('email', $contactEntity->email);
        
        if ($existingCustomer) {
            // Update existing customer
            $existingCustomer->lastContactDate = current_time('mysql');
            $this->customerRepository->update($existingCustomer);
            
            SMPLFY_Log::info("Updated existing customer", [
                'customer_id' => $existingCustomer->get_entry_id()
            ]);
        } else {
            // Create new customer record
            $newCustomer = new CustomerEntity();
            $newCustomer->email = $contactEntity->email;
            $newCustomer->firstName = $contactEntity->nameFirst;
            $newCustomer->lastName = $contactEntity->nameLast;
            
            $customer_id = $this->customerRepository->add($newCustomer);
            
            SMPLFY_Log::info("Created new customer", [
                'customer_id' => $customer_id
            ]);
        }
        
        // Move to workflow step (if using Gravity Flow)
        WorkflowStep::send('10', $contactEntity->formEntry);
    }
}
Hooking Use Cases to Gravity Forms
Use cases are connected to Gravity Forms actions via an Adapter pattern. This keeps your business logic separate from WordPress/GF hooks.
php<?php
namespace SMPLFY\ClientName;

class GravityFormsAdapter {
    
    private ContactFormSubmissionUsecase $contactSubmissionUsecase;
    private ApplicationApprovalUsecase $applicationApprovalUsecase;
    
    public function __construct(
        ContactFormSubmissionUsecase $contactSubmissionUsecase,
        ApplicationApprovalUsecase $applicationApprovalUsecase
    ) {
        $this->contactSubmissionUsecase = $contactSubmissionUsecase;
        $this->applicationApprovalUsecase = $applicationApprovalUsecase;
    }
    
    /**
     * Register all Gravity Forms hooks
     */
    public function register_hooks() {
        // After form submission
        add_action(
            'gform_after_submission_' . FormIds::CONTACT_FORM_ID,
            [$this->contactSubmissionUsecase, 'handle_submission'],
            10,
            2
        );
        
        // After form submission for application form
        add_action(
            'gform_after_submission_' . FormIds::APPLICATION_FORM_ID,
            [$this->applicationApprovalUsecase, 'handle_submission'],
            10,
            2
        );
        
        // Before form submission (for validation)
        add_filter(
            'gform_validation_' . FormIds::CONTACT_FORM_ID,
            [$this, 'validate_contact_form']
        );
    }
    
    public function validate_contact_form($validation_result) {
        // Custom validation logic
        return $validation_result;
    }
}
Common Use Case Patterns
Form Submission Use Case:
phppublic function handle_form_submission($entry) {
    $entity = new FormEntity($entry);
    
    // 1. Log the submission
    SMPLFY_Log::info("Form submitted", ['entry_id' => $entry['id']]);
    
    // 2. Process business logic
    $this->process_order($entity);
    
    // 3. Update workflow step
    WorkflowStep::send(WorkflowStepIds::PROCESSING, $entity->formEntry);
    
    // 4. Send to external system (if needed)
    $this->send_to_external_api($entity);
}
Workflow Step Completion Use Case:
phppublic function handle_approval_complete($entry, $step_id) {
    $entity = $this->repository->get_one('id', $entry['id']);
    
    // Update entity based on approval
    $entity->status = 'Approved';
    $entity->approvedDate = current_time('mysql');
    $this->repository->update($entity);
    
    // Log the approval
    SMPLFY_Log::info("Application approved", [
        'entry_id' => $entry['id'],
        'approver' => wp_get_current_user()->user_email
    ]);
    
    // Move to next step
    WorkflowStep::send(WorkflowStepIds::PROCESSING, $entity->formEntry);
}
Data Synchronization Use Case:
phppublic function sync_with_crm($entry) {
    $entity = new ContactFormEntity($entry);
    
    try {
        $response = wp_remote_post('https://crm.example.com/api/contacts', [
            'body' => json_encode([
                'first_name' => $entity->nameFirst,
                'last_name' => $entity->nameLast,
                'email' => $entity->email,
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . get_option('crm_api_key')
            ]
        ]);
        
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }
        
        SMPLFY_Log::info("Synced to CRM", ['entry_id' => $entry['id']]);
        
    } catch (\Exception $e) {
        SMPLFY_Log::error("CRM sync failed", [
            'entry_id' => $entry['id'],
            'error' => $e->getMessage()
        ]);
    }
}

🔄 Gravity Flow Integration
The core plugin provides the WorkflowStep class for programmatic workflow step transitions.
Moving Entries Between Steps
phpuse SmplfyCore\WorkflowStep;

// Move an entry to a specific workflow step
WorkflowStep::send('10', $entity->formEntry);
// Where '10' is the Gravity Flow step ID
// Note: Must use $entity->formEntry (the raw GF entry array)
Complete Example
phppublic function approve_application($entry) {
    $entity = $this->applicationRepository->get_one('id', $entry['id']);
    
    // Update entry data
    $entity->status = 'Approved';
    $entity->approvalDate = current_time('mysql');
    $this->applicationRepository->update($entity);
    
    // Log the approval
    SMPLFY_Log::info("Application approved", [
        'entry_id' => $entity->get_entry_id(),
        'approver' => wp_get_current_user()->display_name
    ]);
    
    // Move to approval step
    WorkflowStep::send(WorkflowStepIds::APPROVED, $entity->formEntry);
}
Finding Step IDs

Go to Forms → [Your Form] → Workflow
Each step shows its ID in the step settings
Document step IDs in your constants file for clarity:

php<?php
namespace SMPLFY\ClientName;

class WorkflowStepIds {
    const PENDING_REVIEW = '10';
    const APPROVED = '15';
    const REJECTED = '20';
    const PROCESSING = '25';
    const COMPLETE = '30';
}

// Then use:
WorkflowStep::send(WorkflowStepIds::APPROVED, $entity->formEntry);

🔄 WordPress Heartbeat API Integration
The plugin can integrate with WordPress's Heartbeat API for real-time data updates without page refreshes.
What is the Heartbeat API?
WordPress Heartbeat API allows your plugin to:

Poll the server at regular intervals (default: 15 seconds)
Send data from client-side JavaScript to server-side PHP
Receive updated data from the server without reloading the page
Update UI elements in real-time (e.g., showing live form entry data)

Server-Side Heartbeat Handler
Create a heartbeat handler class to process incoming requests:
php<?php
namespace SMPLFY\ClientName;

class ContactFormHeartbeat {
    
    private ContactFormRepository $contactRepository;
    
    public function __construct(ContactFormRepository $contactRepository) {
        $this->contactRepository = $contactRepository;
    }
    
    /**
     * Process heartbeat data and return response
     * 
     * @param array $response Data to send back to client
     * @param array $data Data received from client
     * @return array Modified response
     */
    public function receive_heartbeat(array $response, array $data) {
        // Check if our custom data was sent
        if (empty($data['contact_form_data'])) {
            return $response;
        }
        
        // Extract client data
        $clientData = $data['contact_form_data'];
        $userId = $clientData['userId'];
        
        // Fetch entity for this user
        $contactEntity = $this->contactRepository->get_one_for_user($userId);
        
        if (!empty($contactEntity)) {
            // Send entity data back to client
            $response['entity_exists'] = true;
            $response['contact_data'] = [
                'first_name' => $contactEntity->nameFirst,
                'last_name' => $contactEntity->nameLast,
                'email' => $contactEntity->email,
                'phone' => $contactEntity->phone,
                'entry_id' => $contactEntity->get_entry_id()
            ];
        } else {
            $response['entity_exists'] = false;
        }
        
        return $response;
    }
}
Registering the Heartbeat Hook
In your WordPress adapter:
php<?php
namespace SMPLFY\ClientName;

class WordPressAdapter {
    
    private ContactFormHeartbeat $heartbeatHandler;
    
    public function __construct(ContactFormHeartbeat $heartbeatHandler) {
        $this->heartbeatHandler = $heartbeatHandler;
    }
    
    public function register_hooks() {
        // Register heartbeat handler
        add_filter(
            'heartbeat_received',
            [$this->heartbeatHandler, 'receive_heartbeat'],
            10,
            2
        );
    }
}
Client-Side JavaScript
Send data to the server and receive updates:
javascriptjQuery(document).ready(function($) {
    
    // Send data with heartbeat
    $(document).on('heartbeat-send', function(event, data) {
        // Add your custom data to the heartbeat
        data.contact_form_data = {
            userId: wpData.currentUserId, // From wp_localize_script
            timestamp: Date.now()
        };
    });
    
    // Receive data from heartbeat
    $(document).on('heartbeat-tick', function(event, data) {
        // Check if we received our custom data
        if (data.entity_exists) {
            // Update UI with entity data
            $('#user-first-name').text(data.contact_data.first_name);
            $('#user-last-name').text(data.contact_data.last_name);
            $('#user-email').text(data.contact_data.email);
            $('#entry-status').removeClass('hidden').addClass('active');
        } else {
            // No entity found
            $('#entry-status').removeClass('active').addClass('hidden');
        }
    });
    
});
Enqueuing JavaScript
phppublic function enqueue_scripts() {
    wp_enqueue_script('heartbeat'); // WordPress Heartbeat API
    
    wp_enqueue_script(
        'contact-form-heartbeat',
        plugin_dir_url(__FILE__) . 'js/contact-form-heartbeat.js',
        ['jquery', 'heartbeat'],
        '1.0.0',
        true
    );
    
    // Pass data to JavaScript
    wp_localize_script('contact-form-heartbeat', 'wpData', [
        'currentUserId' => get_current_user_id(),
        'ajaxUrl' => admin_url('admin-ajax.php')
    ]);
}

📊 Datadog Logging
When enabled, the plugin automatically logs all operations to Datadog using the SMPLFY_Log class.
Configuration

Navigate to WordPress Admin → SMPLFY Settings
Check "Send logs to Datadog"
Enter your Datadog API URL (e.g., https://http-intake.logs.datadoghq.com/v1/input)
Enter your Datadog API Key

Using the Logger
phpuse SmplfyCore\SMPLFY_Log;

// Info level logging
SMPLFY_Log::info("Contact form submitted", [
    'entry_id' => $entry['id'],
    'user_email' => $entity->email
]);

// Error level logging
SMPLFY_Log::error("Failed to sync with CRM", [
    'entry_id' => $entry['id'],
    'error' => $exception->getMessage(),
    'trace' => $exception->getTraceAsString()
]);

// Warning level logging
SMPLFY_Log::warning("Duplicate email detected", [
    'email' => $entity->email,
    'existing_entry_id' => $existing->get_entry_id()
]);

// Debug level logging
SMPLFY_Log::debug("Processing workflow step", [
    'step_id' => $step_id,
    'entry_id' => $entry['id'],
    'current_step' => $current_step
]);
What Gets Logged Automatically
When Datadog is enabled, the following are logged automatically:

Form submissions
Entry creates, updates, deletes
Workflow step transitions
Errors and exceptions from repositories
Custom logs you add via SMPLFY_Log

Log Context
All logs include:

Timestamp
Log level (info, warning, error, debug)
Entry ID (when available)
Form ID (when available)
User ID (when available)
Custom data you provide


📦 Require Utilities
The core plugin provides a utility function for recursively loading PHP files from directories:
php// In your main plugin file
require_utilities(__DIR__ . '/public/php/entities');
require_utilities(__DIR__ . '/public/php/repositories');
require_utilities(__DIR__ . '/public/php/usecases');
require_utilities(__DIR__ . '/public/php/adapters');
require_utilities(__DIR__ . '/public/php/types');
This eliminates the need for manual require_once statements for each class file. The function automatically includes all .php files in the specified directory and its subdirectories.
Benefits:

No need to manually maintain include statements
Easy to organize code into logical directories
Automatically picks up new files

Note: Files are loaded in alphabetical order. If you have dependencies between classes, ensure proper autoloading or manual includes where needed.

🔐 Security Features
All core plugin files include direct access prevention:
phpif (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
This prevents files from being executed outside the WordPress context.
Your client plugins should include this in every PHP file.

🚀 Development Workflow
Setting Up a New Client Site

Clone the SMPLFY Boilerplate Plugin
Rename plugin folder and main file for the client
Update namespace throughout: SMPLFY\boilerplate → SMPLFY\ClientName
Update FormIds.php with actual form IDs from Gravity Forms admin
Create Entity classes for each form (extend SMPLFY_BaseEntity)
Create Repository classes for each form (extend SMPLFY_BaseRepository)
Map field IDs in each entity's get_property_map() method
Create Use Cases for business logic
Create Adapters to hook Use Cases into GF/WP/GFlow events
Test locally before deploying to client site

Finding Form and Field IDs
Form IDs:

Go to Forms in WordPress admin
Hover over a form name
Look at the URL: ?id=5 means Form ID is 5

Field IDs:

Open the form in the form editor
Click on a field
Look at the Field Settings sidebar
The Field ID is shown at the top (e.g., 2)
For name fields, sub-field IDs are shown:

First Name = 1.3
Last Name = 1.6


For address fields:

Street Address = 3.1
City = 3.3
State = 3.4
ZIP = 3.5



Pro Tip: Keep a mapping document for each client site:
Form NameForm IDEntity ClassNotesContact Form5ContactFormEntityMain lead captureApplication Form12ApplicationFormEntityHas workflowOrder Form18OrderFormEntitySyncs with CRM
Field LabelField IDEntity PropertyFormFirst Name1.3nameFirstContact (5)Last Name1.6nameLastContact (5)Email2emailContact (5)

🐛 Debugging Tips
Entry Not Updating

Check Datadog logs for error messages
Verify field IDs in get_property_map() match Gravity Forms admin
Ensure repository save is called: $repository->update($entity)
Check GF entry in admin to see if changes persisted
Enable WordPress debugging in wp-config.php:

php   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
Property Not Working

Verify property is in PHPDoc @property annotation
Verify property is mapped in get_property_map()
Check field ID is a string (e.g., '2' not 2)
For name fields, ensure using sub-field ID (e.g., '1.3')
Check for typos in property names (case-sensitive)

Field Mapping Confusion
php// WRONG - numeric value
'email' => 2,

// CORRECT - string value
'email' => '2',

// CORRECT - sub-field notation for name fields
'nameFirst' => '1.3',
'nameLast' => '1.6',

// CORRECT - sub-field notation for address fields
'streetAddress' => '3.1',
'city' => '3.3',
Workflow Step Not Transitioning

Verify step ID is correct in Gravity Flow admin
Ensure using $entity->formEntry not just $entity:

php   // WRONG
   WorkflowStep::send('10', $entity);
   
   // CORRECT
   WorkflowStep::send('10', $entity->formEntry);

Check Datadog logs for workflow errors
Verify entry is in workflow (has workflow_final_status meta)

Repository Returns Null

Check filter value matches exactly (case-
RetrySContinuesensitive, whitespace matters)
2. Verify entry exists in Gravity Forms admin with that field value
3. Check form ID is correct in repository constructor
4. Ensure field ID exists in the form
5. Try using numeric field ID instead of property name in get_one():
php   // Try with field ID directly
   $entity = $repository->get_one(2, 'john@example.com');
Use Case Not Triggering

Verify hook is registered in adapter's register_hooks() method
Check form ID matches the hook suffix:

php   // If FormIds::CONTACT_FORM_ID = 5
   add_action('gform_after_submission_5', ...);

Check adapter is instantiated and hooks are registered in main plugin file
Enable GF logging: Forms → Settings → Logging → Enable

Using WP-CLI for Debugging
bash# View all forms
wp gf form list

# View form details including field IDs
wp gf form get 5

# View a specific entry
wp gf entry get 123

# View all entries for a form
wp gf entry list 5

# Search entries
wp gf entry list 5 --field-id=2 --field-value=john@example.com

# Update an entry field
wp gf entry update 123 2 "newemail@example.com"

# Delete an entry
wp gf entry delete 123

📚 Best Practices
Naming Conventions
Entity Properties:

Use camelCase: nameFirst, emailAddress, phoneNumber
Use descriptive names: customerEmail not just email if ambiguous
Match common PHP conventions for consistency
Keep names concise but clear: approvalDate not dateWhenApplicationWasApproved

Classes:

Use PascalCase: ContactFormEntity, ApplicationRepository
Include form/purpose in name: ContactFormEntity not just ContactEntity
Use consistent suffixes: Entity, Repository, Usecase, Adapter

Constants:

Use SCREAMING_SNAKE_CASE: CONTACT_FORM_ID, ORDER_FORM_ID
Group related constants in classes: FormIds, FieldIds, WorkflowStepIds
Use descriptive names: PENDING_APPROVAL_STEP not just STEP_1

Namespaces:

Use client name: SMPLFY\ClientName
Organize by feature: SMPLFY\ClientName\Webhooks, SMPLFY\ClientName\Usecases
Keep consistent structure across client plugins

Repository Patterns
Do:
php// Type-hint your specific entity
public function send_to_crm(ContactFormEntity $entity) {
    $data = [
        'first_name' => $entity->nameFirst,
        'last_name' => $entity->nameLast,
        'email' => $entity->email
    ];
    // Business logic
}
Don't:
php// Avoid working with raw arrays in business logic
public function send_to_crm(array $entry) {
    $email = rgar($entry, '2'); // Hard to maintain
    // Business logic
}
Entity Design
Keep entities simple:

Properties should map directly to form fields
Complex business logic belongs in use cases, not entities
Entities are data containers with property access
Don't add database queries to entities
Don't add external API calls to entities

Good Entity:
phpclass ContactFormEntity extends SMPLFY_BaseEntity {
    protected function get_property_map(): array {
        return [
            'nameFirst' => '1.3',
            'nameLast' => '1.6',
            'email' => '2'
        ];
    }
    
    // Simple helper methods are OK
    public function get_full_name() {
        return $this->nameFirst . ' ' . $this->nameLast;
    }
}
Bad Entity:
phpclass ContactFormEntity extends SMPLFY_BaseEntity {
    // DON'T do this - business logic belongs in use cases
    public function send_to_crm() {
        wp_remote_post(...);
    }
    
    // DON'T do this - queries belong in repositories
    public function get_related_orders() {
        return GFAPI::get_entries(...);
    }
}
Use Case Design
Single Responsibility:

Each use case should handle one specific action
If a use case gets too large, split it into multiple use cases
Use descriptive names: ContactFormSubmissionUsecase not ContactUsecase

Dependency Injection:
php// GOOD - Dependencies injected via constructor
class ContactFormSubmissionUsecase {
    private ContactFormRepository $contactRepo;
    private CustomerRepository $customerRepo;
    
    public function __construct(
        ContactFormRepository $contactRepo,
        CustomerRepository $customerRepo
    ) {
        $this->contactRepo = $contactRepo;
        $this->customerRepo = $customerRepo;
    }
}

// BAD - Creating dependencies inside use case
class ContactFormSubmissionUsecase {
    public function handle($entry) {
        $repo = new ContactFormRepository(); // Don't do this
    }
}
Logging Strategy
Log meaningful events:
php// GOOD - Contextual information
SMPLFY_Log::info("Contact form submitted", [
    'entry_id' => $entry['id'],
    'user_email' => $entity->email,
    'form_id' => FormIds::CONTACT_FORM_ID
]);

// BAD - Not enough context
SMPLFY_Log::info("Form submitted");
Log errors with full context:
phptry {
    $result = $this->repository->update($entity);
} catch (Exception $e) {
    SMPLFY_Log::error("Failed to update entry", [
        'entry_id' => $entity->get_entry_id(),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'entity_data' => $entity->to_array()
    ]);
}
Don't log sensitive data:
php// BAD - Logging passwords or API keys
SMPLFY_Log::info("API call", [
    'api_key' => get_option('api_key'), // Don't log secrets
    'password' => $entity->password      // Don't log passwords
]);

// GOOD - Redact sensitive data
SMPLFY_Log::info("API call", [
    'api_key' => '***REDACTED***',
    'user_id' => $entity->get_entry_id()
]);
Error Handling
Always check for WP_Error:
php$entry_id = $repository->add($entity);

if (is_wp_error($entry_id)) {
    SMPLFY_Log::error("Failed to create entry", [
        'error' => $entry_id->get_error_message()
    ]);
    return false;
}

// Continue with success case
Use try-catch for external API calls:
phptry {
    $response = wp_remote_post($api_url, $args);
    
    if (is_wp_error($response)) {
        throw new Exception($response->get_error_message());
    }
    
    $body = json_decode(wp_remote_retrieve_body($response));
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response');
    }
    
    // Process response
    
} catch (Exception $e) {
    SMPLFY_Log::error("API call failed", [
        'error' => $e->getMessage(),
        'api_url' => $api_url
    ]);
    return false;
}
```

### Code Organization

**Group related functionality:**
```
client-plugin/
├── public/php/
│   ├── types/
│   │   ├── FormIds.php
│   │   ├── FieldIds.php
│   │   └── WorkflowStepIds.php
│   ├── entities/
│   │   ├── ContactFormEntity.php
│   │   ├── ApplicationFormEntity.php
│   │   └── OrderFormEntity.php
│   ├── repositories/
│   │   ├── ContactFormRepository.php
│   │   ├── ApplicationFormRepository.php
│   │   └── OrderFormRepository.php
│   ├── usecases/
│   │   ├── ContactFormSubmissionUsecase.php
│   │   ├── ApplicationApprovalUsecase.php
│   │   └── OrderProcessingUsecase.php
│   ├── adapters/
│   │   ├── GravityFormsAdapter.php
│   │   ├── GravityFlowAdapter.php
│   │   └── WordPressAdapter.php
│   └── services/
│       ├── CrmService.php
│       └── EmailService.php

🔄 Handling Updates
When Core Plugin Updates
Since client plugins depend on this core plugin, follow this process:

Review changes - Check what changed in the core plugin
Test locally with at least one client plugin
Deploy to staging environment first
Monitor Datadog logs after staging deployment
Roll out incrementally to production sites
Have rollback plan ready

When Creating Breaking Changes
If you need to make breaking changes to the core plugin:

Document clearly what's changing and why
Provide migration examples for client plugins
Consider backward compatibility when possible
Update boilerplate plugin to reflect new patterns
Notify team before deploying
Consider versioning (see below)

Recommended: Version Tracking
While you don't currently have version constraints, consider implementing:
1. Semantic Versioning

1.0.0 - Initial release
1.1.0 - New features (backward compatible)
1.0.1 - Bug fixes (backward compatible)
2.0.0 - Breaking changes

2. CHANGELOG.md
Track all changes so team knows what's new:
markdown# Changelog

## [2.0.0] - 2025-03-15
### Breaking Changes
- Changed WorkflowStep::send() to require entity->formEntry instead of full entity
- Renamed SMPLFY_BaseEntity::getField() to get_field()

### Added
- New SMPLFY_Log::debug() method for debug-level logging
- Support for Gravity Forms 2.9+

### Fixed
- Repository get_one() now handles null values correctly

## [1.5.0] - 2025-02-01
### Added
- WordPress Heartbeat API integration support
- New require_utilities() function

### Fixed
- Entity property mapping with sub-fields
3. Plugin Header Versioning
php/**
 * Plugin Name: SMPLFY Core Plugin
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.3
 */

⚠️ Important Notes

Not standalone: This plugin requires a client-specific companion plugin to function
No version constraints currently: Client plugins should be tested when updating core plugin (consider implementing version checks)
Per-site installation: Each client site gets its own copy of both core and client plugins (not centrally managed via update server)
Team use only: Designed for internal development team, not for public distribution
Field IDs must be strings: Always use string keys in get_property_map() (e.g., '2' not 2)
formEntry property: When using WorkflowStep::send(), always pass $entity->formEntry, not the entity itself


🔮 Future Enhancements to Consider
While not currently implemented, consider these improvements:

Version Constraints

Add version checking between core and client plugins
Prevent activation if versions incompatible
Display admin notice if update needed


Webhook Framework

Base classes for incoming/outgoing webhooks
Standardized authentication patterns
Request/response logging
Retry logic for failed webhooks


Enhanced Logging

Log levels configurable per environment
Log rotation/cleanup
Performance metrics tracking
Structured logging format


Developer Tools

WP-CLI commands for creating entities/repositories
Code generation from existing forms
Migration tools for updating field mappings
Validation helpers for entity data


Testing Framework

Unit test helpers for repositories
Mock entities for testing
Integration test examples
CI/CD pipeline templates


Documentation Generator

Auto-generate property documentation from entities
Field mapping documentation export
API documentation for use cases




📚 Additional Resources

SMPLFY Boilerplate Plugin - Starting template for client plugins
SMPLFY Core Plugin - This plugin
Gravity Forms Documentation - Official GF docs
Gravity Forms API - GF API reference
Gravity Flow Documentation - Gravity Flow docs
WordPress Heartbeat API - WP Heartbeat docs


🤝 Contributing
This is an internal tool for our development team. When making updates:

Test locally with an existing client plugin
Update this README if behavior changes
Add entry to CHANGELOG.md (recommended - template below)
Communicate changes to team before deployment
Consider backward compatibility when possible


📄 License
GPL v2 or later

❓ Questions?

Check this README first
Review the SMPLFY Boilerplate Plugin for examples
Check Datadog logs for runtime issues
Ask the development team


📋 Quick Reference
Common Commands
bash# Create new entity and repository from boilerplate
cp ExampleEntity.php ContactFormEntity.php
cp ExampleRepository.php ContactFormRepository.php

# View form structure
wp gf form get 5

# View entry data
wp gf entry get 123

# Test repository locally
wp eval "echo json_encode((new ContactFormRepository())->get_all());"
Code Snippets
Create and save entity:
php$entity = new ContactFormEntity();
$entity->nameFirst = 'John';
$entity->email = 'john@example.com';
$entry_id = $repository->add($entity);
Load and update entity:
php$entity = $repository->get_one('email', 'john@example.com');
$entity->phone = '555-1234';
$repository->update($entity);
Move workflow step:
phpWorkflowStep::send(WorkflowStepIds::APPROVED, $entity->formEntry);
Log to Datadog:
phpSMPLFY_Log::info("Action completed", ['entry_id' => $id]);
SMPLFY_Log::error("Action failed", ['error' => $e->getMessage()]);
