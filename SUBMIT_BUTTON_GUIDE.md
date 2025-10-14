# Standardized Submit Button Component

## Overview

All edit/create forms now use a standardized submit button component located at `/ci4/app/Views/common/submit-button.php`. This ensures consistent design, proper permission handling, and better user experience across the application.

## Features

✅ **Consistent Design** - Beautiful gradient button with icon, professional styling
✅ **Permission-Aware** - Automatically disables if user lacks create/update permission
✅ **Responsive** - Looks great on all screen sizes
✅ **Customizable** - Easy to customize text, icon, and styling
✅ **Cancel Button** - Optional cancel button that links back to list page
✅ **View-Only Indicator** - Shows helpful message when user has no edit permissions
✅ **Hover Effects** - Smooth animations on hover and click

## Basic Usage

### Simple Usage (Default)

Replace your existing submit button with:

```php
<?php include(APPPATH . 'Views/common/submit-button.php'); ?>
```

This will create a button that says "Save" with a save icon.

### Custom Button Text

```php
<?php
    $submitButtonText = 'Save Payment';
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

### Custom Icon

```php
<?php
    $submitButtonText = 'Save Customer';
    $submitButtonIcon = 'fa-check';  // Font Awesome icon class
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

### Without Cancel Button

```php
<?php
    $submitButtonText = 'Save Contact';
    $showCancelButton = false;
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

### Custom Cancel URL

```php
<?php
    $submitButtonText = 'Save Entry';
    $cancelUrl = '/dashboard';
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

## Available Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$submitButtonText` | string | `'Save'` | Text displayed on the button |
| `$submitButtonIcon` | string | `'fa-save'` | Font Awesome icon class |
| `$submitButtonClass` | string | `'btn btn-primary btn-lg'` | CSS classes for the button |
| `$submitButtonId` | string | `''` | HTML id attribute for the button |
| `$showCancelButton` | bool | `true` | Whether to show the cancel button |
| `$cancelUrl` | string | Auto-detected | URL for cancel button (defaults to `/{tableName}`) |
| `$can_create` | bool | Auto | Create permission flag (passed from controller) |
| `$can_update` | bool | Auto | Update permission flag (passed from controller) |

## Permission Handling

The component automatically checks permissions passed from the controller:

- If user has **no create or update permission**, button is disabled
- Disabled button shows lock icon and "(No Permission)" text
- A helpful message appears: "You have view-only access to this module"
- Form can still be viewed but cannot be submitted

### Ensure Controller Passes Permissions

Make sure your controller's `edit()` or `index()` method includes:

```php
$this->addPermissionsToView($this->data);
```

This adds `$can_create`, `$can_update`, `$can_delete` variables to the view.

## Examples

### Example 1: Payment Form
```php
<?php
    $submitButtonText = 'Save Payment';
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

Result:
- Button text: "Save Payment"
- Icon: Save icon (default)
- Cancel button: Yes, links to `/payments`
- Permission check: Enabled

### Example 2: Contact Form (in tabs, no cancel)
```php
<?php
    $submitButtonText = 'Save Contact';
    $showCancelButton = false;
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

Result:
- Button text: "Save Contact"
- Icon: Save icon (default)
- Cancel button: No
- Permission check: Enabled

### Example 3: Custom Icon and URL
```php
<?php
    $submitButtonText = 'Create Invoice';
    $submitButtonIcon = 'fa-plus-circle';
    $cancelUrl = '/dashboard';
    include(APPPATH . 'Views/common/submit-button.php');
?>
```

Result:
- Button text: "Create Invoice"
- Icon: Plus circle icon
- Cancel button: Yes, links to `/dashboard`
- Permission check: Enabled

## Visual Design

The component includes professional styling:

### Primary Button (Submit)
- **Gradient Background**: Purple gradient (#667eea → #764ba2)
- **Large Size**: 16px font, 12px vertical padding
- **Icon**: Font Awesome icon on the left
- **Shadow**: Soft shadow that increases on hover
- **Hover Effect**: Lifts up 2px with deeper shadow
- **Disabled State**: Gray background, no hover effect, locked icon

### Secondary Button (Cancel)
- **Background**: Gray (#6c757d)
- **Same Size**: Matches primary button
- **Icon**: X icon on the left
- **Hover Effect**: Slightly darker gray

### Container
- **Background**: Light gray (#f8f9fa)
- **Padding**: 20px
- **Border Radius**: 8px rounded corners
- **Margin**: 30px top margin to separate from form
- **Spacing**: Flexbox layout for proper alignment

## Migration Checklist

When updating a form to use the standardized button:

1. ✅ Ensure controller has `use PermissionTrait;`
2. ✅ Ensure controller's edit method calls `$this->requireEditPermission($id);`
3. ✅ Ensure controller's index method calls `$this->addPermissionsToView($this->data);`
4. ❌ Find the existing submit button in the view file
5. ❌ Replace with the component include
6. ❌ Set custom button text if needed
7. ❌ Test with different permission levels
8. ❌ Test responsive design on mobile

## Forms Already Updated

### ✅ Completed
- Payments (`/payments/edit`)
- Receipts (`/receipts/edit`)
- Contacts (`/contacts/edit`)
- Customers (`/customers/edit`)
- Employees (`/employees/edit`)
- Tasks (`/tasks/edit`)

### ❌ To Be Updated
All other edit forms in the application should be updated to use this component for consistency.

## Testing

### Test Scenarios

1. **Full Permission User**
   - ✅ Button is enabled
   - ✅ Hover effect works
   - ✅ Can submit form
   - ✅ No permission message shown

2. **View-Only User**
   - ✅ Button is disabled and grayed out
   - ✅ Shows lock icon and "(No Permission)" text
   - ✅ Shows "view-only access" message
   - ✅ Cannot submit form

3. **Responsive Design**
   - ✅ Looks good on desktop
   - ✅ Looks good on tablet
   - ✅ Buttons stack properly on mobile
   - ✅ Text is readable on all sizes

## Customization

### Override Styling

If you need to customize the styling for a specific form, you can override the CSS after including the component:

```php
<?php
    $submitButtonText = 'Special Action';
    include(APPPATH . 'Views/common/submit-button.php');
?>

<style>
    .form-actions .btn-primary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>
```

### Add Additional Buttons

You can add extra buttons next to the submit button:

```php
<?php
    $submitButtonText = 'Save Draft';
    include(APPPATH . 'Views/common/submit-button.php');
?>

<button type="submit" name="action" value="publish" class="btn btn-success btn-lg">
    <i class="fa fa-paper-plane"></i> Publish
</button>
```

## Best Practices

1. **Always use context-specific text** - "Save Payment" is better than "Submit"
2. **Match icon to action** - Use `fa-save` for save, `fa-check` for complete, etc.
3. **Consider the context** - Hide cancel button in tabs/modals where it doesn't make sense
4. **Test permissions** - Always test with view-only users
5. **Keep it simple** - Don't override styling unless necessary

## Support

If you encounter issues with the submit button component:

1. Verify the controller is using `PermissionTrait`
2. Check that `$this->addPermissionsToView($this->data)` is called
3. Ensure user has logged out and back in after permission changes
4. Check browser console for JavaScript errors
5. Verify Font Awesome icons are loading

## Version History

- **v1.0** (2025-01-14) - Initial component with permission handling and responsive design
- Forms updated: Payments, Receipts, Contacts, Customers, Employees, Tasks
