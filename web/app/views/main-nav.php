<div class="navbar navbar-dark navbar-expand-md bg-dark mb-4" role="navigation">
	<a class="navbar-brand" href="<?= HTML::url('/') ?>"><?= UOJConfig::$data['profile']['oj-name-short'] ?></a>
	<button type="button" class="navbar-toggler collapsed" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="nav navbar-nav mr-auto">
			<li class="nav-item"><a class="nav-link" href="<?= HTML::url('/contests') ?>"><span class="glyphicon glyphicon-stats"></span> <?= UOJLocale::get('contests') ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?= HTML::url('/problems') ?>"><span class="glyphicon glyphicon-list-alt"></span> <?= UOJLocale::get('problems') ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?= HTML::url('/submissions') ?>"><span class="glyphicon glyphicon-tasks"></span> <?= UOJLocale::get('submissions') ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?= HTML::blog_list_url() ?>"><span class="glyphicon glyphicon-edit"></span> <?= UOJLocale::get('blogs') ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?= HTML::url('/faq') ?>"><span class="glyphicon glyphicon-info-sign"></span> <?= UOJLocale::get('help') ?></a></li>
		</ul>
		<ul class="nav navbar-nav float-right" role="tablist">
			<?php if (Auth::check()) : ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
						<span><?= Auth::id() ?></span> <?= $new_msg_tot_html ?>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li role="presentation"><a class="dropdown-item" href="<?= HTML::url('/user/profile/' . Auth::id()) ?>"><?= UOJLocale::get('my profile') ?></a></li>
						<li role="presentation"><a class="dropdown-item" href="<?= HTML::url('/user/msg') ?>"><?= UOJLocale::get('private message') ?>&nbsp;&nbsp;<?= $new_user_msg_num_html ?></a></li>
						<li role="presentation"><a class="dropdown-item" href="<?= HTML::url('/user/system-msg') ?>"><?= UOJLocale::get('system message') ?>&nbsp;&nbsp;<?= $new_system_msg_num_html ?></a></li>
						<?php if (isSuperUser(Auth::user())) : ?>
							<li role="presentation"><a class="dropdown-item" href="<?= HTML::url('/super-manage') ?>"><?= UOJLocale::get('system manage') ?></a></li>
						<?php endif ?>
					</ul>
				</li>
				<li class="nav-item" role="presentation"><a class="nav-link" href="<?= HTML::url('/logout?_token=' . crsf_token()) ?>"><?= UOJLocale::get('logout') ?></a></li>
			<?php else : ?>
				<li class="nav-item" role="presentation"><a class="nav-link" href="<?= HTML::url('/login') ?>"><?= UOJLocale::get('login') ?></a></li>
				<li class="nav-item" role="presentation"><a class="nav-link" href="<?= HTML::url('/register') ?>"><?= UOJLocale::get('register') ?></a></li>
			<?php endif ?>
		</ul>
	</div>
	<!--/.nav-collapse -->
</div>
<script type="text/javascript">
	var zan_link = '';
	$('#form-search-problem').submit(function(e) {
		e.preventDefault();

		url = '<?= HTML::url('/problems') ?>';
		qs = [];
		$(['search']).each(function() {
			if ($('#input-' + this).val()) {
				qs.push(this + '=' + encodeURIComponent($('#input-' + this).val()));
			}
		});
		if (qs.length > 0) {
			url += '?' + qs.join('&');
		}
		location.href = url;
	});
</script>