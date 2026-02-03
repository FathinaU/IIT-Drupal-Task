# Custom Event Registration Module (Drupal 10)

This module was developed as part of the IIT Bombay Semester-Long Internship screening task. It provides a system for administrators to configure events and for users to register for them with dynamic, AJAX-enabled forms.

## Installation
1. Upload the `event_registration` folder to your Drupal installation's `modules/custom` directory.
2. Enable the module via Drush (`drush en event_registration`) or the Admin UI (`/admin/extend`).
3. Import the provided `database_tables.sql` if tables are not automatically created via the Schema API.

## URLs & Navigation
- **Event Configuration (Admin):** `/admin/config/event-setup`
- **User Registration Form:** `/event/register`
- **Admin Listing Page:** `/admin/event-registrations`

## Database Architecture
The module uses two custom database tables defined via the Drupal Schema API:
1. **event_config**: Stores administrator-defined event details including Event Name, Category, Event Date, and Registration Start/End dates.
2. **event_registrations**: Stores user-submitted data. It includes a foreign key relationship (Event Name) to the config table and a created timestamp.

## Implementation Details

### 1. Dynamic AJAX Form Logic
The Registration Form utilizes the Drupal Form API with `#ajax` callbacks. When a user selects a "Category," an AJAX request is triggered to the `updateEventList` function. This function queries the `event_config` table and dynamically populates the "Event Name" and "Event Date" dropdowns based on the selected category, providing a seamless user experience without page reloads.

### 2. Validation Logic
The module enforces strict data integrity:
- **Duplicate Prevention:** Before saving, the `validateForm` method checks the database for an existing entry with the same Email and Event Name combination.
- **Data Integrity:** Regex is used to ensure no special characters are entered into text fields.
- **Date Validation:** The registration form is only accessible if the current date falls between the Admin-defined Start and End dates.

### 3. Email Notification Logic
Upon successful form submission, the module utilizes the **Drupal Mail API** and `hook_mail`. 
- An HTML-formatted confirmation email is sent to the user.
- An administrative notification is sent to the address configured in the Admin settings.
- All email content (Name, Date, Event) is passed through a `$params` array to the mail template.

### 4. Security & Permissions
- **Custom Permission:** Access to the registrant listing is restricted via a custom permission `access event registrations` defined in `event_registration.permissions.yml`.
- **Dependency Injection:** The module follows Drupal 10 best practices by injecting the `database` and `plugin.manager.mail` services rather than calling the global `\Drupal` container.

## Technical Constraints Met
- Drupal 10.x compatible.
- PSR-4 Autoloading compliant.
- No contributed modules used.
- Validated against Drupal Coding Standards.
