<?php declare(strict_types=1);
namespace CRM\Utils;

use CRM\Utils\UIFactoryMethod;
use CRM\Utils\Bx24Rest;
use CRM\Utils\Security;

require_once(__DIR__ . '/config.php');

$tpl_page = 'main';

$ui = (new UIFactoryMethod(__DIR__ .'/tpl/'. $tpl_page))->createUI();

// Объект с данными для шаблона
$tdto = new \stdClass;

$tdto->title = 'Замена ответственных за контакты компаний в коробке';

// флаг запуска скрипта
$start = $argv[1] ?? (int) Security::getRequestData('start') ?? 0; 

// Первое открытие страницы без параметров GET
if (! $start)
{
	$ui->render_no_argument($tdto);
	$ui->close_output();
	exit(0);
}

// База данных в коробке
$db_gd = new Bx24Rest(WEB_HOOK_URL_BOX);

$result = [];

$limit         = 50; // записей в одном запросе
$limit_smaller = 45; // для прерываний после стольких запросов

$current_call = 0; // номер текущего запроса до сброса

$counter = 1; // счётчик обработанных записей
$result  = []; // обнуляем массив результатов запроса

$found_companies = 0; // счётчик дублей:
$added_companies = 0; // реально добавленные

$lead_id = 0;

$time_start = microtime(true);
$tdto->msg  = $ui->message('Начало: ' . $time_start . LINE_BREAK . LINE_BREAK);
$ui->render($tdto);

if (ob_get_level() > 0)
{
	ob_flush();
}

// Проходим в цикле по всем сотрудникам выборками по 50 человек
// Подставляем ID сотрудников (по 50 ID) в фильтр поиска компаний, 
// где данные сотрудники в поле ASSIGNED_BY_ID
// Проходим в цикле по всем контактам каждой компании, 
// и заменяем contact.ASSIGNED_BY_ID на company.ASSIGNED_BY_ID


function updateContacts(object $db_handle, array $contacts_to_change): array
{
	$result = [];
	
	$batch_params = [];

	foreach ($contacts_to_change as $id => $assigned_by_id)
	{
		$batch_params[$id] =
		[
			'method' => 'crm.contact.update',
			'params' => ['ID' => $id, 'fields' => ['ASSIGNED_BY_ID' => $assigned_by_id]]
		];
	}

	// апдейт контактов
	$result = $db_handle->callBatch($batch_params, 1); // halt_on_errors

	//echo "batch_params\n";

	//print_r($batch_params);
	return $result;
}

// Проходим в цикле по всем сотрудникам выборками по 50 человек

// массив из 50 пар ID=>ASSIGNED_BY_ID для batch update
$contacts_to_change = [];

$total_contacts_changed = 0;
$total_company_found    = 0;

$user_params =
[
	'FILTER' => ['USER_TYPE' => 'employee']
];

foreach ($db_gd->bx24Get50('user.get', $user_params) as $res)
{
	$users_50 = [];

	foreach ($res['result'] as $k => $v)
	{
		array_push($users_50, $res['result'][$k]['ID']);
	}

	// Подставляем ID сотрудников (по 50 ID) в фильтр поиска компаний,
	// где данные сотрудники в поле ASSIGNED_BY_ID
	$company_params =
	[
		'filter' => ['ASSIGNED_BY_ID' => $users_50],
		'select' => ['ID', 'ASSIGNED_BY_ID']
	];

	$res_company = [];

	$batch_contact_params = [];

	foreach($db_gd->bx24Get50('crm.company.list', $company_params) as $res_company)
	{
		// Проходим в цикле по всем контактам каждой компании,
		// и заменяем contact.ASSIGNED_BY_ID на company.ASSIGNED_BY_ID
		
		$companies_50 = []; // ID => ASSIGNED_BY_ID

		foreach ($res_company['result'] as $k => $v)
		{
			$contact_params =
			[
			'filter' => ['COMPANY_ID' => $res_company['result'][$k]['ID']],
			'select' => ['ID', 'COMPANY_ID', 'ASSIGNED_BY_ID']
			];

			$total_company_found++;

			$batch_contact_params[$res_company['result'][$k]['ID']] =
			[
			'method' => 'crm.contact.list',
			'params' => $contact_params
			];
			
			$companies_50[$res_company['result'][$k]['ID']] = 
				$res_company['result'][$k]['ASSIGNED_BY_ID'];
		} // foreach ($res_companies['result'] as $k => $v)

		$res_contact = [];

		//echo "res_company\n";
		//print_r($res_company);

		$result_batch = $db_gd->callBatch($batch_contact_params, 1); // halt_on_errors

		$batch_contact_params = [];

		//exit(0);

		if (! is_array($result_batch))
		{
			echo ' Ошибки при поиске контактов: ', print_r($result_batch), LINE_BREAK;
			$ui->total($total_contacts_changed, $total_company_found);
			$ui->close_output();
			exit(0);
		}

		foreach ($result_batch['result']['result'] as $company_id => $contacts)
		{
			foreach ($contacts as $k => $contact_info)
			{
				//echo 'ass_id/co_id: ', $contacts[$k]['ASSIGNED_BY_ID'], '-', 
				//	$companies_50[$company_id], ' ';
				if ($contacts[$k]['ASSIGNED_BY_ID'] != $companies_50[$company_id])
				{
					$contacts_to_change[$contacts[$k]['ID']] = $companies_50[$company_id];
					echo $contacts[$k]['ID'] . ' ';
				}
				else
				{
					echo 'skipping company ', $contacts[$k]['COMPANY_ID'], "\n";
				}
				
				if (count($contacts_to_change) == 50)
				{
					// Сбрасываем в batch update

					$result = [];

					if (! DRY_RUN)
					{
						$result = updateContacts($db_gd, $contacts_to_change);
					}
					else
					{
						echo 'DRY-RUN updateContacts...', LINE_BREAK;
					}

					if (is_array($result))
					{ // данные получены
							$total_contacts_changed += count($contacts_to_change); // ok
					}
					else
					{
						echo ' Ошибки при апдейте контактов: ', print_r($result), LINE_BREAK;
						$ui->total($total_contacts_changed, $total_company_found);
						$ui->close_output();
						exit(0);
					}

					if (RUN_MAX_RECORDS == $counter)
					{
						echo LINE_BREAK, '---RUN_MAX_RECORDS event. Exiting.---', LINE_BREAK;
						$ui->total($total_contacts_changed, $total_company_found);
						$ui->close_output();
						exit(0);
					}

					$counter++;

					// Обнуляем массив изменямых контактов
					$contacts_to_change = [];
					echo "\n" . $total_contacts_changed . " so far...\n";
				} //if (count($contacts_to_change) == 50)
			} // foreach($contacts as $k => $contact_info)
		}
	} //foreach ($db_gd->bx24Get50('crm.company.list', $company_params))

	if (ob_get_level() > 0)
	{
		ob_flush();
	}
}// foreach

// Сбрасываем остатки контактов в $contacts_to_change
if (count($contacts_to_change))
{
	// Сбрасываем в batch update
	$result = updateContacts($db_gd, $contacts_to_change);

	if (is_array($result))
	{ // данные получены
		$total_contacts_changed += count($contacts_to_change); // ok
	}
	else
	{
		echo ' Ошибки при апдейте контактов: ', print_r($result), LINE_BREAK;
	}
} //if (count($contacts_to_change))


$ui->total($total_contacts_changed, $total_company_found);
$ui->close_output();
