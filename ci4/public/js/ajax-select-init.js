/**
 * AJAX Select2 Initialization Library
 * Provides reusable Select2 configurations for large datasets
 *
 * Usage:
 * initAjaxSelect('.select-employee-ajax', 'employees');
 * initAjaxSelect('.select-customer-ajax', 'customers');
 */

/**
 * Initialize Select2 with AJAX for a given entity type
 * @param {string} selector - CSS selector for the select element
 * @param {string} entityType - Type of entity (employees, customers, projects, tasks, contacts, etc.)
 * @param {object} options - Additional options
 */
function initAjaxSelect(selector, entityType, options = {}) {
    const defaults = {
        minimumInputLength: 2,
        allowClear: true,
        placeholder: 'Type to search...',
        delay: 250,
        limit: 50,
        baseUrl: '/common/search',
        customParams: {},
        formatResult: null,
        formatSelection: null
    };

    const config = { ...defaults, ...options };

    // Entity-specific configurations
    const entityConfigs = {
        employees: {
            url: '/common/searchEmployees',
            placeholder: 'Type to search employees',
            formatResult: function(item) {
                return item.first_name + ' ' + item.surname +
                       (item.job_title ? ' (' + item.job_title + ')' : '') +
                       (item.email ? ' - ' + item.email : '');
            },
            formatSelection: function(item) {
                return item.first_name + ' ' + item.surname;
            }
        },
        customers: {
            url: '/common/searchCustomers',
            placeholder: 'Type to search customers',
            formatResult: function(item) {
                return item.company_name +
                       (item.email ? ' - ' + item.email : '') +
                       (item.phone ? ' - ' + item.phone : '');
            },
            formatSelection: function(item) {
                return item.company_name;
            }
        },
        contacts: {
            url: '/common/searchContacts',
            placeholder: 'Type to search contacts',
            formatResult: function(item) {
                return item.first_name + ' ' + (item.surname || '') +
                       (item.email ? ' - ' + item.email : '') +
                       (item.company_name ? ' (' + item.company_name + ')' : '');
            },
            formatSelection: function(item) {
                return item.first_name + ' ' + (item.surname || '');
            }
        },
        projects: {
            url: '/common/searchProjects',
            placeholder: 'Type to search projects',
            formatResult: function(item) {
                return (item.project_code ? '[' + item.project_code + '] ' : '') +
                       item.name +
                       (item.customer_name ? ' - ' + item.customer_name : '');
            },
            formatSelection: function(item) {
                return (item.project_code ? '[' + item.project_code + '] ' : '') + item.name;
            }
        },
        tasks: {
            url: '/common/searchTasks',
            placeholder: 'Type to search tasks',
            formatResult: function(item) {
                return item.name +
                       (item.project_name ? ' (' + item.project_name + ')' : '') +
                       (item.status ? ' [' + item.status + ']' : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        users: {
            url: '/common/searchUsers',
            placeholder: 'Type to search users',
            formatResult: function(item) {
                return item.name + (item.email ? ' - ' + item.email : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        businesses: {
            url: '/common/searchBusinesses',
            placeholder: 'Type to search businesses',
            formatResult: function(item) {
                return item.name + (item.company_email ? ' - ' + item.company_email : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        categories: {
            url: '/common/searchCategories',
            placeholder: 'Type to search categories',
            formatResult: function(item) {
                return item.name + (item.description ? ' - ' + item.description : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        sprints: {
            url: '/common/searchSprints',
            placeholder: 'Type to search sprints',
            formatResult: function(item) {
                return item.sprint_name +
                       (item.start_date ? ' (' + item.start_date + ' to ' + item.end_date + ')' : '');
            },
            formatSelection: function(item) {
                return item.sprint_name;
            }
        },
        templates: {
            url: '/common/searchTemplates',
            placeholder: 'Type to search templates',
            formatResult: function(item) {
                return item.name + (item.type ? ' [' + item.type + ']' : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        roles: {
            url: '/common/searchRoles',
            placeholder: 'Type to search roles',
            formatResult: function(item) {
                return item.name + (item.description ? ' - ' + item.description : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        },
        tags: {
            url: '/common/searchTags',
            placeholder: 'Type to search tags',
            formatResult: function(item) {
                return item.name + (item.usage_count ? ' (' + item.usage_count + ')' : '');
            },
            formatSelection: function(item) {
                return item.name;
            }
        }
    };

    const entityConfig = entityConfigs[entityType] || {};
    const url = config.url || entityConfig.url || config.baseUrl + '/' + entityType;
    const formatResult = config.formatResult || entityConfig.formatResult || function(item) {
        return item.name || item.text || item.id;
    };
    const formatSelection = config.formatSelection || entityConfig.formatSelection || formatResult;

    $(selector).select2({
        ajax: {
            url: url,
            dataType: 'json',
            delay: config.delay,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    ...config.customParams
                };
            },
            processResults: function(data, params) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: formatResult(item),
                            id: item.id,
                            ...item // Include all item properties for custom use
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: config.minimumInputLength,
        placeholder: config.placeholder || entityConfig.placeholder,
        allowClear: config.allowClear,
        templateResult: function(item) {
            if (!item.id) {
                return item.text;
            }
            return $('<span>').text(item.text);
        },
        templateSelection: function(item) {
            if (!item.id) {
                return item.text;
            }
            if (config.formatSelection) {
                return config.formatSelection(item);
            }
            if (entityConfig.formatSelection && typeof entityConfig.formatSelection === 'function') {
                return entityConfig.formatSelection(item);
            }
            return item.text;
        }
    });
}

/**
 * Initialize cascading selects (e.g., Customer -> Project -> Task)
 * @param {object} config - Configuration object
 */
function initCascadingSelects(config) {
    // Example config:
    // {
    //   customer: { selector: '#customer_id', entityType: 'customers' },
    //   project: { selector: '#project_id', entityType: 'projects', dependsOn: 'customer' },
    //   task: { selector: '#task_id', entityType: 'tasks', dependsOn: 'project' }
    // }

    Object.keys(config).forEach(function(key) {
        const item = config[key];
        const customParams = {};

        // Add dependency parameter
        if (item.dependsOn) {
            const dependsOnSelector = config[item.dependsOn].selector;
            customParams[item.dependsOn + '_id'] = function() {
                return $(dependsOnSelector).val();
            };
        }

        initAjaxSelect(item.selector, item.entityType, {
            customParams: customParams
        });

        // Reset dependent selects when parent changes
        if (item.dependsOn) {
            const dependsOnSelector = config[item.dependsOn].selector;
            $(dependsOnSelector).on('change', function() {
                $(item.selector).val(null).trigger('change');
            });
        }
    });
}

/**
 * Initialize filter selects (can load initial results without typing)
 * @param {string} selector - CSS selector
 * @param {string} entityType - Entity type
 * @param {object} options - Additional options
 */
function initFilterSelect(selector, entityType, options = {}) {
    const filterOptions = {
        minimumInputLength: 0, // Allow loading without typing
        ...options
    };
    initAjaxSelect(selector, entityType, filterOptions);
}

/**
 * Batch initialize common selects on a form
 * @param {object} selectors - Object mapping entity types to selectors
 */
function initCommonSelects(selectors = {}) {
    const defaultSelectors = {
        employees: '.select-employee-ajax',
        customers: '.select-customer-ajax',
        contacts: '.select-contact-ajax',
        projects: '.select-project-ajax',
        tasks: '.select-task-ajax',
        users: '.select-user-ajax',
        businesses: '.select-business-ajax',
        categories: '.select-category-ajax',
        sprints: '.select-sprint-ajax',
        templates: '.select-template-ajax',
        roles: '.select-role-ajax',
        tags: '.select-tag-ajax',
        ...selectors
    };

    Object.keys(defaultSelectors).forEach(function(entityType) {
        const selector = defaultSelectors[entityType];
        if ($(selector).length > 0) {
            initAjaxSelect(selector, entityType);
        }
    });
}

// Auto-initialize on document ready
$(document).ready(function() {
    // Auto-init common selects if they exist
    if (typeof autoInitAjaxSelects !== 'undefined' && autoInitAjaxSelects) {
        initCommonSelects();
    }
});
