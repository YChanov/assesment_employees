<?php

class HelperFunctions
{
    public static function stripTagsContent($text, $tags = '', $invert = FALSE) {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    public static function renderProfilesFromArray($profiles = []) {
        foreach ($profiles as $profile) {
            if (!empty($profile['bio'])) {
                $profileBio = HelperFunctions::stripTagsContent($profile['bio'], '<script>', true);
                $profileBio = strip_tags($profileBio);
            }
            if (!empty($profileBio)) {
                $textProfileBio = '<text class="profile-bio"> Bio: ' . $profileBio . ' </text>';
            } else {
                $textProfileBio = '<text class="profile-bio"> No Bio </text>';
            }
            echo '<li>
        <div class="container-profile">
            <div class="profile-image">
                <img class="profile-img-soruce" src="' . $profile['avatar'] . '" onerror="imgError(this);" alt="">
            </div>
            <div class="profile-data">
                <span class="profile-name"> ' . $profile['name'] . ' </span>
                <span class="profile-title"> ' . $profile['title'] . ' </span>
                <span class="profile-company"> ' . $profile['company'] . ' </span>
                ' . $textProfileBio . '
            </div>
        </div>
    </li>';
        }
    }

    public static function renderMainPagination($total_pages, $currentPage) {

        $initialPageCount = min($total_pages, 9);
        $pages_links = array();

        $tmp = $initialPageCount;
        if ($tmp < $currentPage || $currentPage > $initialPageCount) {
            $tmp = 2;
        }
        for ($i = 1; $i <= $tmp; $i++) {
            $pages_links[$i] = $i;
        }

        if ($currentPage > $initialPageCount && $currentPage <= ($total_pages - $initialPageCount + 2)) {
            for ($i = $currentPage - 3; $i <= $currentPage + 3; $i++) {
                if ($i > 0 && $i < $total_pages) {
                    $pages_links[$i] = $i;
                }
            }
        }

        $tmp = $total_pages - $initialPageCount + 1;
        if ($tmp > $currentPage - 2) {
            $tmp = $total_pages - 1;
        }

        for ($i = $tmp; $i <= $total_pages; $i++) {
            if ($i > 0) {
                $pages_links[$i] = $i;
            }
        }

        $prev = 0;
        $style_active = '';

        foreach ($pages_links as $p) {
            if (($p - $prev) > 1) {
                if ($prev < 3) {
                    echo '<a href="?page=' . ($p - 1) . ' ">...</a>';
                } else {
                    echo '<a href="?page=' . ($prev + 1) . '">...</a>';
                }
            }
            $prev = $p;

            if ($p == $currentPage) {
                $style_active = 'class="active-page"';
            }

            echo '<a ' . $style_active . ' href="?page=' . $p . '" >' . $p . '</a>';

            $style_active = '';
        }
    }

    public static function renderPaginationWithPrevAndNextLinks($currentPage, $totalPages) {
        $page_prev = $currentPage > 1 ? $currentPage - 1 : '#';
        $page_next = $currentPage < $totalPages ? $currentPage + 1 : '#';

        echo '<div class="pright">
            <a href="?page=' . $page_prev . '"> &laquo; Prev</a>
        </div>';

        self::renderMainPagination($totalPages, $currentPage);

        echo '<div class="pleft">
            <a href="?page=' . $page_next . '">Next &raquo; </a>
        </div>';
    }
}