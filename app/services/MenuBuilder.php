<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuItem;
use App\Repositories\MenuItemRepository;
use App\Repositories\MenuItemRepositoryInterface;

class MenuBuilder
{
    private MenuItemRepositoryInterface $menuRepo;

    public function __construct(?MenuItemRepositoryInterface $menuRepo = null)
    {
        $this->menuRepo = $menuRepo ?? new MenuItemRepository();
    }

    public function buildMenu(string $role, string $currentUri): string
    {
        $tree = $this->menuRepo->getTreeByRole($role);
        return $this->renderMenuTree($tree, $currentUri);
    }

    private function renderMenuTree(array $items, string $currentUri, bool $isSubmenu = false): string
    {
        if (empty($items)) {
            return '';
        }

        $ulClass = $isSubmenu ? 'submenu-list' : 'sidebar-menu';
        $html = sprintf('<ul class="%s">', $ulClass);

        if (!$isSubmenu) {
            $html .= '<li class="menu-label">Main Navigation</li>';
        }

        foreach ($items as $item) {
            /** @var MenuItem $item */
            $isActive = $this->isUriActive($item->url, $currentUri);
            $activeClass = $isActive ? ' active' : '';

            $html .= '<li class="menu-item">';
            
            $iconSvg = $this->getIconSvg($item->icon);
            $fullUrl = str_starts_with($item->url, 'http') ? $item->url : BASE_URL . $item->url;

            $html .= sprintf(
                '<a href="%s" class="%s">%s %s</a>',
                htmlspecialchars($fullUrl, ENT_QUOTES, 'UTF-8'),
                $isSubmenu ? 'submenu-link' . $activeClass : 'menu-link' . $activeClass,
                $iconSvg,
                htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8')
            );

            // Recursive rendering for submenus
            if (!empty($item->children)) {
                $html .= $this->renderMenuTree($item->children, $currentUri, true);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
        return $html;
    }

    private function isUriActive(string $itemUrl, string $currentUri): bool
    {
        $itemPath = parse_url($itemUrl, PHP_URL_PATH) ?: $itemUrl;
        $currentPath = parse_url($currentUri, PHP_URL_PATH) ?: $currentUri;
        $normalizedItem = rtrim($itemPath, '/');
        $normalizedCurrent = rtrim($currentPath, '/');

        if ($normalizedItem === $normalizedCurrent) {
            return true;
        }

        return !empty($normalizedItem) && $normalizedItem !== '/admin' && str_starts_with($normalizedCurrent, $normalizedItem);
    }

    private function getIconSvg(?string $iconKey): string
    {
        if (empty($iconKey)) {
            return '';
        }

        // Inline SVG map for menu items
        $svgs = [
            'dashboard' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
            'users' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'clipboard-list' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
            'chart-bar' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
            'pencil-alt' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
            'bell' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
            'cog' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'user-circle' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
            'home' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'camera' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 10l-4 4l-2 -2"/><path d="M12 21a9 9 0 1 0 0 -18a9 9 0 0 0 0 18z"/></svg>',
            'user-check' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'history' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'file-text' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'upload' => '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>'
        ];

        return $svgs[$iconKey] ?? '<svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>';
    }
}
