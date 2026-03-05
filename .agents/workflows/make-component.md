---
description: make-component
---

When I request the creation of a component [COMPONENT_NAME], please automatically create the following isolated structure:
1. UI File: Create `resources/views/components/[COMPONENT_NAME].blade.php`.
2. Style File: Create `resources/css/components/[COMPONENT_NAME].css` containing only the specific classes for this component.
3. Script File: Create `resources/js/components/[COMPONENT_NAME].js` for the logic.
4. Logic File (PHP): Create a Laravel View Component in `app/View/Components/[COMPONENT_NAME].php`.
5. Integration: Ensure that the Blade file automatically loads the CSS and JS using the `@vite` directive or the standard method used in this project.