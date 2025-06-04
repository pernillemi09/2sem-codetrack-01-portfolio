# Database and Repositories

This guide explains how to work with the database layer in the portfolio application.

## 1. Repository Pattern Overview

The portfolio application uses the Repository pattern to abstract database operations:

- **Models**: Represent data entities (e.g., Message, Project)
- **Repositories**: Handle database operations for specific model types
- **Controllers**: Use repositories to access and manipulate data

This separation provides cleaner code and easier testing.

## 2. Creating a New Database Table

### 2.1 Creating a Migration File

The portfolio application uses SQL migration files in the `database/migrations/` directory. To create a new table, create a new SQL file following the naming convention:

Create a file named `003_create_skills_table.sql` in the `database/migrations/` directory:

```sql
CREATE TABLE `skills`
(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `name` text NOT NULL,
    `category` text NOT NULL,
    `proficiency` integer NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample data
INSERT INTO skills (name, category, proficiency) VALUES ('HTML', 'Frontend', 90);
INSERT INTO skills (name, category, proficiency) VALUES ('CSS', 'Frontend', 85);
INSERT INTO skills (name, category, proficiency) VALUES ('JavaScript', 'Frontend', 80);
INSERT INTO skills (name, category, proficiency) VALUES ('PHP', 'Backend', 95);
INSERT INTO skills (name, category, proficiency) VALUES ('SQL', 'Backend', 85);
```

> **Important**: The filename must start with a sequential number (like `001_`, `002_`, etc.) followed by a descriptive name, and end with `.sql`. This ensures migrations run in the correct order.

### 2.2 Running the Migration

The portfolio comes with a migration system that automatically runs all SQL migration files:

```bash
php bin/migrate.php
```

This command:
1. Creates a `migrations` table to track which migrations have been applied
2. Finds all SQL files in `database/migrations/` that haven't been applied yet
3. Executes each SQL file in numerical order
4. Records each completed migration in the `migrations` table

## 3. Creating a Model

Create a model class in `src/Models/Skill.php`:

```php
<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a skill in the system.
 */
class Skill
{
    /**
     * @param int $id Unique identifier for the skill
     * @param string $name Name of the skill
     * @param string $category Category the skill belongs to
     * @param int $proficiency Proficiency level (0-100)
     * @param string $created_at Timestamp when the skill was created
     */
    public function __construct(
        private readonly int $id,
        private string $name,
        private string $category,
        private int $proficiency,
        private readonly string $created_at,
    ) {
    }

    /**
     * Get the skill ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the skill name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the skill category.
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get the proficiency level.
     */
    public function getProficiency(): int
    {
        return $this->proficiency;
    }

    /**
     * Get the creation timestamp.
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
}
```

## 4. Creating a Repository

Create a repository in `src/Repositories/SkillRepository.php`:

```php
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use App\Models\Skill;
use PDO;

class SkillRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Find all skills ordered by category and name.
     *
     * @return Skill[]
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM `skills`
            ORDER BY `category`, `name`
        ");

        return array_map(
            fn(array $row) => $this->createSkill($row),
            $stmt->fetchAll()
        );
    }

    /**
     * Find skills by category.
     *
     * @return Skill[]
     */
    public function findByCategory(string $category): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM `skills`
            WHERE `category` = ?
            ORDER BY `name`
        ");

        $stmt->execute([$category]);

        return array_map(
            fn(array $row) => $this->createSkill($row),
            $stmt->fetchAll()
        );
    }

    /**
     * Find a skill by its ID.
     */
    public function find(int $id): ?Skill
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM `skills`
            WHERE `id` = ?
        ");

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->createSkill($row) : null;
    }

    /**
     * Create a new skill.
     */
    public function create(string $name, string $category, int $proficiency): Skill
    {
        $stmt = $this->db->prepare("
            INSERT INTO skills (`name`, `category`, `proficiency`)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$name, $category, $proficiency]);

        return $this->find((int) $this->db->lastInsertId());
    }

    /**
     * Update a skill's properties.
     */
    public function update(int $id, string $name, string $category, int $proficiency): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `skills`
            SET `name` = ?, `category` = ?, `proficiency` = ?
            WHERE `id` = ?
        ");

        return $stmt->execute([$name, $category, $proficiency, $id]);
    }

    /**
     * Delete a skill.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM `skills`
            WHERE `id` = ?
        ");

        return $stmt->execute([$id]);
    }

    /**
     * Create a Skill instance from database row.
     */
    private function createSkill(array $row): Skill
    {
        return new Skill(
            id: (int) $row['id'],
            name: $row['name'],
            category: $row['category'],
            proficiency: (int) $row['proficiency'],
            created_at: $row['created_at'],
        );
    }
}
```

## 5. Using Your Model and Repository in Controllers

Now you can use your repository in controllers:

```php
public function skills(Request $request): Response
{
    $skillsRepository = new SkillRepository();
    $skills = $skillsRepository->findAll();
    
    // Group skills by category
    $groupedSkills = [];
    foreach ($skills as $skill) {
        $groupedSkills[$skill->getCategory()][] = $skill;
    }
    
    $response = new Response();
    $response->setTemplate($this->template, 'skills', [
        'groupedSkills' => $groupedSkills
    ]);
    return $response;
}
```

## 6. Using Data in Views

In your view file, you can now access the data:

```php
<?php foreach ($groupedSkills as $category => $skills): ?>
    <div class="skill-category">
        <h2><?= htmlspecialchars($category) ?></h2>
        <ul class="skill-list">
            <?php foreach ($skills as $skill): ?>
                <li>
                    <span class="skill-name"><?= htmlspecialchars($skill->getName()) ?></span>
                    <div class="skill-bar">
                        <div class="skill-progress" style="width: <?= $skill->getProficiency() ?>%"></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
```

## 7. Best Practices

- Always use prepared statements to prevent SQL injection
- Keep database logic in repositories, not in controllers
- Use models to represent and validate data
- Add appropriate type-hints and PHPDoc comments
- Create meaningful method names that reflect business logic
