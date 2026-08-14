<?php
require_once __DIR__.'/includes/bootstrap.php';
$publicTitle='Start Free with CuePOS | Snooker Club Software';
$publicDescription='Create a private CuePOS workspace for your Pakistani snooker club. Start free with one table and upgrade when you need more.';
$publicCanonical=siteUrl('/trial.php');
$selectedPlan=$_GET['plan']??'basic';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verifyCsrf();
    $owner=sanitize($_POST['owner_name']??'');
    $email=filter_var($_POST['email']??'',FILTER_VALIDATE_EMAIL);
    $password=$_POST['password']??'';
    $club=sanitize($_POST['club_name']??'');
    $country=strtoupper(substr(sanitize($_POST['country_code']??''),0,2));
    $timezone=sanitize($_POST['timezone']??'UTC');
    $currency=currencyForCountry($country);
    $planSlug=preg_replace('/[^a-z0-9_-]/','',strtolower($_POST['plan_slug']??'basic'));
    if(!$owner||!$email||!$club||strlen($password)<10){
        $error='Please complete all required fields and choose a password with at least 10 characters.';
    }elseif(!in_array($timezone,timezone_identifiers_list(),true)){
        $error='Choose a valid time zone for your club.';
    }else{
        try{
            $db=getDB();$db->beginTransaction();
            $p=$db->prepare('SELECT * FROM plans WHERE slug=? AND is_public=1 LIMIT 1');$p->execute([$planSlug]);$plan=$p->fetch();
            if(!$plan)throw new RuntimeException('That plan is not available.');$tenantStatus=$plan['slug']==='free'?'active':'trial';$trialEnds=$tenantStatus==='trial'?date('Y-m-d H:i:s',strtotime('+14 days')):null;
            $exists=$db->prepare('SELECT id FROM users WHERE email=? LIMIT 1');$exists->execute([$email]);
            if($exists->fetchColumn())throw new RuntimeException('An account already exists with that email. Please log in instead.');
            $base=slugify($club);$slug=$base;$n=2;$check=$db->prepare('SELECT id FROM clubs WHERE slug=? LIMIT 1');
            while(true){$check->execute([$slug]);if(!$check->fetchColumn())break;$slug=$base.'-'.$n++;}
            $token=bin2hex(random_bytes(16));
            $q=$db->prepare("INSERT INTO clubs(name,slug,address,phone,tv_token,country_code,timezone,locale,currency_code,currency_symbol,tenant_status,trial_ends_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $q->execute([$club,$slug,null,null,$token,$country?:null,$timezone,'en',$currency[0],$currency[1],$tenantStatus,$trialEnds]);
            $clubId=(int)$db->lastInsertId();
            $q=$db->prepare("INSERT INTO users(club_id,name,email,password,role,is_active) VALUES(?,?,?,?, 'owner',1)");$q->execute([$clubId,$owner,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $userId=(int)$db->lastInsertId();
            $db->prepare("INSERT INTO club_memberships(club_id,user_id,membership_role,status,joined_at) VALUES(?,?, 'owner','active',NOW())")->execute([$clubId,$userId]);
            $db->prepare("INSERT INTO subscriptions(club_id,plan_id,status,billing_interval,trial_ends_at,starts_at) VALUES(?,?,?,'monthly',?,NOW())")->execute([$clubId,$plan['id'],$tenantStatus,$trialEnds]);
            $db->prepare("INSERT INTO trial_signups(club_id,owner_user_id,email,plan_id,source,created_at) VALUES(?,?,?,?, 'website',NOW())")->execute([$clubId,$userId,$email,$plan['id']]);
            $db->prepare("INSERT INTO rate_plans(club_id,plan_name,billing_type,rate_per_minute) VALUES(?, 'Standard Rate','per_minute',10)")->execute([$clubId]);
            $rateId=(int)$db->lastInsertId();$tableLimit=min(6,(int)$plan['table_limit']);
            $t=$db->prepare("INSERT INTO tables(club_id,table_number,table_name,table_type,status,rate_plan_id) VALUES(?,?,?,'snooker','available',?)");
            for($i=1;$i<=$tableLimit;$i++)$t->execute([$clubId,(string)$i,'Table '.$i,$rateId]);
            $settings=$db->prepare('INSERT INTO settings(club_id,setting_key,setting_value) VALUES(?,?,?)');
            foreach(['onboarding_complete'=>'0','subscription_plan'=>$plan['slug'],'trial_notice'=>$tenantStatus==='trial'?'1':'0'] as $k=>$v)$settings->execute([$clubId,$k,$v]);
            $db->commit();session_regenerate_id(true);
            $_SESSION=['user_id'=>$userId,'user_name'=>$owner,'user_role'=>'owner','club_id'=>$clubId,'club_name'=>$club,'currency_symbol'=>$currency[1],'currency_code'=>$currency[0],'csrf_token'=>bin2hex(random_bytes(32))];
            header('Location: '.url('dashboard.php?welcome=trial'));exit;
        }catch(Throwable $e){
            if(isset($db)&&$db->inTransaction())$db->rollBack();
            $error=$e->getMessage();
        }
    }
}
require __DIR__.'/includes/public-header.php';
$plans=publicPlans();
?>
<main class="relative overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-[30rem] bg-gradient-to-br from-emerald-50 via-white to-teal-50 -z-10"></div>
    <div class="max-w-6xl mx-auto px-5 py-14 lg:py-20 grid lg:grid-cols-[.9fr_1.1fr] gap-10 xl:gap-16 items-start">
        <section class="lg:pt-8">
            <span class="eyebrow"><span class="w-2 h-2 bg-cue rounded-full"></span>Made for Pakistani snooker clubs</span>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[.98] tracking-tight mt-6">Your club deserves a <span class="text-cue">better operating system.</span></h1>
            <p class="text-slate-600 text-lg leading-8 mt-6 max-w-xl">Create a secure CuePOS workspace in a few minutes. Your tables, staff, players and daily operations stay private to your club.</p>
            <div class="mt-9 space-y-1 max-w-xl">
                <?php foreach([
                    ['fa-table-cells-large','A private workspace for your club','Your tables, rates and team data remain separate from every other club.'],
                    ['fa-bolt','Ready-to-use floor setup','Starter tables and a standard rate plan are created automatically.'],
                    ['fa-globe','Local settings from day one','Set your country and time zone; CuePOS applies the appropriate currency default.'],
                    ['fa-credit-card','No card during the trial','Explore the full workflow for 14 days before choosing what comes next.']
                ] as [$icon,$title,$copy]): ?>
                    <div class="trial-benefit">
                        <span class="trial-benefit-icon"><i class="fa-solid <?=$icon?>"></i></span>
                        <div><h2 class="font-bold text-[.94rem] text-ink"><?=$title?></h2><p class="text-sm text-slate-500 mt-1 leading-6"><?=$copy?></p></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-8 inline-flex gap-3 items-center rounded-2xl bg-ink px-4 py-3 text-sm text-slate-300 shadow-xl shadow-slate-900/10">
                <span class="w-8 h-8 grid place-items-center rounded-full bg-cue text-ink"><i class="fa-solid fa-shield-halved"></i></span>
                <span><b class="text-white">Built for snooker clubs.</b><br>Not a generic POS system.</span>
            </div>
        </section>

        <section class="form-shell">
            <div class="flex items-start justify-between gap-4 pb-6 border-b border-slate-200">
                <div><div class="flex items-center gap-3"><span class="form-step">1</span><span class="text-xs font-extrabold uppercase tracking-[.13em] text-cue-deep">Create your workspace</span></div><h2 class="font-display text-3xl font-bold tracking-tight mt-4">Create your CuePOS workspace</h2><p class="text-sm text-slate-500 leading-6 mt-2">Start free with one table, then choose Basic or Pro when your club needs more.</p></div>
                <a class="text-sm font-bold text-slate-600 hover:text-ink whitespace-nowrap" href="<?=url('login.php')?>">Already a member?</a>
            </div>
            <?php if($error): ?><div class="form-message form-message-error mt-6 flex gap-3"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><span><?=sanitize($error)?></span></div><?php endif; ?>
            <form method="post" class="mt-6">
                <input type="hidden" name="csrf_token" value="<?=csrfToken()?>">
                <div class="grid sm:grid-cols-2 gap-5">
                    <label class="field sm:col-span-2"><span class="field-label">Your full name <span class="field-hint">Required</span></span><input class="field-control" required autocomplete="name" name="owner_name" placeholder="For example, Hassan Ahmed" value="<?=sanitize($_POST['owner_name']??'')?>"></label>
                    <label class="field sm:col-span-2"><span class="field-label">Club name <span class="field-hint">Required</span></span><input class="field-control" required autocomplete="organization" name="club_name" placeholder="For example, Royal Snooker Club" value="<?=sanitize($_POST['club_name']??'')?>"></label>
                    <label class="field sm:col-span-2"><span class="field-label">Business email <span class="field-hint">Required</span></span><input class="field-control" required autocomplete="email" type="email" name="email" placeholder="you@yourclub.com" value="<?=sanitize($_POST['email']??'')?>"></label>
                    <label class="field sm:col-span-2"><span class="field-label">Create password <span class="field-hint">10+ characters</span></span><input class="field-control" required autocomplete="new-password" type="password" minlength="10" name="password" placeholder="Choose a secure password"></label>
                    <label class="field"><span class="field-label">Country code <span class="field-hint">2 letters</span></span><input class="field-control uppercase" maxlength="2" autocomplete="country" name="country_code" placeholder="PK" value="<?=sanitize($_POST['country_code']??'')?>"></label>
                    <label class="field"><span class="field-label">Your time zone</span><select class="field-control field-select" name="timezone"><option value="UTC">UTC</option><?php foreach(['Asia/Karachi','Europe/London','America/New_York','Asia/Dubai','Australia/Sydney'] as $tz):?><option <?=($_POST['timezone']??'')===$tz?'selected':''?> value="<?=$tz?>"><?=$tz?></option><?php endforeach?></select></label>
                    <label class="field sm:col-span-2"><span class="field-label">Choose your plan <span class="field-hint">Change anytime</span></span><select class="field-control field-select" name="plan_slug"><?php foreach($plans as $p):?><option <?=($selectedPlan===$p['slug']?'selected':'')?> value="<?=sanitize($p['slug'])?>"><?=sanitize($p['name'])?><?= $p['price_monthly']?' — Rs. '.number_format($p['price_monthly'],0).'/month':' — Free'?></option><?php endforeach?></select></label>
                </div>
                <button class="btn-public btn-public-primary w-full mt-7" type="submit">Create my workspace <i class="fa-solid fa-arrow-right"></i></button>
                <div class="form-trust"><i class="fa-solid fa-lock"></i><span>By continuing, you agree to the CuePOS <a class="marketing-link" href="<?=url('terms.php')?>">Terms</a> and <a class="marketing-link" href="<?=url('privacy.php')?>">Privacy Policy</a>. No payment information is needed for the Free Plan.</span></div>
            </form>
        </section>
    </div>
</main>
<?php require __DIR__.'/includes/public-footer.php'; ?>
