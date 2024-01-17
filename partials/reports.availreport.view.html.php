<?php

$form = (new CForm())->setName('availreport_view');

$table = (new CTableInfo());

$view_url = $data['view_curl']->getUrl();
print_r($data['filter']['from']);
// $arr = explode('&', $view_url, -1);
// // $key = array_search('from=', $arr);

// // print($arr['from']);
// // $test = strrchr( $view_url, '&from='); //returns ".jpg"
// // print_r(($arr[1]));

// // print_r($arr[2]);
// $test = str_replace("from=", "", $arr[1]);
// print($test);
// // if (array_key_exists('from', $arr)) {
// // }
// // if (str_contains($view_url, 'from=')) {

// // }

$table->setHeader([
	(new CColHeader(_('Host'))),
	(new CColHeader(_('Name'))),
	(new CColHeader(_('Problems'))),
	(new CColHeader(_('Ok'))),
	(new CColHeader(_('Tags')))
]);

$allowed_ui_problems = CWebUser::checkAccess(CRoleHelper::UI_MONITORING_PROBLEMS);
$triggers = $data['triggers'];

$tags = makeTags($triggers, true, 'triggerid', ZBX_TAG_COUNT_DEFAULT);
foreach ($triggers as &$trigger) {
	$trigger['tags'] = $tags[$trigger['triggerid']];
}
unset($trigger);

foreach ($triggers as $trigger) {
	$table->addRow([
		$trigger['host_name'],
		$allowed_ui_problems
			? new CLink($trigger['description'],
				(new CUrl('zabbix.php'))
					->setArgument('action', 'problem.view')
					->setArgument('filter_name', '')
					->setArgument('triggerids', [$trigger['triggerid']])
			)
			: $trigger['description'],
		($trigger['availability']['true'] < 0.00005)
			? ''
			: (new CSpan(sprintf('%.4f%%', $trigger['availability']['true'])))->addClass(ZBX_STYLE_RED),
		($trigger['availability']['false'] < 0.00005)
			? ''
			: (new CSpan(sprintf('%.4f%%', $trigger['availability']['false'])))->addClass(ZBX_STYLE_GREEN),
		$trigger['tags']
	]);
}

$form->addItem([$table,	$data['paging']]);

echo $form;
?>
