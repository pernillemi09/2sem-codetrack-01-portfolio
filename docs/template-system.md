# Template System

This guide explains how to use the templating system in the portfolio application.

## 1. Understanding the Template System

The portfolio application uses a simple yet powerful template system that allows you to:

- Extend a base layout for consistent page structure
- Define reusable sections for different content areas
- Include partial templates for components
- Pass dynamic data from controllers to views
- Create clean, maintainable templates

## 2. Creating Pages with Templates

To create a new page in the portfolio:

1. Create a controller method in an appropriate controller class
2. Create a view file using the template system
3. Set up routing to connect the URL to the controller method

### 2.1 Example: Creating a "Skills" Page

#### Controller Method

In `src/Controllers/HomeController.php` (or create a new controller):

```php
public function skills(Request $request): Response
{
    $skills = [
        ['name' => 'HTML/CSS', 'level' => 90],
        ['name' => 'JavaScript', 'level' => 85],
        ['name' => 'PHP', 'level' => 80],
        ['name' => 'MySQL', 'level' => 75],
    ];
    
    $response = new Response();
    $response->setTemplate($this->template, 'skills', [
        'skills' => $skills,
        ...$this->pullFlash($response),
        'request' => $request
    ]);
    return $response;
}
```

#### View File

Create `views/skills.view.php`:

```php
<?php
/** @var \App\Template $this */
/** @var array<array<string, mixed>> $skills */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */

$this->extend('layout');
?>

<?php $this->start('title', 'Skills') ?>

<section class="skills-section section-padding">
    <div class="container">
        <h1>My Skills</h1>
        
        <div class="skills-grid">
            <?php foreach ($skills as $skill): ?>
                <div class="skill-card">
                    <h3><?= htmlspecialchars($skill['name']) ?></h3>
                    <div class="skill-level">
                        <div class="skill-bar" style="width: <?= htmlspecialchars($skill['level']) ?>%"></div>
                        <span class="skill-percentage"><?= htmlspecialchars($skill['level']) ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
```

#### Route Configuration

In `public/index.php`:

```php
// Import the controller at the top of the file if needed
use App\Controllers\HomeController;

// Add the route
$router->get('/skills', [HomeController::class, 'skills']);
```

## 3. Template System Features

### 3.1 Extending Layouts

Every page view starts by extending the base layout:

```php
<?php
/** @var \App\Template $this */
$this->extend('layout');
?>
```

This must be called at the beginning of your view file.

### 3.2 Defining Sections

Sections allow you to define content for specific areas in the layout.

#### Multi-line Content Sections

For sections with multiple lines of content:

```php
<?php $this->start('content') ?>
    <section class="my-section">
        <div class="container">
            <h1>My Content</h1>
            <p>Content goes here...</p>
        </div>
    </section>
<?php $this->end() ?>
```

#### Simple One-line Sections

For simple one-line sections, you can use the shorthand:

```php
<?php $this->start('title', 'Page Title') ?>
```

### 3.3 Displaying Sections in Layouts

In the layout file, use the `section()` method to display section content:

```php
<title><?php $this->section('title') ?> - My Portfolio</title>

<main>
    <?php $this->section('content') ?>
</main>
```

## 4. Data Management

### 4.1 Passing Data to Views

Controllers pass data to views through the Response's `setTemplate()` method:

```php
$response = new Response();
$response->setTemplate($this->template, 'view-name', [
    'projects' => $projectRepository->findAll(),
    'featuredProject' => $projectRepository->findFeatured(),
    ...$this->pullFlash($response),  // Adds success/error messages
    'request' => $request  // Commonly passed in all views
]);
return $response;
```

### 4.2 Accessing Data in Views

Data is extracted into variables that can be accessed directly:

```php
<?php
/** @var \App\Template $this */
/** @var \App\Models\Project[] $projects */
/** @var \App\Models\Project|null $featuredProject */
?>

<h1>My Projects</h1>

<?php if ($featuredProject): ?>
    <div class="featured-project">
        <h2><?= htmlspecialchars($featuredProject->getTitle()) ?></h2>
    </div>
<?php endif; ?>
    
<div class="projects-grid">
    <?php foreach ($projects as $project): ?>
        <article class="project-card">
            <h3><?= htmlspecialchars($project->getTitle()) ?></h3>
        </article>
    <?php endforeach; ?>
</div>
```

## 5. Creating Reusable Components

### 5.1 Building Partial Templates

For reusable components, create partial templates in a dedicated directory:

```php
// views/partials/form-input.php
<?php
/** 
 * @var string $name Input field name
 * @var string $label Input field label
 * @var string $type Input type (default: text)
 * @var string|null $value Current input value
 * @var array<string, array<string>> $errors All form errors
 */

$type = $type ?? 'text';
$value = $value ?? '';
$hasError = isset($errors[$name]);
$errorClass = $hasError ? ' has-error' : '';
?>

<div class="form-group<?= $errorClass ?>">
    <label for="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($label) ?></label>
    <input 
        type="<?= htmlspecialchars($type) ?>" 
        id="<?= htmlspecialchars($name) ?>" 
        name="<?= htmlspecialchars($name) ?>"
        value="<?= htmlspecialchars($value) ?>"
    >
    <?php if ($hasError): ?>
        <p class="error-message"><?= htmlspecialchars($errors[$name][0]) ?></p>
    <?php endif; ?>
</div>
```

### 5.2 Including Partial Templates

Include partials using PHP's `require` statement:

```php
<?php
// Pass local variables to the partial
$name = 'email';
$label = 'Email Address';
$type = 'email';
$value = $request->get('email');

require __DIR__ . '/partials/form-input.php';
?>
```

## 6. Practical Examples

### 6.1 Navigation Links

To add a new link to the main navigation menu:

```php
<li>
    <a href="/skills" <?= $request->getPath() === '/skills' ? 'class="active"' : '' ?>>
        Skills
    </a>
</li>
```

### 6.2 Form Validation Errors

Display validation errors in forms:

```php
// In controller
// Create errors array - keys are field names, values are arrays of error messages
$errors = [
    'email' => ['Please enter a valid email address.'],
    'message' => ['Message is too short.']
];

// Flash errors to session and redirect
$this->flashErrors($response, $errors);
$response->redirect('/contact');

// When rendering the form
$response->setTemplate($this->template, 'contact', [
    ...$this->pullFlash($response), // Gets flashed errors
    'request' => $request
]);

// In view
<?php if (!empty($errors['email'])): ?>
    <p class="error-message"><?= htmlspecialchars($errors['email'][0]) ?></p>
<?php endif; ?>
```

### 6.3 Page-Specific CSS

Add CSS for new pages to `public/css/style.css`:

```css
/* Skills page styles */
.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.skill-card {
    padding: 1.5rem;
    background-color: #f9f9f9;
    border-radius: 0.5rem;
}

.skill-level {
    margin-top: 0.5rem;
    background-color: #e0e0e0;
    height: 0.5rem;
    border-radius: 0.25rem;
    overflow: hidden;
}

.skill-bar {
    height: 100%;
    background-color: var(--accent-color);
}
```

## 7. Template Best Practices

Follow these guidelines when working with the template system:

### Documentation
- Use PHPDoc comments to document available variables in views
- Document default variables expected by partial templates

### Security
- Always escape output with `htmlspecialchars()` to prevent XSS attacks
- Never trust user input displayed in templates

### Organization
- Keep business logic in controllers, not in views
- Break complex views into partial templates
- Use meaningful variable and section names
- Keep templates focused on presentation

### Code Style
- Maintain consistent indentation and formatting
- Follow a consistent naming convention for view files (`name.view.php`)
