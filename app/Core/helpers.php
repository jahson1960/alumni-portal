<?php

use App\Core\Csrf;

function url(string $path = ''): string
{
    $base = defined('BASE_URL') ? BASE_URL : '';
    $path = ltrim($path, '/');
    return rtrim($base, '/') . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Same as asset(), but appends a ?v=<mtime> cache-buster so browsers pick up rebuilds immediately. */
function versioned_asset(string $path): string
{
    $relative = ltrim($path, '/');
    $absolute = ROOT_PATH . '/assets/' . $relative;
    $version = is_file($absolute) ? (string) filemtime($absolute) : '1';
    return asset($relative) . '?v=' . $version;
}

/** @return array{0:float,1:float,2:float} [hue 0-360, saturation 0-100, lightness 0-100] */
function hex_to_hsl(string $hex): array
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $l = ($max + $min) / 2;

    if ($max === $min) {
        return [0.0, 0.0, round($l * 100, 2)];
    }

    $d = $max - $min;
    $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
    $h = match ($max) {
        $r => (($g - $b) / $d) + ($g < $b ? 6 : 0),
        $g => (($b - $r) / $d) + 2,
        default => (($r - $g) / $d) + 4,
    };
    $h *= 60;

    return [round($h, 2), round($s * 100, 2), round($l * 100, 2)];
}

function hsl_to_hex(float $h, float $s, float $l): string
{
    $h = fmod($h, 360) / 360;
    $s = max(0, min(100, $s)) / 100;
    $l = max(0, min(100, $l)) / 100;

    if ($s === 0.0) {
        $r = $g = $b = $l;
    } else {
        $hue2rgb = function (float $p, float $q, float $t): float {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1 / 2) return $q;
            if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
            return $p;
        };
        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        $r = $hue2rgb($p, $q, $h + 1 / 3);
        $g = $hue2rgb($p, $q, $h);
        $b = $hue2rgb($p, $q, $h - 1 / 3);
    }

    return sprintf('#%02x%02x%02x', (int) round($r * 255), (int) round($g * 255), (int) round($b * 255));
}

/** A valid 6-digit hex color, or the given default if the input isn't one. */
function valid_hex_color(?string $value, string $default): string
{
    $value = (string) $value;
    return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $default;
}

/** 50/100/200/600/700/900 hex shade ramp derived from one base hex color. */
function color_ramp(string $baseHex): array
{
    [$h, $s, $l] = hex_to_hsl($baseHex);
    return [
        '50' => hsl_to_hex($h, $s, $l + (100 - $l) * 0.94),
        '100' => hsl_to_hex($h, $s, $l + (100 - $l) * 0.86),
        '200' => hsl_to_hex($h, $s, $l + (100 - $l) * 0.66),
        '600' => hsl_to_hex($h, $s, $l),
        '700' => hsl_to_hex($h, $s, $l * 0.82),
        '900' => hsl_to_hex($h, $s, $l * 0.55),
    ];
}

/** "#rrggbb" -> "r g b" (space-separated, for CSS rgb(var(--x) / <alpha>) usage). */
function hex_to_rgb_triplet(string $hex): string
{
    $hex = ltrim($hex, '#');
    return hexdec(substr($hex, 0, 2)) . ' ' . hexdec(substr($hex, 2, 2)) . ' ' . hexdec(substr($hex, 4, 2));
}

/**
 * Builds the :root CSS custom-property block for the whole admin-configurable theme:
 * header/dropdown geometry, plus color ramps for the three themeable surfaces (menu accents,
 * buttons/CTAs, the alumni map). In "unified" mode all three surfaces share one base color;
 * in "custom" mode each has its own. Buttons are exposed as an RGB triplet so Tailwind's
 * `gold`/`gold-hover` tokens (see tailwind.config.js) can support opacity modifiers like bg-gold/10.
 */
function theme_style(array $settings, int $headerHeight = 64, int $dropdownOffset = 4): string
{
    $mode = ($settings['theme_mode'] ?? 'unified') === 'custom' ? 'custom' : 'unified';
    $accent = valid_hex_color($settings['theme_accent_color'] ?? null, '#d49326');

    if ($mode === 'unified') {
        $menuColor = $btnColor = $mapColor = $accent;
    } else {
        $menuColor = valid_hex_color($settings['menu_accent_color'] ?? null, '#d49326');
        $btnColor = valid_hex_color($settings['theme_button_color'] ?? null, '#d49326');
        $mapColor = valid_hex_color($settings['theme_map_color'] ?? null, '#d49326');
    }

    $headerHeight = max(48, min(120, $headerHeight));
    $dropdownOffset = max(-20, min(40, $dropdownOffset));

    $vars = [];
    foreach (color_ramp($menuColor) as $shade => $hex) {
        $vars["--menu-accent-$shade"] = $hex;
    }
    foreach (color_ramp($mapColor) as $shade => $hex) {
        $vars["--map-$shade"] = $hex;
    }
    $btnRamp = color_ramp($btnColor);
    $vars['--btn-600-rgb'] = hex_to_rgb_triplet($btnRamp['600']);
    $vars['--btn-700-rgb'] = hex_to_rgb_triplet($btnRamp['700']);

    $vars['--header-height'] = $headerHeight . 'px';
    $vars['--dropdown-offset'] = $dropdownOffset . 'px';

    $css = ':root{';
    foreach ($vars as $name => $value) {
        $css .= $name . ':' . $value . ';';
    }
    $css .= '}';

    return $css;
}

function upload_url(?string $path): ?string
{
    if (!$path) {
        return null;
    }
    return asset('uploads/' . ltrim($path, '/'));
}

/**
 * A deterministic {bg,text} pastel Tailwind class pair picked from a fixed palette based on the
 * given seed string — the same seed always maps to the same pair, giving cards varied-but-stable
 * colors (e.g. avatar initials, category badges) without storing a color per row.
 */
function palette_classes(string $seed): array
{
    static $palette = [
        ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
        ['bg' => 'bg-violet-100', 'text' => 'text-violet-700'],
        ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
        ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
        ['bg' => 'bg-rose-100', 'text' => 'text-rose-700'],
        ['bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
        ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-700'],
        ['bg' => 'bg-fuchsia-100', 'text' => 'text-fuchsia-700'],
    ];
    return $palette[crc32($seed) % count($palette)];
}

/**
 * @return array{percent:int, sections:array<string,bool>} completion state per Edit Profile tab, used by the
 * "Profile Completion" ring/checklist. A section counts as complete once its core fields are filled in.
 */
function profile_completion(array $user, array $education, array $experience): array
{
    $filled = fn (?string $v): bool => trim((string) $v) !== '';

    $sections = [
        'profile_information' => $filled($user['name'] ?? null) && $filled($user['headline'] ?? null) && $filled($user['bio'] ?? null),
        'education' => ($filled($user['program'] ?? null) && !empty($user['graduation_year'])) || !empty($education),
        'experience' => ($filled($user['headline'] ?? null) && $filled($user['company'] ?? null)) || !empty($experience),
        'skills' => $filled($user['skills'] ?? null) && $filled($user['expertise_areas'] ?? null),
        'interests' => $filled($user['business_interests'] ?? null) || $filled($user['personal_interests'] ?? null),
        'contact' => $filled($user['city'] ?? null) && $filled($user['country'] ?? null) && $filled($user['phone'] ?? null),
        'links' => $filled($user['linkedin_url'] ?? null) || $filled($user['personal_website'] ?? null) || $filled($user['twitter_url'] ?? null),
        'privacy' => !empty($user['show_email']) || !empty($user['show_phone']),
    ];

    $percent = (int) round((count(array_filter($sections)) / count($sections)) * 100);

    return ['percent' => $percent, 'sections' => $sections];
}

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** One-line blurb shown on the Directory's "By Programme" summary cards. */
function programme_description(string $program): string
{
    static $known = [
        'MBA' => 'Develop your business acumen and leadership potential.',
        'Executive MBA' => 'For experienced professionals seeking advanced leadership skills.',
        'Global MBA' => 'A globally-minded MBA for leaders operating across borders.',
        'DBA' => 'Doctoral-level research training for scholar-practitioners.',
        'MSc Finance' => 'Advanced knowledge in finance and investment management.',
        'MSc Marketing Management' => 'Strategic marketing for today\'s competitive landscape.',
        'MSc International Business' => 'Global perspective. Local impact.',
    ];
    return $known[$program] ?? ('Advance your career with RBSN\'s ' . $program . ' programme.');
}

/** ISO 3166-1 alpha-2 code for a free-text country name, or null if not recognized (map just skips it). */
function country_iso2(string $country): ?string
{
    static $map = [
        'Nigeria' => 'NG', 'Ghana' => 'GH', 'Kenya' => 'KE', 'South Africa' => 'ZA',
        'United States' => 'US', 'USA' => 'US', 'United Kingdom' => 'GB', 'UK' => 'GB',
        'Canada' => 'CA', 'United Arab Emirates' => 'AE', 'UAE' => 'AE', 'India' => 'IN',
        'Egypt' => 'EG', 'Morocco' => 'MA', 'Rwanda' => 'RW', 'Uganda' => 'UG',
        'Tanzania' => 'TZ', 'Ethiopia' => 'ET', 'Senegal' => 'SN', 'Ivory Coast' => 'CI',
        'Cote d\'Ivoire' => 'CI', 'Germany' => 'DE', 'France' => 'FR', 'Australia' => 'AU',
        'China' => 'CN', 'Netherlands' => 'NL', 'Switzerland' => 'CH', 'Qatar' => 'QA',
        'Saudi Arabia' => 'SA', 'Zambia' => 'ZM', 'Zimbabwe' => 'ZW', 'Cameroon' => 'CM',
        'Ireland' => 'IE', 'Spain' => 'ES', 'Italy' => 'IT', 'Brazil' => 'BR',
        'Singapore' => 'SG', 'Malaysia' => 'MY', 'Botswana' => 'BW', 'Namibia' => 'NA',
    ];
    return $map[$country] ?? null;
}

/**
 * Flag image markup for an ISO 3166-1 alpha-2 code, via flagcdn.com.
 * Unicode regional-indicator flag emoji (the old approach here) don't render as flags on
 * Windows — most Windows font builds show the raw two-letter code instead — so an actual
 * image is used for reliable cross-platform rendering.
 */
function country_flag_html(string $iso2, string $class = 'w-6 h-[18px] rounded-sm object-cover shadow-sm'): string
{
    $iso2 = strtolower($iso2);
    if (strlen($iso2) !== 2) {
        return '';
    }
    return '<img src="https://flagcdn.com/24x18/' . e($iso2) . '.png" '
        . 'srcset="https://flagcdn.com/48x36/' . e($iso2) . '.png 2x" '
        . 'width="24" height="18" alt="" loading="lazy" class="' . e($class) . '">';
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . Csrf::token() . '">';
}

function flash_get(string $key): ?string
{
    if (!empty($_SESSION['_flash'][$key])) {
        $msg = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
    return null;
}

function errors_get(): array
{
    $errors = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors']);
    return $errors;
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_filter($parts));
    return implode('', array_slice($letters, 0, 2)) ?: '?';
}

function format_date(?string $date, string $format = 'M j, Y'): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : '';
}

function avatar_html(array $user, string $classes = 'w-9 h-9'): string
{
    $name = $user['name'] ?? '?';
    $inner = !empty($user['avatar'])
        ? '<img src="' . e(upload_url($user['avatar'])) . '" alt="' . e($name) . '" class="' . $classes . ' rounded-full object-cover">'
        : '<div class="' . $classes . ' rounded-full bg-primary-navy text-white flex items-center justify-center text-xs font-bold flex-shrink-0">' . e(initials($name)) . '</div>';

    if (!array_key_exists('last_active_at', $user)) {
        return $inner;
    }

    $online = \App\Models\User::isOnline($user['last_active_at']);
    // data-online-dot marks this for the shared live-refresh poll (see online-status.js) —
    // whichever page renders it doesn't need to wire that up itself.
    $idAttr = isset($user['id']) ? ' data-online-dot="' . (int) $user['id'] . '"' : '';
    $dot = '<span' . $idAttr . ' class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white ' . ($online ? 'bg-green-500' : 'bg-slate-300') . '" title="' . ($online ? 'Online' : 'Offline') . '"></span>';
    return '<span class="relative inline-block flex-shrink-0">' . $inner . $dot . '</span>';
}

function online_status_html(?string $lastActiveAt, ?int $userId = null): string
{
    $online = \App\Models\User::isOnline($lastActiveAt);
    $idAttr = $userId !== null ? ' data-online-text="' . $userId . '"' : '';
    if ($online) {
        return '<span' . $idAttr . ' class="inline-flex items-center gap-1 text-green-600"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Online</span>';
    }
    return '<span' . $idAttr . ' class="inline-flex items-center gap-1 text-slate-400"><span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>Offline</span>';
}

function job_logo_html(array $job, string $classes = 'w-10 h-10'): string
{
    $mode = $job['logo_display_mode'] ?? 'logo';
    if ($mode !== 'badge' && !empty($job['company_logo'])) {
        return '<img src="' . e($job['company_logo']) . '" alt="' . e($job['company']) . '" class="' . $classes . ' object-contain rounded border border-slate-200 bg-white p-1 flex-shrink-0">';
    }
    $bg = e($job['company_bg_color'] ?? '#f8fafc');
    $color = e($job['company_text_color'] ?? '#091a2e');
    $initials = e(mb_substr((string) $job['company'], 0, 4));
    return '<div class="' . $classes . ' rounded border border-slate-200 flex items-center justify-center text-[0.65rem] font-bold flex-shrink-0" style="background: ' . $bg . '; color: ' . $color . ';">' . $initials . '</div>';
}

function location_display(array $user): string
{
    $city = trim((string) ($user['city'] ?? ''));
    $country = trim((string) ($user['country'] ?? ''));
    if ($city !== '' && $country !== '') {
        return "{$city}, {$country}";
    }
    return $city !== '' ? $city : $country;
}

/**
 * Render a comma-separated free-text field (skills, expertise, interests) as badge chips.
 */
function tag_list_html(?string $commaSeparated, string $class = 'badge-blue'): string
{
    $items = array_filter(array_map('trim', explode(',', (string) $commaSeparated)));
    if (!$items) {
        return '';
    }
    $html = '';
    foreach ($items as $item) {
        $html .= '<span class="' . $class . ' mr-1 mb-1 inline-block">' . e($item) . '</span>';
    }
    return $html;
}

function category_badges_html(array $categories, string $class = 'badge-blue'): string
{
    if (!$categories) {
        return '';
    }
    $html = '';
    foreach ($categories as $cat) {
        $html .= '<span class="' . $class . ' mr-1">' . e($cat['name']) . '</span>';
    }
    return $html;
}

function article_category_label(string $category): string
{
    $labels = [
        'article' => 'Article',
        'research' => 'Research',
        'case_study' => 'Case Study',
        'white_paper' => 'White Paper',
    ];
    return $labels[$category] ?? 'Article';
}

/** Derives the admin-configurable page key (if any) that a nav URL points to, by inspecting its path segments. */
function url_page_key(string $fullUrl): string
{
    static $known = ['directory', 'connections', 'feed', 'messages', 'mentorship', 'businesses', 'jobs', 'companies', 'events', 'events_mine', 'knowledge', 'resources', 'benefits', 'give', 'news', 'spotlight', 'map', 'cohorts'];

    $base = rtrim(defined('BASE_URL') ? BASE_URL : '', '/');
    $path = $base !== '' && str_starts_with($fullUrl, $base) ? substr($fullUrl, strlen($base)) : $fullUrl;
    $path = ltrim($path, '/');
    $path = explode('?', $path)[0];
    $path = explode('#', $path)[0];
    $segments = explode('/', $path);
    $segment = $segments[0] ?? '';

    if ($segment === 'events' && ($segments[1] ?? '') === 'mine') {
        return 'events_mine';
    }

    return in_array($segment, $known, true) ? $segment : '';
}

/** Pulls the ?tab= or ?view= value (whichever is present) out of a nav URL, for tab-level visibility checks. */
function url_tab_key(string $fullUrl): ?string
{
    $query = parse_url($fullUrl, PHP_URL_QUERY);
    if (!$query) {
        return null;
    }
    parse_str($query, $params);
    return $params['tab'] ?? $params['view'] ?? null;
}

/** Lowercase, hyphenated slug of arbitrary text, for building stable link_visibility keys. */
function slugify(string $text): string
{
    $slug = strtolower($text);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

/** Stable per-link visibility key: page_key (or "other") + a slug of the link's own title. */
function link_visibility_key(string $pageKey, string $title): string
{
    return ($pageKey !== '' ? $pageKey : 'other') . ':' . slugify($title);
}

function mega_item(string $url, string $icon, string $title, string $description): string
{
    $pageKey = url_page_key($url);
    if (!\App\Models\PageVisibility::shouldShowInNav($pageKey, \App\Core\Auth::user())) {
        return '';
    }
    $tabKey = url_tab_key($url);
    if ($tabKey !== null && !\App\Models\PageTabVisibility::isVisible($pageKey, $tabKey)) {
        return '';
    }
    if (!\App\Models\LinkVisibility::isVisible(link_visibility_key($pageKey, $title))) {
        return '';
    }

    return '<a href="' . e($url) . '" class="mega-item">'
        . '<span class="mega-item-icon"><i class="' . e($icon) . '"></i></span>'
        . '<span class="min-w-0"><span class="mega-item-title">' . e($title) . '</span>'
        . '<span class="mega-item-desc">' . e($description) . '</span></span>'
        . '</a>';
}

function mega_col_heading(string $title): string
{
    return '<h4 class="mega-col-title">' . e($title) . '</h4><div class="mega-col-rule"></div>';
}

function reading_time_minutes(string $html): int
{
    $words = str_word_count(strip_tags($html));
    return max(1, (int) ceil($words / 200));
}

function post_type_label(string $type): string
{
    $labels = [
        'general' => 'Update',
        'achievement' => 'Achievement',
        'announcement' => 'Announcement',
        'question' => 'Question',
        'job_opportunity' => 'Job Opportunity',
        'event' => 'Event',
        'partnership' => 'Partnership',
    ];
    return $labels[$type] ?? 'Update';
}

/** Groups a notification's `type` into one of the sidebar categories on the Notifications page. */
function notification_category(string $type): string
{
    $map = [
        'new_message' => 'messages',
        'connection_request' => 'connections',
        'connection_accepted' => 'connections',
        'new_follower' => 'connections',
        'mentorship_request' => 'mentorship',
        'mentorship_accepted' => 'mentorship',
        'mentorship_declined' => 'mentorship',
        'resume_review_claimed' => 'mentorship',
        'resume_review_completed' => 'mentorship',
    ];
    return $map[$type] ?? 'system';
}

/** Bootstrap icon for a notification's `type`, shown in its row badge. */
function notification_icon(string $type): string
{
    $map = [
        'new_message' => 'bi-envelope',
        'connection_request' => 'bi-person-plus',
        'connection_accepted' => 'bi-person-check',
        'new_follower' => 'bi-person-heart',
        'mentorship_request' => 'bi-mortarboard',
        'mentorship_accepted' => 'bi-mortarboard',
        'mentorship_declined' => 'bi-mortarboard',
        'post_liked' => 'bi-heart',
        'post_commented' => 'bi-chat-dots',
        'job_pending' => 'bi-briefcase',
        'job_approved' => 'bi-briefcase',
        'job_rejected' => 'bi-briefcase',
        'resume_review_claimed' => 'bi-file-earmark-text',
        'resume_review_completed' => 'bi-file-earmark-check',
    ];
    return $map[$type] ?? 'bi-gear';
}

/** Font Awesome icon for a Career Resource's format (Guide/Template/Report/Video/PPTX). */
function resource_type_icon(?string $type): string
{
    $map = [
        'Guide' => 'fa-file-lines',
        'Template' => 'fa-file-pen',
        'Report' => 'fa-chart-line',
        'Video' => 'fa-video',
        'PPTX' => 'fa-file-powerpoint',
    ];
    return $map[$type ?? ''] ?? 'fa-file-lines';
}

/** Bootstrap icon for a Career Resources / Interview Prep category, shown in the sidebar list. */
function resource_category_icon(string $category): string
{
    $map = [
        'Business' => 'bi-briefcase',
        'Career' => 'bi-compass',
        'Personal Development' => 'bi-emoji-smile',
        'Behavioral Questions' => 'bi-chat-square-text',
        'Technical Questions' => 'bi-code-slash',
        'Case Interview Prep' => 'bi-diagram-3',
        'Salary Negotiation' => 'bi-cash-coin',
    ];
    return $map[$category] ?? 'bi-bookmark';
}

/** Font Awesome icon for an event category, shown on event cards. Uncategorized events cycle through a fallback set by $index so every card still reads as distinct. */
function event_category_icon(?string $category, int $index = 0): string
{
    $map = [
        'Global Events' => 'fa-globe',
        'Reunions' => 'fa-people-roof',
        'Executive Programmes' => 'fa-gem',
        'Webinars' => 'fa-display',
    ];
    if (isset($map[$category ?? ''])) {
        return $map[$category];
    }
    $fallback = ['fa-people-group', 'fa-house', 'fa-briefcase', 'fa-rocket', 'fa-person', 'fa-champagne-glasses'];
    return $fallback[$index % count($fallback)];
}

/** @return array{border:string,bg:string,text:string} Tailwind accent classes for an event category (or a cycling fallback for uncategorized events). */
function event_category_color(?string $category, int $index = 0): array
{
    $map = [
        'Global Events' => ['border' => 'border-l-amber-400', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        'Reunions' => ['border' => 'border-l-emerald-400', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        'Executive Programmes' => ['border' => 'border-l-indigo-400', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'Webinars' => ['border' => 'border-l-sky-400', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
    ];
    if (isset($map[$category ?? ''])) {
        return $map[$category];
    }
    $fallback = [
        ['border' => 'border-l-violet-400', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
        ['border' => 'border-l-teal-400', 'bg' => 'bg-teal-100', 'text' => 'text-teal-600'],
        ['border' => 'border-l-rose-400', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
        ['border' => 'border-l-fuchsia-400', 'bg' => 'bg-fuchsia-100', 'text' => 'text-fuchsia-600'],
        ['border' => 'border-l-cyan-400', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-600'],
        ['border' => 'border-l-orange-400', 'bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
    ];
    return $fallback[$index % count($fallback)];
}

/** Font Awesome icon for a giving campaign, matched by keywords in its title (or a cycling fallback). */
function campaign_icon(string $title, int $index = 0): string
{
    $t = mb_strtolower($title);
    if (str_contains($t, 'leadership')) {
        return 'fa-user';
    }
    if (str_contains($t, 'innovation') || str_contains($t, 'lab')) {
        return 'fa-flask';
    }
    if (str_contains($t, 'emergency') || str_contains($t, 'relief')) {
        return 'fa-hand-holding-heart';
    }
    if (str_contains($t, 'scholarship') || str_contains($t, 'mba')) {
        return 'fa-graduation-cap';
    }
    if (str_contains($t, 'entrepreneur')) {
        return 'fa-rocket';
    }
    if (str_contains($t, 'outreach') || str_contains($t, 'rural') || str_contains($t, 'community')) {
        return 'fa-globe';
    }
    $fallback = ['fa-seedling', 'fa-hands-holding-child', 'fa-people-group'];
    return $fallback[$index % count($fallback)];
}

/** @return array{bg:string,text:string,badge:string} Tailwind accent classes for a giving campaign, matched by title keywords. */
function campaign_color(string $title, int $index = 0): array
{
    $t = mb_strtolower($title);
    if (str_contains($t, 'leadership')) {
        return ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-700'];
    }
    if (str_contains($t, 'innovation') || str_contains($t, 'lab')) {
        return ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700'];
    }
    if (str_contains($t, 'emergency') || str_contains($t, 'relief')) {
        return ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'badge' => 'bg-amber-100 text-amber-700'];
    }
    if (str_contains($t, 'scholarship') || str_contains($t, 'mba')) {
        return ['bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'badge' => 'bg-sky-100 text-sky-700'];
    }
    if (str_contains($t, 'entrepreneur')) {
        return ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'badge' => 'bg-violet-100 text-violet-700'];
    }
    if (str_contains($t, 'outreach') || str_contains($t, 'rural') || str_contains($t, 'community')) {
        return ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'badge' => 'bg-teal-100 text-teal-700'];
    }
    $fallback = [
        ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'badge' => 'bg-rose-100 text-rose-700'],
        ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'badge' => 'bg-cyan-100 text-cyan-700'],
        ['bg' => 'bg-fuchsia-100', 'text' => 'text-fuchsia-600', 'badge' => 'bg-fuchsia-100 text-fuchsia-700'],
    ];
    return $fallback[$index % count($fallback)];
}

function time_ago(?string $datetime): string
{
    if (!$datetime) {
        return '';
    }
    $diff = time() - strtotime($datetime);
    if ($diff < 3600) {
        return max(1, (int) floor($diff / 60)) . 'm ago';
    }
    if ($diff < 86400) {
        return (int) floor($diff / 3600) . 'h ago';
    }
    if ($diff < 604800) {
        return (int) floor($diff / 86400) . 'd ago';
    }
    return (int) floor($diff / 604800) . 'w ago';
}
