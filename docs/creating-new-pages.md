# Creating New Pages

This guide explains the process of adding new pages to your portfolio application.

## 1. Understanding the MVC Flow

The portfolio application follows the Model-View-Controller (MVC) pattern:

- **Routes**: Connect URLs to controller methods
- **Controllers**: Process requests and prepare data
- **Views**: Display the data to users using the template system

## 2. Adding a New Page: Step by Step

### 2.1 Adding a Route

Routes connect URLs to controller methods. All routes are defined in `public/index.php`.

```php
// Add this line in public/index.php after other routes
$router->get('/skills', [HomeController::class, 'skills']);
```

Routes can also have dynamic parameters using the `{paramName}` syntax:

```php
$router->get('/project/{id}', [ProjectController::class, 'show']);
```

These dynamic parameters allow you to:

1. **Capture values from the URL**: The `{id}` part will match any value in that position
   - Example: `/project/5` will match and `id` will have the value `5`
   - Example: `/project/portfolio-website` will match and `id` will have the value `portfolio-website`

2. **Access parameters in your controller**: The router extracts these values and passes them as method arguments
   ```php
   // Controller method receives route parameters as arguments
   public function show(Request $request, string $id): Response
   {
       // The $id parameter is passed directly from the route
       
       // Find the project using the ID
       $project = $this->projects->find((int) $id);
       
       // ...rest of your controller code
   }
   ```

3. **Create dynamic URLs in your views**:
   ```php
   <a href="/project/<?= $project->getId() ?>">View Project</a>
   ```

### 2.2 Creating a Controller Method

Controllers are responsible for handling requests and returning responses.

If you're adding a page related to existing functionality, add a method to an existing controller:

```php
// In src/Controllers/HomeController.php
public function skills(Request $request): Response
{
    $response = new Response();
    $skills = [
        'Frontend' => ['HTML', 'CSS', 'JavaScript'],
        'Backend' => ['PHP', 'Node.js', 'Python']
    ];
    
    $response->setTemplate($this->template, 'skills', [
        'skills' => $skills,
        'pageTitle' => 'My Skills'
    ]);
    return $response;
}
```

For completely new functionality, you might create a new controller:

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controller;
use App\Http\Request;
use App\Http\Response;

class SkillsController extends Controller
{
    public function index(Request $request): Response
    {
        $response = new Response();
        $skills = [
            'Frontend' => ['HTML', 'CSS', 'JavaScript'],
            'Backend' => ['PHP', 'Node.js', 'Python']
        ];
        
        $response->setTemplate($this->template, 'skills', [
            'skills' => $skills
        ]);
        return $response;
    }
}
```

### 2.3 Creating a View

Views are responsible for displaying data to users. All views are stored in the `views/` directory.

Create a new view file, e.g., `views/skills.view.php`:

```php
<?php
/** @var \App\Template $this */
/** @var array $skills */

$this->extend('layout');
?>

<?php $this->start('title', 'My Skills') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-heading">
            My Skills
        </h1>
    </div>
</section>

<section class="skills-section">
    <div class="container">
        <?php foreach ($skills as $category => $list): ?>
            <div class="skill-category">
                <h2><?= htmlspecialchars($category) ?></h2>
                <ul class="skill-list">
                    <?php foreach ($list as $skill): ?>
                        <li><?= htmlspecialchars($skill) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>
```

### 2.4 Adding CSS (Optional)

If your page needs custom styling, add CSS to `public/css/pages/skills.css`:

```css
.skills-section {
    padding: var(--space-xl) 0;
}

.skill-category {
    margin-bottom: var(--space-lg);
}

.skill-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-md);
    margin-top: var(--space-sm);
}

.skill-list li {
    background: var(--color-bg);
    padding: var(--space-xs) var(--space-sm);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow-sm);
}
```

Then import this CSS in your layout file or directly in your view.

## 3. Testing Your New Page

After completing these steps:

1. Visit your new page at the URL you defined
2. Verify that all data is displayed correctly
3. Test responsive behavior on different screen sizes
4. Check for any styling issues

## 4. Common Issues and Solutions

### Page Not Found

- Check if the route is correctly defined in `public/index.php`
- Ensure the controller method exists and is spelled correctly

### Undefined Variables

- Make sure all variables used in views are passed from the controller
- Add type hints in PHPDoc comments using `/** @var type $name */`

### Styling Problems

- Check if CSS is properly linked
- Use browser developer tools to inspect element styles
