<?php declare(strict_types=1);
namespace CRM\Utils;

/**
 * Class: UICli
 *
 * @see UI
 */
class UICli implements UIInterface
{
	public $template_object;
	
	/**
	 * __construct
	 *
	 * @param string $template_file
	 *
	 * @return void
	 */
	public function __construct(string $template_file)
	{
		$this->template_object = (new TemplateFactoryMethod($template_file . '.txt'))->createTemplate();
	}

	/**
	 * render
	 *
	 * @param object $template_DTO
	 *
	 * @return void
	 */
	public function render(object &$template_DTO) : void
	{
		$this->template_object->render($template_DTO);
	}
	
	/**
	 * render_no_argument
	 *
	 * @param mixed $template_DTO
	 *
	 * @return void
	 */
	public function render_no_argument(&$template_DTO): void
	{
		$template_DTO->msg = $this->message('Отсутствует аргумент', 'error');
		$this->render($template_DTO);
	}

	/**
	 * message
	 *
	 * @param string $msg
	 * @param string $error
	 *
	 * @return array
	 */
	public function message(string $msg = '...ping...', string $error = '') : array
	{
		$msg_obj = [];
		$msg_heading = $error ? 'Ошибка: ' : 'Инфо: ';
		$msg_obj['message'] = $msg_heading . $msg;

		return $msg_obj;
	}
	
	/**
	 * total_buffered
	 *
	 * @param int $added_companies
	 * @param int $found_companies
	 *
	 * @return string
	 */	
	public function total_buffered(int $changed_contacts, int $found_companies) : string
	{
		echo LINE_BREAK;

		$time_end = microtime(true);

		$out = <<< EOT
		Найдено компаний    : $found_companies
		Исправлено контактов: $changed_contacts
		окончание: $time_end

		EOT;
		return $out;
	}

	/**
	 * total
	 *
	 * @param int $added_companies
	 * @param int $found_companies
	 *
	 * @return void
	 */
	public function total(int $changed_contacts, int $found_companies) : void
	{
		echo $this->total_buffered($changed_contacts, $found_companies);
	}
	
	/**
	 * hr_line_buffered
	 *
	 * @return string
	 */
	public function hr_line_buffered() : string
	{
		return str_repeat('-', 60) . PHP_EOL;
	}

	/**
	 * hr_line
	 *
	 * @return void
	 */
	public function hr_line() : void
	{
		echo $this->hr_line_buffered();
	}
	
	/**
	 * close_output_buffered
	 *
	 * @return string
	 */
	public function close_output_buffered() : string
	{
		return '';
	}

	/**
	 * close_output
	 *
	 * @return void
	 */
	public function close_output(): void
	{
		echo $this->close_output_buffered();
	}
}
