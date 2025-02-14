<?php

namespace SquareBracket;

class UserData
{
    private \Core\Database $database;
    private $data;
    private $followers;
    private $is_banned;

    public function __construct(\Core\Database $database, $id)
    {
        $this->database = $database;
        $this->data = $this->database->fetch("SELECT u.* FROM users u WHERE u.id = ?", [$id]);
        $this->followers = $this->database->fetch("SELECT COUNT(user) FROM subscriptions WHERE user = ?", [$id])['COUNT(user)'];
        $this->is_banned = $this->database->fetch("SELECT * FROM bans WHERE userid = ?", [$id]);
    }

    public function isUserBanned()
    {
        if ($this->is_banned) { return true; }
        return false;
    }

    public function getUserArray(): array
    {
        return [
            "username" => $this->data["name"],
            "displayname" => $this->data["title"],
            "color" => $this->data["customcolor"],
            "followers" => $this->followers,
            "joined" => $this->data["joined"],
            "connected" => $this->data["lastview"],
        ];
    }
}