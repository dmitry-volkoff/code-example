<?php
namespace CRM\Utils;

/**
 * Bx24Rest
 */
class Bx24Rest
{
	public $web_hook_url     = '';
	public $total            = 0; // $result['total']
	protected $web_hook_host = '';
	protected $start         = 0; // $result['next']

	public static $calls           = 0;
	public static $time_last_start = 0;

	public function __construct(string $web_hook_url_)
	{
		// Устанавливаем URL для REST-запросов к битриксу.
		$this->web_hook_url  = $web_hook_url_;
		$this->web_hook_host = parse_url($web_hook_url_, PHP_URL_HOST);
	}

	protected function setWebHookURL()
	{
		CrestExt::$web_hook_url = $this->web_hook_url;
	}

	/**
	 * Call crest method
	 *
	 * @param string|string $b24_method
	 * @param array|array $b24_params
	 * @return type
	 */

	public function call(string $b24_method = '', array $b24_params = []) : array
	{
		$min_request_lag_usec = 500000; // минимальный интервал мксек между запросами
		$call_interval_usec   = 500000; // вычисленный интервал мксек между запросами

		if (empty($b24_method))
		{
			//trigger_error("Не указан метод.");
			//return [];
			return ['error' => 'Invalid params', 
				'error_information' => 'Не указан метод для ' . __METHOD__];

		}

		$time_start = microtime(true);
		static::$calls++;

		if (static::$time_last_start)
		{
			$call_interval_usec = 
				(int) (($time_start - static::$time_last_start) * 1000000);
		}

		// Притормаживаем запросы, если скорость их поступления >2 запроса в сек
		if ($call_interval_usec < $min_request_lag_usec)
		{
			//echo ' ' . $b24_method . '(', $min_request_lag_usec - $call_interval_usec, ')';
			usleep($min_request_lag_usec - $call_interval_usec);
		}

		static::$time_last_start = microtime(true);

		$this->setWebHookURL();

		$time_end = 0;
		$result   = [];

		$result = CrestExt::call(
			$b24_method,
			$b24_params
		);

		if (! is_array($result))
		{
			$time_end = microtime(true);
			$time     = $time_end - $time_start;
			
			static::$calls++;

			echo $this->web_hook_host . ": Пустой результат запроса " . 
				' (' . $time_end . '/' . $time . '/' . static::$calls . ')' . LINE_BREAK;
			
			//throw new RuntimeException('result[] is not an array when calling ' . $b24_method);
			
			$this->total = 0;
			return [];
		}

		if (array_key_exists('total', $result))
		{
			$this->total = (int) $result['total'];
		}

		if (array_key_exists('next', $result))
		{
			$this->start = (int) $result['next'];
		}

		if (array_key_exists('error', $result))
		{
			//trigger_error('Ошибка при вызове метода ' . $b24_method . ': ' . 
			//	print_r($result['error_information'] ?? $result['error'], false));

			$time_end = microtime(true);
			$time     = $time_end - $time_start;

			echo $this->web_hook_host . ': Ошибка при вызове ' . $b24_method . ': ';
			print_r($result['error_information'] ?? $result['error']);
			echo ' (' . $time_end . '/' . $time . '/' . 
				static::$calls . ')' . LINE_BREAK;
			
			return $result;
		}
		else
		{
			return $result;
		}
	}// function

	/**
	 * Call crest callBatch method
	 *
	 * @param array $b24_methods_params
	 * @param int halt_on_errors_flag
	 * @return array
	 */

	public function callBatch(array $b24_methods_params = [], int $halt_on_errors_flag = 0) : array
	{
		$min_request_lag_usec = 500000; // минимальный интервал мксек между запросами
		$call_interval_usec   = 500000; // вычисленный интервал мксек между запросами

		if (empty($b24_methods_params))
		{
			//trigger_error("Не указан масссив методов и параметров для callBatch.");
			return ['error' => 'Invalid params', 
				'error_information' => 
					'Не указан масссив методов и параметров для ' . __METHOD__];
		}
		
		$time_start = microtime(true);
		static::$calls++;

		if (static::$time_last_start)
		{
			$call_interval_usec = 
				(int) (($time_start - static::$time_last_start) * 1000000);
		}

		// Притормаживаем запросы, если скорость их поступления >2 запроса в сек
		if ($call_interval_usec < $min_request_lag_usec)
		{
			//echo ' ' . $b24_method . '(', $min_request_lag_usec - $call_interval_usec, ')';
			usleep($min_request_lag_usec - $call_interval_usec);
		}
		
		static::$time_last_start = microtime(true);

		$this->setWebHookURL();
		
		$time_end = 0;		
		$result   = [];

		$result = CrestExt::callBatch(
			$b24_methods_params,
			$halt_on_errors_flag
		);

		if (! is_array($result))
		{
			$time_end = microtime(true);
			$time     = $time_end - $time_start;

			static::$calls++;

			echo $this->web_hook_host . ": Пустой результат запроса " . 
				' (' . $time_end . '/' . $time . '/' . static::$calls . ')' . LINE_BREAK;
			
			//throw new RuntimeException('result[] is not an array when calling ' . $b24_method);
			
			$this->total = 0;
			return [];
		}

		if (array_key_exists('total', $result))
		{
			$this->total = (int) $result['total'];
		}

		if (array_key_exists('next', $result))
		{
			$this->start = (int) $result['next'];
		}

		if (array_key_exists('error', $result))
		{
			//trigger_error('Ошибка при вызове метода ' . $b24_method . ': ' . 
			//	print_r($result['error_information'] ?? $result['error'], false));
			$time_end = microtime(true);
			$time     = $time_end - $time_start;

			echo $this->web_hook_host . 
				': Ошибка при вызове метода ' . __METHOD__ . ': ';
			print_r($result['error_information'] ?? $result['error']);
			echo ' (' . $time_end . '/' . $time . '/' . 
				static::$calls . ')' . LINE_BREAK;

			return $result;
		}
		else
		{
			return $result;
		}
	}// function


	/**
	 * Отдаёт все записи по условию запроса порциями по 50
	 *
	 * @param string|string $b24_method
	 * @param array|array $b24_params
	 * @return type
	 */

	public function bx24Get50(string $b24_method = 'scope', array $b24_params = []) : iterable
	{
		$result = $this->call($b24_method, $b24_params);

		//if (! isset($result['total']))
		if (! is_array($result))
		{
			//throw new RuntimeException('No result["total"] returned in ' . $b24_method);
			//echo $this->web_hook_host . ': No result["total"] returned in ' . $b24_method . LINE_BREAK;
			//print_r($result);
			$result = [];
		}

		yield $result;

		// получаем все остальные записи сверх первых 50
		$current_call = 1; // номер текущего запроса

		$limit = 50; // записей в одном запросе
		$total = $result['total'] ?? 0;
		$calls = ceil((int) $total / $limit); // вычисляем требуемое количество запросов
		//echo '$calls=' . $calls . PHP_EOL;

		while ($current_call < $calls)
		{
			$b24_params['start'] = $current_call * $limit;

			$result = $this->call($b24_method, $b24_params);

			$current_call++;

			yield $result;
		} // while
	} // function

	/**
	 * Отдаёт по одной записи из запроса по условию.
	 *
	 * @param string|string $b24_method
	 * @param array|array $b24_params
	 * @return type
	 */

	public function bx24GetEeach(string $b24_method = 'scope', array $b24_params = []) : iterable
	{
		foreach ($this->bx24Get50($b24_method, $b24_params) as $result)
		{
			if (! array_key_exists('result', $result))
			{
				$result['result'] = [];
			}
			//print_r($result, false);
			foreach ($result['result'] as $key => $val)
			{
				yield $val;
			}
		}
	}

	/**
	 * Отдаёт по условию запроса `$b24_params` по одной записи из сущности `user`.
	 *
	 * @param array|array $b24_params
	 * @return type
	 */

	public function getUsers(array $b24_params = []) : iterable
	{
		$b24_method = 'user.get';

		foreach ($this->bx24GetEeach($b24_method, $b24_params) as $result)
		{
			//print_r($result, false);
			yield $result;
		}
	}

	/**
	 * Отдаёт по условию запроса `$b24_params` 
	 * по одной записи из сущности `user` с сортировкой по фамилии.
	 *
	 * @param array|array $b24_params
	 * @return type
	 */

	public function getUsersSortLastName(array $filter = []) : iterable
	{
		$b24_method = 'user.get';
		$b24_params = ['filter' => $filter, 'sort' => 'LAST_NAME', 'order' => 'ASC'];

		foreach ($this->bx24GetEeach($b24_method, $b24_params) as $result)
		{
			//print_r($result, false);
			yield $result;
		}
	}

	/**
	 * Отдаёт по условию запроса `$b24_params` по одной записи из сущности `deal`.
	 *
	 * @param array|array $b24_params
	 * @return type
	 */

	public function getDeals(array $b24_params = []) : iterable
	{
		$b24_method = 'crm.deal.list';

		foreach ($this->bx24GetEeach($b24_method, $b24_params) as $result)
		{
			//print_r($result, false);
			yield $result;
		}
	}

	/**
	 * Отдаёт по условию запроса `$b24_params` по одной записи из сущности `leads`.
	 *
	 * @param array|array $b24_params
	 * @return type
	 */

	public function getLeads(array $b24_params = []) : iterable
	{
		$b24_method = 'crm.lead.list';

		foreach ($this->bx24GetEeach($b24_method, $b24_params) as $result)
		{
			//print_r($result, false);
			yield $result;
		}
	}

	/**
	 * Отдаёт по условию запроса `$b24_params` по одной записи из сущности `tasks`.
	 * Не забываем про нижний регистр полей в $result: не ID, а id.
	 *
	 * @param array|array $b24_params
	 * @return type
	 */

	public function getTasks(array $b24_params = []) : iterable
	{
		$b24_method = 'tasks.task.list';

		foreach ($this->bx24Get50($b24_method, $b24_params) as $result)
		{
			if (! array_key_exists('result', $result))
			{
				$result['result'] = [];
			}

			if (! array_key_exists('tasks', $result['result']))
			{
				$result['result']['tasks'] = [];
			}

			//print_r($result, false);
			foreach ($result['result']['tasks'] as $key => $val)
			{
				yield $val;
			}
		} // foreach
	}
} // class CrestExt
