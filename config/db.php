<?php
/** Shared CuePOS configuration for public marketing, tenant workspaces, and platform administration. */
define('DB_HOST', getenv('CUEPOS_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('CUEPOS_DB_NAME') ?: 'cuepos');
define('DB_USER', getenv('CUEPOS_DB_USER') ?: 'root');
define('DB_PASS', getenv('CUEPOS_DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('APP_URL', rtrim(getenv('CUEPOS_APP_URL') ?: '', '/'));
define('PUBLIC_SITE_URL', rtrim(getenv('CUEPOS_PUBLIC_SITE_URL') ?: (APP_URL ?: 'https://cuepos.com'), '/'));
define('APP_NAME', 'CuePOS');
define('BASE_PATH', dirname(__DIR__));

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('CuePOS database error: '.$e->getMessage());
            http_response_code(500);
            exit('Database connection failed. Check config/db.php.');
        }
    }
    return $pdo;
}
function startAppSession(): void { if (session_status() !== PHP_SESSION_ACTIVE) { session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')]); session_start(); } }
function sanitize(?string $value): string { return htmlspecialchars(trim(strip_tags((string)$value)), ENT_QUOTES, 'UTF-8'); }
function isLoggedIn(): bool { return !empty($_SESSION['user_id']) && !empty($_SESSION['club_id']); }
function requireLogin(): void { if (!isLoggedIn()) { header('Location: '.url('login.php')); exit; } header('X-Robots-Tag: noindex, nofollow', true); }
function isPlatformAdmin(): bool { return !empty($_SESSION['is_platform_admin']); }
function requirePlatformAdmin(): void { requireLogin(); if (!isPlatformAdmin()) { http_response_code(403); exit('You do not have permission to access the CuePOS platform console.'); } }
function requireRole($roles): void { if (!in_array($_SESSION['user_role'] ?? '', (array)$roles, true)) { http_response_code(403); exit('You do not have permission to access this area.'); } }
function url(string $path = ''): string { return (APP_URL ?: '') . '/' . ltrim($path, '/'); }
function siteUrl(string $path = ''): string { static $base = null; if ($base === null) { $configured = platformSetting('public_site_url'); $base = rtrim($configured ?: PUBLIC_SITE_URL, '/'); } return $base . '/' . ltrim($path, '/'); }
function jsonResponse(bool $success, string $message = '', array $data = []): void { header('Content-Type: application/json; charset=utf-8'); header('X-Robots-Tag: noindex, nofollow', true); echo json_encode(['success'=>$success, 'message'=>$message, 'data'=>$data]); exit; }
function csrfToken(): string { if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function verifyCsrf(): void { if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) jsonResponse(false, 'Your session expired. Refresh the page and try again.'); }
function money($amount): string { return ($_SESSION['currency_symbol'] ?? 'Rs.').' '.number_format((float)$amount, 0); }
function currentClub(): array { return ['id'=>(int)($_SESSION['club_id'] ?? 0), 'name'=>$_SESSION['club_name'] ?? APP_NAME]; }
function currencyForCountry(string $countryCode): array { $map=['PK'=>['PKR','Rs.'],'GB'=>['GBP','£'],'AE'=>['AED','AED'],'AU'=>['AUD','A$'],'CA'=>['CAD','C$'],'EU'=>['EUR','€'],'IN'=>['INR','₹']]; return $map[$countryCode] ?? ['USD','$']; }
function slugify(string $value): string { $slug=strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',iconv('UTF-8','ASCII//TRANSLIT',$value) ?: $value),'-')); return $slug ?: 'club'; }
function platformSetting(string $key, ?string $default = null): ?string { try { $q=getDB()->prepare('SELECT setting_value FROM platform_settings WHERE setting_key=? LIMIT 1');$q->execute([$key]);$value=$q->fetchColumn();return $value!==false?(string)$value:$default; } catch(Throwable $e) { return $default; } }
function publicPlans(): array { try { $q=getDB()->query('SELECT * FROM plans WHERE is_public=1 ORDER BY sort_order,id');$plans=$q->fetchAll();if($plans)return $plans; } catch(Throwable $e) {} return [['slug'=>'starter','name'=>'Starter','tagline'=>'For focused single-venue clubs','description'=>'Core tables, POS, players, café and reports.','price_monthly'=>29,'table_limit'=>6,'staff_limit'=>5],['slug'=>'pro','name'=>'Pro','tagline'=>'For high-traffic clubs','description'=>'Advanced reports, TV display and tournaments.','price_monthly'=>79,'table_limit'=>18,'staff_limit'=>15],['slug'=>'enterprise','name'=>'Enterprise','tagline'=>'For multi-branch operations','description'=>'Custom onboarding and enterprise operating controls.','price_monthly'=>0,'table_limit'=>100,'staff_limit'=>100]]; }
function audit(string $action, string $entityType, ?int $entityId = null, $old = null, $new = null): void { try { $stmt=getDB()->prepare('INSERT INTO audit_log (club_id,user_id,action,entity_type,entity_id,old_value,new_value,ip_address) VALUES (?,?,?,?,?,?,?,?)'); $stmt->execute([$_SESSION['club_id']??null,$_SESSION['user_id']??null,$action,$entityType,$entityId,$old?json_encode($old):null,$new?json_encode($new):null,$_SERVER['REMOTE_ADDR']??null]); } catch(Throwable $e) { error_log($e->getMessage()); } }
function tierDiscount(string $tier): float { return ['silver'=>5,'gold'=>10,'platinum'=>15][$tier] ?? 0; }
function calculateSlabBilling(float $elapsedMinutes, ?string $slabConfig): float { $slabs=json_decode($slabConfig ?: '[]', true) ?: []; usort($slabs, fn($a,$b)=>($a['up_to_minutes']??PHP_INT_MAX)<=>($b['up_to_minutes']??PHP_INT_MAX)); foreach($slabs as $slab){ if(isset($slab['up_to_minutes']) && $elapsedMinutes <= (float)$slab['up_to_minutes']) return (float)($slab['price']??0); } $base=end($slabs) ?: []; $baseMinutes=(float)($base['up_to_minutes']??0); $basePrice=(float)($base['price']??0); $extraMinutes=(float)($base['per_extra_minutes'] ?? 1); $extraPrice=(float)($base['extra_price'] ?? 0); return $basePrice + (ceil(max(0,$elapsedMinutes-$baseMinutes)/max(1,$extraMinutes))*$extraPrice); }
function activeShiftId(): ?int { $q=getDB()->prepare("SELECT id FROM shifts WHERE club_id=? AND status='open' ORDER BY id DESC LIMIT 1"); $q->execute([$_SESSION['club_id']]); return ($v=$q->fetchColumn()) ? (int)$v : null; }
