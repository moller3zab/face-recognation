<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\MenuItem;
use PDO;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }

    public function findByRole(string $role): array
    {
        $sql = "SELECT * FROM `menu_items` WHERE `is_active` = 1 AND (`role` = :role OR `role` = 'both') ORDER BY `sort_order` ASC, `id` ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':role' => $role]);

        $items = [];
        while ($row = $stmt->fetch()) {
            $items[] = $this->hydrate($row);
        }

        return $items;
    }

    public function getTreeByRole(string $role): array
    {
        $flatItems = $this->findByRole($role);
        $indexed = [];

        foreach ($flatItems as $item) {
            $indexed[$item->id] = $item;
        }

        $tree = [];
        foreach ($indexed as $id => $item) {
            if ($item->parentId !== null && isset($indexed[$item->parentId])) {
                $indexed[$item->parentId]->children[] = $item;
            } else {
                $tree[] = $item;
            }
        }

        return $tree;
    }

    private function hydrate(array $row): MenuItem
    {
        return new MenuItem(
            id: (int) $row['id'],
            parentId: $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
            title: $row['title'],
            url: $row['url'],
            icon: $row['icon'],
            role: $row['role'],
            sortOrder: (int) $row['sort_order'],
            isActive: (bool) $row['is_active']
        );
    }
}
