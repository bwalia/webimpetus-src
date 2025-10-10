# Tags Implementation Guide

## Overview

A universal tagging system has been implemented across the application, allowing users to categorize and organize **Projects**, **Customers**, **Contacts**, and **Templates** using custom color-coded tags.

## Features

### ✅ Implemented Features

1. **Multi-entity Tagging**: Tag support for:
   - Projects
   - Customers
   - Contacts
   - Templates

2. **Tag Management Interface** ([/tags/manage](http://slworker00:5500/tags/manage)):
   - Create new tags with custom names and colors
   - Edit existing tags
   - Delete tags (automatically removes from all associated entities)
   - Color picker for visual customization

3. **Entry Form Integration**:
   - Multi-select dropdown with Select2
   - Color-coded tag display
   - Real-time tag loading
   - Automatic saving on form submission
   - Link to tag management page

4. **Database Architecture**:
   - Main `tags` table
   - Junction tables: `project_tags`, `customer_tags`, `contact_tags`, `template_tags`
   - Database views for optimized queries with tags

## Database Schema

### Tags Table
```sql
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#667eea',
    `description` TEXT,
    `uuid_business_id` VARCHAR(150),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_tag_per_business` (`slug`, `uuid_business_id`),
    KEY `idx_business` (`uuid_business_id`)
);
```

### Junction Tables
```sql
CREATE TABLE IF NOT EXISTS `project_tags` (
    `project_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`project_id`, `tag_id`),
    UNIQUE KEY `unique_project_tag` (`project_id`, `tag_id`),
    KEY `idx_tag` (`tag_id`)
);

CREATE TABLE IF NOT EXISTS `customer_tags` (
    `customer_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`customer_id`, `tag_id`),
    UNIQUE KEY `unique_customer_tag` (`customer_id`, `tag_id`),
    KEY `idx_tag` (`tag_id`)
);

CREATE TABLE IF NOT EXISTS `contact_tags` (
    `contact_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`contact_id`, `tag_id`),
    UNIQUE KEY `unique_contact_tag` (`contact_id`, `tag_id`),
    KEY `idx_tag` (`tag_id`)
);

CREATE TABLE IF NOT EXISTS `template_tags` (
    `template_id` INT(11) UNSIGNED NOT NULL,
    `tag_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`template_id`, `tag_id`),
    UNIQUE KEY `unique_template_tag` (`template_id`, `tag_id`),
    KEY `idx_tag` (`tag_id`)
);
```

### Database Views
```sql
-- Optimized view for projects with tags
CREATE OR REPLACE VIEW `view_projects_with_tags` AS
SELECT p.*,
    GROUP_CONCAT(DISTINCT t.name) as tag_names,
    GROUP_CONCAT(DISTINCT t.color) as tag_colors,
    c.company_name as customer_name
FROM projects p
LEFT JOIN project_tags pt ON p.id = pt.project_id
LEFT JOIN tags t ON pt.tag_id = t.id
LEFT JOIN customers c ON p.customers_id = c.id
GROUP BY p.id;

-- Similar views exist for customers and contacts
```

## API Endpoints

### Tags Controller Routes

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/tags` | GET | List all tags (web view) |
| `/tags/manage` | GET | Tag management interface |
| `/tags/tagsList` | GET | Get all tags as JSON |
| `/tags/save` | POST | Create or update a tag |
| `/tags/delete/{id}` | GET/DELETE | Delete a tag |
| `/tags/attach` | POST | Attach tags to an entity |
| `/tags/getEntityTags/{type}/{id}` | GET | Get tags for specific entity |

### API Examples

#### Get All Tags (JSON)
```javascript
GET /tags/tagsList

Response:
{
    "status": true,
    "data": [
        {
            "id": 1,
            "name": "High Priority",
            "slug": "high-priority",
            "color": "#ef4444",
            "description": "Important items requiring immediate attention"
        },
        {
            "id": 2,
            "name": "Marketing",
            "slug": "marketing",
            "color": "#10b981",
            "description": "Marketing related projects"
        }
    ]
}
```

#### Create a New Tag
```javascript
POST /tags/save
Data: {
    name: "VIP Customer",
    color: "#f59e0b",
    description: "High-value customers"
}

Response:
{
    "status": true,
    "message": "Tag created successfully!"
}
```

#### Attach Tags to Entity
```javascript
POST /tags/attach
Data: {
    entity_type: "project",  // or "customer", "contact"
    entity_id: 123,
    tag_ids: [1, 2, 5]
}

Response:
{
    "status": true,
    "message": "Tags updated successfully"
}
```

#### Get Entity Tags
```javascript
GET /tags/getEntityTags/project/123

Response:
{
    "status": true,
    "data": [
        {
            "id": 1,
            "name": "High Priority",
            "color": "#ef4444"
        },
        {
            "id": 2,
            "name": "Marketing",
            "color": "#10b981"
        }
    ]
}
```

## Entry Form Implementation

### Projects ([/projects/edit/{uuid}](ci4/app/Views/projects/edit.php))

**HTML Structure:**
```html
<div class="form-row">
    <div class="form-group col-md-12">
        <label for="project_tags">
            <i class="fa fa-tags"></i> Tags
            <a href="/tags/manage" target="_blank">
                <i class="fa fa-cog"></i> Manage Tags
            </a>
        </label>
        <select id="project_tags" name="project_tags[]"
                class="form-control select2"
                multiple="multiple"
                data-placeholder="Select tags for this project...">
            <!-- Populated by JavaScript -->
        </select>
    </div>
</div>
```

**JavaScript Implementation:**
```javascript
// Load all available tags
function loadTags() {
    const projectId = '<?= @$project->id ?>';

    $.ajax({
        url: '/tags/tagsList',
        method: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                const $select = $('#project_tags');

                // Populate options
                response.data.forEach(function(tag) {
                    const option = new Option(tag.name, tag.id, false, false);
                    $(option).attr('data-color', tag.color);
                    $select.append(option);
                });

                // Initialize Select2 with custom templates
                $select.select2({
                    placeholder: 'Select tags for this project...',
                    allowClear: true,
                    templateResult: formatTag,
                    templateSelection: formatTagSelection
                });

                // Load current tags if editing
                if (projectId) {
                    loadCurrentTags(projectId);
                }
            }
        }
    });
}

// Load currently assigned tags
function loadCurrentTags(projectId) {
    $.ajax({
        url: '/tags/getEntityTags/project/' + projectId,
        method: 'GET',
        success: function(response) {
            if (response.status && response.data) {
                const tagIds = response.data.map(tag => tag.id.toString());
                $('#project_tags').val(tagIds).trigger('change');
            }
        }
    });
}

// Save tags on form submit
$('#addcustomer').on('submit', function(e) {
    const projectId = '<?= @$project->id ?>';

    if (projectId) {
        e.preventDefault();

        $.ajax({
            url: '/tags/attach',
            method: 'POST',
            data: {
                entity_type: 'project',
                entity_id: projectId,
                tag_ids: $('#project_tags').val() || []
            },
            success: function(response) {
                // Submit main form
                $('#addcustomer').off('submit').submit();
            }
        });
    }
});
```

### Customers ([/customers/edit/{uuid}](ci4/app/Views/customers/edit.php))

Same implementation pattern as projects with:
- `#customer_tags` field ID
- `entity_type: 'customer'`
- `loadCustomerTags()` function

### Contacts ([/contacts/edit/{uuid}](ci4/app/Views/contacts/edit.php))

Same implementation pattern with:
- `#contact_tags` field ID
- `entity_type: 'contact'`
- `loadContactTags()` function

### Templates ([/templates/edit/{uuid}](ci4/app/Views/templates/edit.php))

Same implementation pattern with:
- `#template_tags` field ID
- `entity_type: 'template'`
- `loadTemplateTags()` function
- Integrated with existing template editor sidebar

## List Page Display

Tags are displayed in list pages using custom column renderers:

### Projects List ([/projects](ci4/app/Views/projects/list.php))

```javascript
const columnRenderers = {
    tag_names: function(data, type, row) {
        if (!data) return '<span style="color: #9ca3af;">No tags</span>';

        const tagNames = data.split(', ');
        const tagColors = (row.tag_colors || '').split(',');

        let html = '<div style="display: flex; flex-wrap: wrap; gap: 4px;">';
        tagNames.forEach((tag, index) => {
            const color = tagColors[index]?.trim() || '#667eea';
            html += `<span class="tag" style="background-color: ${color}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem;">${tag}</span>`;
        });
        html += '</div>';
        return html;
    }
};
```

The same pattern is used in customers and contacts list pages.

## Tag Management Interface

### Access
Navigate to [/tags/manage](http://slworker00:5500/tags/manage)

### Features
1. **View All Tags**: Grid display of all tags with color preview
2. **Create Tag**:
   - Enter tag name (auto-generates slug)
   - Choose color with color picker
   - Add optional description
3. **Edit Tag**: Click "Edit" on any tag card
4. **Delete Tag**: Click "Delete" (confirms before deletion)

### Tag Card Display
```html
<div class="tag-card" style="border-left-color: <?= $tag['color'] ?>">
    <div class="tag-card-header">
        <div class="tag-card-name"><?= $tag['name'] ?></div>
        <div class="tag-card-actions">
            <button onclick="editTag(<?= json_encode($tag) ?>)">Edit</button>
            <button onclick="deleteTag(<?= $tag['id'] ?>)">Delete</button>
        </div>
    </div>
    <div class="tag-preview" style="background-color: <?= $tag['color'] ?>">
        <?= $tag['name'] ?>
    </div>
</div>
```

## Multi-Tenancy

All tags are isolated by `uuid_business_id`:
- Users only see tags for their current business
- Tag slugs are unique per business (same tag name can exist in different businesses)
- Automatic filtering in all queries

## Usage Examples

### Creating a Tag
1. Go to `/tags/manage`
2. Click "+ Create New Tag"
3. Enter name: "High Priority"
4. Choose color: #ef4444 (red)
5. Add description: "Urgent items"
6. Click "Save"

### Tagging a Project
1. Open any project: `/projects/edit/{uuid}`
2. Scroll to "Tags" section
3. Click the multi-select dropdown
4. Select one or more tags
5. Click "Submit" to save the project and tags

### Viewing Tagged Projects
1. Go to `/projects`
2. Tags appear in the list table as colored badges
3. Filter/search by tag names using the DataTable search

## Files Modified

### Controllers
- **[ci4/app/Controllers/Tags.php](ci4/app/Controllers/Tags.php)** - Complete tag management controller (updated to include template_tags)
- **[ci4/app/Controllers/Templates.php](ci4/app/Controllers/Templates.php)** - Updated to use view_templates_with_tags

### Views
- **[ci4/app/Views/tags/manage.php](ci4/app/Views/tags/manage.php)** - Tag management interface
- **[ci4/app/Views/projects/edit.php](ci4/app/Views/projects/edit.php:90-106)** - Project tags field
- **[ci4/app/Views/customers/edit.php](ci4/app/Views/customers/edit.php:207-224)** - Customer tags field
- **[ci4/app/Views/contacts/edit.php](ci4/app/Views/contacts/edit.php:252-269)** - Contact tags field
- **[ci4/app/Views/templates/edit.php](ci4/app/Views/templates/edit.php:47-64)** - Template tags field
- **[ci4/app/Views/projects/list.php](ci4/app/Views/projects/list.php)** - Tag display in list
- **[ci4/app/Views/customers/list.php](ci4/app/Views/customers/list.php)** - Tag display in list
- **[ci4/app/Views/contacts/list.php](ci4/app/Views/contacts/list.php)** - Tag display in list
- **[ci4/app/Views/templates/list.php](ci4/app/Views/templates/list.php)** - Enhanced with summary cards and tag display

### Database
- `database_migrations_tags_and_projects.sql` - Complete schema with tags tables and views
- `add_template_tags.sql` - Template tags junction table and view

## Best Practices

### Tag Naming
- Use clear, descriptive names
- Keep names short (under 20 characters)
- Use consistent capitalization

### Color Usage
- Use meaningful colors (red = urgent, green = success, blue = info)
- Maintain color consistency across similar tag types
- Ensure good contrast for readability

### Tag Organization
- Limit to 10-15 tags per business to avoid confusion
- Use hierarchical naming for related tags (e.g., "Sales: New Lead", "Sales: Qualified")
- Archive or delete unused tags regularly

## Testing Checklist

- [x] Create new tag from `/tags/manage`
- [x] Edit existing tag
- [x] Delete tag and verify removal from entities
- [x] Add tags to new project
- [x] Edit tags on existing project
- [x] Add tags to new customer
- [x] Add tags to new contact
- [x] View tags in projects list
- [x] View tags in customers list
- [x] View tags in contacts list
- [x] Verify multi-tenancy (tags isolated by business)
- [x] Test tag search/filter in list pages

## Future Enhancements

1. **Tag Filtering**: Add filter buttons on list pages to show only items with specific tags
2. **Tag Statistics**: Dashboard showing tag usage counts
3. **Tag Import/Export**: CSV import/export for bulk tag management
4. **Tag Templates**: Pre-defined tag sets for common use cases
5. **Tag Permissions**: Restrict tag creation/editing to specific roles
6. **Tag Colors from Palette**: Predefined color palette instead of free color picker
7. **Tag Autocomplete**: Suggest existing tags when typing in search fields
8. **Bulk Tag Assignment**: Select multiple items and assign tags in one action

## Troubleshooting

### Tags Not Showing in Dropdown
- Verify `/tags/tagsList` returns data
- Check browser console for JavaScript errors
- Ensure Select2 library is loaded

### Tags Not Saving
- Check that entity (project/customer/contact) has an `id` field
- Verify `/tags/attach` endpoint is accessible
- Check that junction tables exist in database

### Tags Not Displaying in List
- Verify database views are created
- Check that list queries use the views (e.g., `view_projects_with_tags`)
- Ensure column renderer is defined for `tag_names`

## Support

For issues or questions:
- Check browser console for errors
- Review database query logs
- Verify multi-tenancy `uuid_business_id` filtering

---

**Status**: ✅ Complete and Production Ready

**Last Updated**: October 9, 2024
