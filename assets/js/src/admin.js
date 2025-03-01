/**
 * Modern Faker Admin JavaScript
 */

// Import WordPress dependencies
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

// Import stylesheets
import '../../css/admin.scss';

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  // Initialize the Modern Faker admin interface
  initModernFaker();
});

/**
 * Initialize Modern Faker functionality
 */
function initModernFaker() {
  // Generator form handling
  const generatorForm = document.querySelector('.lorem-press-generator-form form');
  if (generatorForm) {
    generatorForm.addEventListener('submit', handleFormSubmit);
  }

  // Meta field handling
  initMetaFieldsManager();

  // Dynamic settings updates
  initDynamicSettings();
}

/**
 * Handle form submission for generation
 * 
 * @param {Event} event The form submit event
 */
function handleFormSubmit(event) {
  event.preventDefault();

  // Get form data
  const form = event.target;
  const formData = new FormData(form);
  
  // Show progress
  const progressEl = document.getElementById('generation-progress');
  const resultsEl = document.getElementById('generation-results');
  
  if (progressEl && resultsEl) {
    progressEl.style.display = 'block';
    resultsEl.innerHTML = '';
  }

  // Submit using WordPress API Fetch
  const data = {
    action: 'modern_faker_generate'
  };

  // Convert FormData to object
  for (const [key, value] of formData.entries()) {
    data[key] = value;
  }

  // Special handling for certain form elements
  if (data.settings) {
    try {
      data.settings = JSON.parse(data.settings);
    } catch (e) {
      console.error('Error parsing settings JSON:', e);
    }
  }

  // Make the AJAX request
  jQuery.ajax({
    url: ajaxurl,
    method: 'POST',
    data: data,
    success: handleGenerationSuccess,
    error: handleGenerationError
  });
}

/**
 * Handle successful generation response
 * 
 * @param {Object} response The AJAX response
 */
function handleGenerationSuccess(response) {
  const progressEl = document.getElementById('generation-progress');
  const resultsEl = document.getElementById('generation-results');
  const actionsEl = document.querySelector('.lorem-press-results-actions');

  if (progressEl) {
    progressEl.style.display = 'none';
  }

  if (actionsEl) {
    actionsEl.style.display = 'block';
  }

  if (resultsEl) {
    if (response.success) {
      const results = response.data.results || [];
      const errors = response.data.errors || [];

      // Display successful results
      if (results.length > 0) {
        const resultsListEl = document.createElement('div');
        resultsListEl.className = 'lorem-press-results-list';

        results.forEach((result, index) => {
          const itemEl = document.createElement('div');
          itemEl.className = 'lorem-press-result-item';
          
          // Different display based on generator type
          let icon = 'admin-post';
          let type = 'item';
          
          switch (response.data.generator_type) {
            case 'post':
              icon = 'admin-post';
              type = 'post';
              break;
            case 'user':
              icon = 'admin-users';
              type = 'user';
              break;
            case 'term':
              icon = 'category';
              type = 'term';
              break;
            case 'comment':
              icon = 'admin-comments';
              type = 'comment';
              break;
          }
          
          let editLink = '';
          let title = result;
          
          if (response.data.edit_links && response.data.edit_links[index]) {
            editLink = response.data.edit_links[index];
          }
          
          if (response.data.titles && response.data.titles[index]) {
            title = response.data.titles[index];
          }
          
          itemEl.innerHTML = `
            <span class="dashicons dashicons-${icon}"></span>
            Created ${type}: ${editLink ? `<a href="${editLink}" target="_blank">${title}</a>` : title}
            (ID: ${result})
          `;
          
          resultsListEl.appendChild(itemEl);
        });

        resultsEl.appendChild(resultsListEl);
      }

      // Display errors
      if (errors.length > 0) {
        const errorsListEl = document.createElement('div');
        errorsListEl.className = 'lorem-press-errors-list';

        errors.forEach(error => {
          const errorEl = document.createElement('div');
          errorEl.className = 'lorem-press-error-item';
          errorEl.innerHTML = `<span class="dashicons dashicons-warning"></span> Error: ${error}`;
          errorsListEl.appendChild(errorEl);
        });

        resultsEl.appendChild(errorsListEl);
      }
    } else {
      // Display error message
      resultsEl.innerHTML = `
        <div class="lorem-press-error-message">
          <span class="dashicons dashicons-warning"></span>
          Error: ${response.data.message || 'Unknown error occurred.'}
        </div>
      `;
    }
  }
}

/**
 * Handle generation error
 * 
 * @param {Object} xhr The AJAX request object
 * @param {string} status The error status
 * @param {string} error The error message
 */
function handleGenerationError(xhr, status, error) {
  const progressEl = document.getElementById('generation-progress');
  const resultsEl = document.getElementById('generation-results');
  
  if (progressEl) {
    progressEl.style.display = 'none';
  }

  if (resultsEl) {
    resultsEl.innerHTML = `
      <div class="lorem-press-error-message">
        <span class="dashicons dashicons-warning"></span>
        Error: There was a problem communicating with the server.
      </div>
    `;
  }
}

/**
 * Initialize meta fields manager
 */
function initMetaFieldsManager() {
  // Add meta field
  const addMetaBtn = document.querySelector('.add-meta-field');
  if (addMetaBtn) {
    addMetaBtn.addEventListener('click', () => {
      const container = document.getElementById('meta-fields-container');
      if (container) {
        const firstRow = container.querySelector('.meta-field-row');
        if (firstRow) {
          const newRow = firstRow.cloneNode(true);
          
          // Reset values
          newRow.querySelectorAll('input, select').forEach(input => {
            input.value = '';
          });
          
          // Show remove button
          const removeBtn = newRow.querySelector('.remove-meta-field');
          if (removeBtn) {
            removeBtn.style.display = 'inline-block';
          }
          
          container.appendChild(newRow);
        }
      }
    });
  }

  // Remove meta field (delegated event)
  document.addEventListener('click', event => {
    if (event.target.classList.contains('remove-meta-field')) {
      const row = event.target.closest('.meta-field-row');
      if (row) {
        row.remove();
      }
    }
  });
}

/**
 * Initialize dynamic settings updates
 */
function initDynamicSettings() {
  // Toggle settings visibility based on checkboxes
  const toggles = {
    '#with-featured-image': '.featured-image-settings',
    '#with-terms': '.taxonomy-settings',
    '#with-meta': '.meta-settings',
    '#with-comments': '.comments-settings'
  };

  for (const [toggle, target] of Object.entries(toggles)) {
    const toggleEl = document.querySelector(toggle);
    const targetEl = document.querySelector(target);
    
    if (toggleEl && targetEl) {
      toggleEl.addEventListener('change', () => {
        targetEl.style.display = toggleEl.checked ? 'block' : 'none';
      });
    }
  }

  // Clear results button
  const clearResultsBtn = document.querySelector('.clear-results');
  if (clearResultsBtn) {
    clearResultsBtn.addEventListener('click', () => {
      const resultsEl = document.getElementById('generation-results');
      const noResultsEl = document.querySelector('.lorem-press-no-results');
      const actionsEl = document.querySelector('.lorem-press-results-actions');
      
      if (resultsEl) {
        resultsEl.innerHTML = '';
      }
      
      if (noResultsEl) {
        noResultsEl.style.display = 'block';
      }
      
      if (actionsEl) {
        actionsEl.style.display = 'none';
      }
    });
  }
}