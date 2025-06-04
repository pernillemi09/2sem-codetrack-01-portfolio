<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\Database;
use App\Models\Message;
use PDO;

class MessageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Find the total number of messages.
     */
    public function count(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) as `total`
            FROM `messages`
        ");

        return (int) $stmt->fetch()['total'];
    }

    /**
     * Find the total number of unread messages.
     */
    public function countUnread(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS `total`
            FROM `messages`
            WHERE `read` = FALSE
        ");

        return (int) $stmt->fetch()['total'];
    }

    /**
     * Find all messages ordered by creation date.
     *
     * @return Message[]
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM `messages`
            ORDER BY `created_at` DESC
        ");

        return array_map(
            fn(array $row) => $this->createMessage($row),
            $stmt->fetchAll()
        );
    }

    /**
     * Find a message by its ID.
     */
    public function find(int $id): ?Message
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM `messages`
            WHERE `id` = ?
        ");

        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->createMessage($row) : null;
    }

    /**
     * Save a new message to the database.
     */
    public function create(string $name, string $email, string $subject, string $message): Message
    {
        $stmt = $this->db->prepare("
            INSERT INTO messages (`name`, `email`, `subject`, `message`)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$name, $email, $subject, $message]);

        return $this->find((int) $this->db->lastInsertId());
    }

    /**
     * Update the read status of a message.
     */
    public function updateReadStatus(int $id, bool $read): bool
    {
        $stmt = $this->db->prepare("
            UPDATE `messages`
            SET `read` = ?
            WHERE `id` = ?
        ");

        return $stmt->execute([$read ? 1 : 0, $id]);
    }

    /**
     * Delete a message.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM `messages`
            WHERE `id` = ?
        ");

        return $stmt->execute([$id]);
    }

    /**
     * Create a Message object from a database row.
     */
    private function createMessage(array $row): Message
    {
        return new Message(
            id: (int) $row['id'],
            name: $row['name'],
            email: $row['email'],
            subject: $row['subject'],
            message: $row['message'],
            created_at: $row['created_at'],
            read: (bool) $row['read'],
        );
    }
}
