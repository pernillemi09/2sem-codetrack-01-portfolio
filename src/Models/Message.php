<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a contact form message in the system.
 */
class Message
{
    /**
     * @param int $id Unique identifier for the message
     * @param string $name Name of the person who sent the message
     * @param string $email Email address of the sender
     * @param string $subject Subject line of the message
     * @param string $message Content of the message
     * @param bool $read Whether the message has been read
     * @param string $created_at Timestamp when the message was created
     */
    public function __construct(
        private readonly int $id,
        private string $name,
        private string $email,
        private string $subject,
        private string $message,
        private readonly string $created_at,
        private bool $read = false,
    ) {
    }

    /**
     * Get the message ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the sender's name.
     */
    public function getName(): string
    {
        return $this->name ?? '<no name>';
    }

    /**
     * Get the sender's email.
     */
    public function getEmail(): string
    {
        return $this->email ?? '<no email>';
    }

    /**
     * Get the message subject.
     */
    public function getSubject(): string
    {
        return $this->subject ?? '<no subject>';
    }

    /**
     * Get the message content.
     */
    public function getMessage(): string
    {
        return $this->message ?? '<no message>';
    }

    /**
     * Get the creation timestamp.
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * Check if the message has been read.
     */
    public function getIsRead(): bool
    {
        return $this->read;
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead(): void
    {
        $this->read = true;
    }

    /**
     * Mark the message as unread.
     */
    public function markAsUnread(): void
    {
        $this->read = false;
    }

    /**
     * Toggle the read status of the message.
     */
    public function toggleRead(): void
    {
        $this->read = !$this->read;
    }

    /**
     * Convert the message to an array representation.
     * 
     * @return array{
     *   id: int,
     *   name: string,
     *   email: string,
     *   subject: string,
     *   message: string,
     *   read: bool,
     *   created_at: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'read' => $this->read,
            'created_at' => $this->created_at,
        ];
    }
}
