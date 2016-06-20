<? echo '<?xml version="1.0" encoding="UTF-8" ?>' ?>

<message>
	<param>
		<action><?=$api_action?></action>
		<login><?=$api_login?></login>
		<password><?=$api_password?></password>
		<code><?=$art_number?></code>
		<? if ($brand):?><brand><?=$brand?></brand><? endif ?>
		<sm><?=$api_match_method?></sm>
	</param>
</message>
