<?php

use Illuminate\Support\Facades\Storage;

/**
 * removes trailing quotes from stings passed in
 */
if (! function_exists('removeTrailingQuotes')) {
    function removeTrailingQuotes($string):string
    {
        $str = ltrim($string, '"');
        $str = rtrim($str, '"');
        return $str;
    }
}

/**
 * Generates dynamic codes for polygons
 * @param int $id
 * @return string
 */
if (! function_exists('generateCodeForPolygon')) {
function generateCodeForPolygon($id,$parent_id)
{
    $zone = \App\Models\Zone::whereId($id)->with('parent', 'ancestors')->first();

    $zoneParent = $zone->parent()->first(['id','code','parent_id']);

    $zoneAncestor = $zone->ancestors()->first(['id','code']);

    $firstPolyInZone = \App\Models\Polygon::whereZoneId($zone->id)->with('parent')->first();

    $polyParent = \App\Models\Polygon::whereId($parent_id)->with('children')->first();

    $firstChildOfPolyParent = $polyParent ? $polyParent->children()->first() : null;

    $secondChildPolyOfParent = [];

    $children = $polyParent ? $polyParent->children()->get() : [];
    foreach($children as $key => $child) {
        if ($key === 1) {
            $secondChildPolyOfParent = $child;
        break;
        }
    }
    return Sprintf('%s-%s-%s-%s-%s-%s',
        $zoneAncestor->code ?? '',
        $zoneParent->code ?? '',
        $zone->code ?? '',
        $firstPolyInZone->id ?? '',
        $secondChildPolyOfParent['id'] ?? '',
        $firstChildOfPolyParent->id ?? ''
    );
}
}

if (! function_exists('renderDropdown')) {
    function renderDropdown($data)
    {
        if (array_key_exists('slug', $data) && $data['slug'] === 'dropdown') {
            echo '<li class="c-sidebar-nav-dropdown">';
            echo '<a class="c-sidebar-nav-dropdown-toggle" href="javascript:;">';

            if ($data['hasIcon'] === true && $data['iconType'] === 'coreui') {
                echo '<i class="' . $data['icon'] . ' c-sidebar-nav-icon"></i>';
            }

            echo $data['name'] . '</a>';
            echo '<ul class="c-sidebar-nav-dropdown-items">';

            renderDropdown($data['elements']);

            echo '</ul></li>';
        } else {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['slug'] === 'link') {
                    echo '<li class="c-sidebar-nav-item">';
                    echo '<a class="c-sidebar-nav-link" href="' . $data[$i]['href'] . '">';
                    echo '<span class="c-sidebar-nav-icon"></span>' . $data[$i]['name'] . '</a></li>';
                } elseif ($data[$i]['slug'] === 'dropdown') {
                    renderDropdown($data[$i]);
                }
            }
        }
    }
}

if (! function_exists('get_inputmask')) {
    function get_inputmask($format)
    {
        switch ($format) {

            case '%':
                return "99";

            case '$':
                return "9999999999";
        }

        return '9999999999';
    }
}

if (! function_exists('is_part_of')) {
    function is_part_of($array1, $array2, $is_any = false)
    {
        if (! is_array($array1)) {
            return in_array($array1, $array2);
        }

        foreach ($array1 as $row) {
            if ($is_any) {
                if (in_array($row, $array2)) {
                    return true;
                }
            } else {
                if (! in_array($row, $array2)) {
                    return false;
                }
            }
        }

        return $is_any ? false : true;
    }
}

if (! function_exists('generate_random_colors')) {
    function generate_random_colors($n = 1)
    {
        if ($n < 1) {
            return null;
        }
        $colors = [];

        for ($i=0; $i < $n; $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        return $n == 1 ? $colors[0] : $colors;
    }
}

if (! function_exists('generate_slug_by_path')) {
    function generate_slug_by_path($path) {
        if (! $path) return false;

        $str = '';
        $slugs = explode('/', $path);

        foreach ($slugs as $index => $slug) {
            $slugs[$index] = Illuminate\Support\Str::slug($slug);
        }

        $str = implode('/', $slugs);
        $i = 2;

        while (App\Models\SEOUrl::wherePath($str)->exists()) {
            $str = $str . '-' . $i;
            $i++;
        }

        return $str;
    }
}

if (! function_exists('is_a_number')) {
    function is_a_number($int) {
        return filter_var($int, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== false;
    }
}

if (! function_exists('getZipCodes')) {
    function getZipCodes() {
        return json_decode(Storage::disk('local')->get("zips.json"), true);
    }
}

if (! function_exists('serialize_links_from_request')) {
    function serialize_links_from_request (?array $input_links) {
        if (!$input_links) {
            return null;
        }

        $labels = $input_links['label'];
        $links = [];

        foreach ($labels as $index => $label) {
            $url = $input_links['url'][$index];

            if (!$label && !$url) {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url
            ];
        }

        return serialize($links);
    }
}

if (! function_exists('get_menu_link')) {
    function get_menu_link(string $url) {
        $parsedUrl = parse_url($url);
        $parsedAppUrl = parse_url(config('app.url'));

        if ($parsedUrl['host'] == $parsedAppUrl['host']) {
            return $url;
        }

        $newUrl = $parsedUrl['path'];

        if ($parsedUrl['query'] ?? false) {
            $newUrl .= '?'.$parsedUrl['query'];
        }

        return $newUrl;

        return url();
    }
}

if (!function_exists('generate_random_password')) {
    function generate_random_password($length = 8)
    {
        // Define character sets without '0', 'O', and symbols
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNPQRSTUVWXYZ'; // Exclude 'O'
        $numbers = '123456789'; // Exclude '0'

        // Ensure the password contains at least one character from each set
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        // Fill the remaining length with random characters from all sets
        $allCharacters = $lowercase . $uppercase . $numbers;
        for ($i = 3; $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        // Shuffle the password to avoid predictable patterns
        return str_shuffle($password);
    }
}

if (!function_exists('complete_the_url')) {
    function complete_the_url(string $prefix, ?string $url): ?string
    {
        if ($url) {
            if (!str_starts_with($url, '/')) {
                $url = '/' . $url;
            }
            $url = $prefix . $url;
        }

        return $url;
    }
}
