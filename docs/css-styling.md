# CSS and Styling

This guide explains how to work with CSS in the portfolio application.

## 1. CSS File Organization

The CSS in this project is organized into several categories:

```
public/css/
├── style.css             # Main CSS file that imports all others
├── base/                 # Base styles and variables
│   ├── reset.css         # CSS reset
│   ├── typography.css    # Typography styles
│   └── variables.css     # CSS custom properties (variables)
├── components/           # Reusable component styles
│   ├── buttons.css
│   ├── cards.css
│   ├── forms.css
│   └── ...
├── layout/               # Layout components
│   ├── header.css
│   ├── footer.css
│   ├── grid.css
│   └── ...
└── pages/                # Page-specific styles
    ├── home.css
    ├── contact.css
    ├── projects.css
    └── admin/            # Admin-specific styles
        ├── dashboard.css
        └── messages.css
```

## 2. CSS Variables

The application uses CSS custom properties (variables) for consistent theming:

```css
/* variables.css */
:root {
    /* Colors */
    --color-primary: #4a6cf7;
    --color-primary-dark: #3f57d2;
    --color-secondary: #6c757d;
    --color-success: #28a745;
    --color-danger: #dc3545;
    --color-warning: #ffc107;
    --color-info: #17a2b8;
    --color-light: #f8f9fa;
    --color-dark: #343a40;
    
    /* Text colors */
    --color-text: #333;
    --color-text-light: #6c757d;
    --color-heading: #212529;
    
    /* Background colors */
    --color-bg: #fff;
    --color-bg-light: #f8f9fa;
    --color-bg-dark: #212529;
    --color-border: #dee2e6;
    
    /* Spacing */
    --space-xs: 0.25rem;  /* 4px */
    --space-sm: 0.5rem;   /* 8px */
    --space-md: 1rem;     /* 16px */
    --space-lg: 1.5rem;   /* 24px */
    --space-xl: 2rem;     /* 32px */
    --space-xxl: 3rem;    /* 48px */
    
    /* Typography */
    --font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-family-heading: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-base: 1rem;
    --font-size-sm: 0.875rem;
    --font-size-lg: 1.125rem;
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-bold: 700;
    --line-height: 1.5;
    
    /* Borders */
    --border-radius: 0.25rem;
    --border-radius-lg: 0.5rem;
    --border-radius-sm: 0.125rem;
    --border: 1px solid var(--color-border);
    
    /* Shadows */
    --box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    --box-shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
    --box-shadow-lg: 0 1rem 3rem rgba(0,0,0,.175);
    
    /* Transitions */
    --transition: all 0.3s ease;
}
```

## 3. Adding New Styles

### 3.1 For Existing Pages

Add your styles to the appropriate page CSS file.

For example, to modify the projects page:

```css
/* pages/projects.css */
.project-filter {
    display: flex;
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

.filter-button {
    padding: var(--space-xs) var(--space-sm);
    background: transparent;
    border: var(--border);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
}

.filter-button.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}
```

### 3.2 For New Pages

Create a new CSS file in the appropriate directory:

```css
/* pages/skills.css */
.skills-section {
    padding: var(--space-xl) 0;
}

.skill-category {
    margin-bottom: var(--space-lg);
}

.skill-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: var(--space-md);
    margin-top: var(--space-sm);
}

.skill-item {
    background: var(--color-bg);
    padding: var(--space-md);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow-sm);
}

.skill-bar {
    height: 8px;
    background: var(--color-bg-light);
    border-radius: 4px;
    margin-top: var(--space-xs);
    overflow: hidden;
}

.skill-progress {
    height: 100%;
    background: var(--color-primary);
    border-radius: 4px;
}
```

### 3.3 Import the New CSS

Don't forget to import your new CSS file in `style.css`:

```css
/* style.css */
@import 'pages/skills.css';
```

## 4. Responsive Design

The site uses a mobile-first approach. Use media queries for responsive designs:

```css
.container {
    width: 100%;
    padding: 0 var(--space-md);
    margin: 0 auto;
    
    /* Responsive breakpoints */
    @media (min-width: 576px) {
        max-width: 540px;
    }
    
    @media (min-width: 768px) {
        max-width: 720px;
    }
    
    @media (min-width: 992px) {
        max-width: 960px;
    }
    
    @media (min-width: 1200px) {
        max-width: 1140px;
    }
}
```

## 5. Using CSS Custom Properties for Theming

You can create dark mode or other themes by modifying CSS variables:

```css
/* Dark theme */
@media (prefers-color-scheme: dark) {
    :root {
        --color-bg: #121212;
        --color-bg-light: #1e1e1e;
        --color-text: #e0e0e0;
        --color-text-light: #a0a0a0;
        --color-heading: #ffffff;
        --color-border: #333;
    }
}
```

## 6. CSS Best Practices

1. **Use CSS variables** for colors, spacing, and other repeated values
2. **Follow a naming convention** (BEM is recommended)
3. **Organize styles** by component and page
4. **Use comments** to document complex styles
5. **Keep specificity low** to avoid conflicts
6. **Minimize nesting** for better performance
7. **Use relative units** like `rem` and `%` for better accessibility
8. **Test on multiple devices** and browsers

## 7. Performance Tips

1. **Minimize CSS files** for production
2. **Avoid inline styles** whenever possible
3. **Use efficient selectors** (avoid universal selectors like `*`)
4. **Consider loading strategies** for large CSS files
5. **Optimize critical CSS** for faster initial loading
