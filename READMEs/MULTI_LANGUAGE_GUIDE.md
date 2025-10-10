# Multi-Language Support Guide

## Overview
The application now supports 4 languages:
- 🇬🇧 English (en) - Default
- 🇫🇷 French (fr)
- 🇳🇱 Dutch (nl)
- 🇮🇳 Hindi (hi)

## How It Works

### Language Files
Translation files are located in `ci4/app/Language/{code}/App.php`:
- `ci4/app/Language/en/App.php` - English (200+ terms)
- `ci4/app/Language/fr/App.php` - French
- `ci4/app/Language/nl/App.php` - Dutch
- `ci4/app/Language/hi/App.php` - Hindi

### Language Switching
Users can switch languages using the dropdown in the top header:
1. Click the language dropdown (shows flag and language name)
2. Select desired language
3. Page will reload with new language

### Language Priority
The system loads languages in this order:
1. **Session preference** - User's current session choice
2. **Database user preference** - Saved in users.language_code column
3. **Business default** - Saved in businesses.language_code column
4. **System default** - English (en)

## Using Translations in Views

### Basic Usage
Replace hardcoded text with the `lang()` helper:

```php
<!-- Before -->
<h1>Tasks</h1>

<!-- After -->
<h1><?= lang('App.tasks') ?></h1>
```

### Common Translations
```php
<!-- Actions -->
<?= lang('App.submit') ?>      // Submit
<?= lang('App.save') ?>        // Save
<?= lang('App.delete') ?>      // Delete
<?= lang('App.edit') ?>        // Edit

<!-- Navigation -->
<?= lang('App.dashboard') ?>   // Dashboard
<?= lang('App.tasks') ?>       // Tasks
<?= lang('App.projects') ?>    // Projects

<!-- Fields -->
<?= lang('App.name') ?>        // Name
<?= lang('App.description') ?> // Description
<?= lang('App.status') ?>      // Status
<?= lang('App.priority') ?>    // Priority

<!-- Status Values -->
<?= lang('App.status_todo') ?>        // To Do
<?= lang('App.status_in_progress') ?> // In Progress
<?= lang('App.status_done') ?>        // Done

<!-- Priority Values -->
<?= lang('App.priority_high') ?>   // High
<?= lang('App.priority_medium') ?> // Medium
<?= lang('App.priority_low') ?>    // Low
```

### In JavaScript
For table headers and dynamic content:
```php
<script>
let columnsTitle = [
    '<?= lang('App.id') ?>',
    '<?= lang('App.name') ?>',
    '<?= lang('App.status') ?>'
];
</script>
```

## Adding New Translations

### 1. Add to English (en/App.php)
```php
return [
    // ... existing translations
    'my_new_key' => 'My New Text',
];
```

### 2. Add to Other Languages
Update fr/App.php, nl/App.php, and hi/App.php with translations:

**French:**
```php
'my_new_key' => 'Mon Nouveau Texte',
```

**Dutch:**
```php
'my_new_key' => 'Mijn Nieuwe Tekst',
```

**Hindi:**
```php
'my_new_key' => 'मेरा नया पाठ',
```

## Examples in Codebase

### Services Module (Completed)
[ci4/app/Views/services/list.php](ci4/app/Views/services/list.php)
```php
<!-- Summary Cards -->
<div class="summary-card-title">
    <i class="fa fa-cogs"></i> <?= lang('App.services') ?>
</div>

<!-- Table Columns -->
let columnsTitle = [
    '<?= lang('App.id') ?>',
    '<?= lang('App.name') ?>',
    '<?= lang('App.category') ?>'
];
```

### Tasks Module (Completed)
[ci4/app/Views/tasks/list.php](ci4/app/Views/tasks/list.php)
```php
let columnsTitle = [
    '<?= lang('App.id') ?>',
    '<?= lang('App.task_title') ?>',
    '<?= lang('App.priority') ?>'
];
```

## Technical Details

### Language Loading
[ci4/app/Controllers/Core/CommonController.php:78-108](ci4/app/Controllers/Core/CommonController.php#L78-L108)

The `changeLanguage()` method is called on every page load and sets the appropriate language based on user preferences.

### Language Switching Endpoint
[ci4/app/Controllers/Dashboard.php:174-197](ci4/app/Controllers/Dashboard.php#L174-L197)

The `/dashboard/setLanguage` endpoint handles AJAX requests from the language switcher.

### Language Switcher UI
[ci4/app/Views/common/top-header.php:54-155](ci4/app/Views/common/top-header.php#L54-L155)

Dropdown with flags and JavaScript handler for language switching.

## Translation Coverage

### Fully Translated Modules
- ✅ Services (list view with summary cards and table)
- ✅ Tasks (list view table headers)

### Pending Translation
The following modules still need view updates:
- Businesses
- Customers
- Projects
- Contacts
- Invoices
- Timeslips
- Calendar
- And other modules...

## Next Steps for Developers

1. **Update existing views** - Replace hardcoded text with `lang()` helper
2. **Add missing translations** - If you need new terms, add to all 4 language files
3. **Test translations** - Switch languages and verify all UI text translates correctly
4. **Maintain consistency** - Use existing translation keys when possible

## Notes

- **Data remains unchanged** - Only UI text is translated, not user-entered data
- **Session-based** - Language preference stored in session, doesn't require page reload for all changes
- **Extensible** - Easy to add new languages by creating new language files
- **CodeIgniter native** - Uses CI4's built-in language system

## Support

For questions or issues with multi-language support:
1. Check language files in `ci4/app/Language/`
2. Verify `lang()` helper usage in views
3. Test language switching via top header dropdown
4. Check browser console for JavaScript errors
