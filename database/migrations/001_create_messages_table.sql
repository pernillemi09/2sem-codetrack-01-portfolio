CREATE TABLE `messages`
(
    `id` integer PRIMARY KEY AUTOINCREMENT,
    `name` text NOT NULL,
    `email` text NOT NULL,
    `subject` text NOT NULL,
    `message` text NOT NULL,
    `read` integer NOT NULL DEFAULT 0,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP
);
