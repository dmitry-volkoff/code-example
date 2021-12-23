<?php declare(strict_types=1);
namespace CRM\Utils;

/**
 * Class: Template
 *
 * @see TemplateInterface
 */
class Template implements TemplateInterface
{
	public $template_file = '';
	public $template_contents = '';

	/**
	 * __construct
	 *
	 *
	 * @param string $template_file
	 *
	 * @return
	 */
	public function __construct(string $template_file)
	{
		$this->template_file = $template_file;
	}

	/**
	 * render_buffer
	 *
	 *
	 * @param object $tdto
	 *
	 * @return string
	 */
	public function render_buffer(object &$tdto) : string
	{
		ob_start();
		include($this->template_file);
		return ob_get_clean();
	}
	
	/**
	 * render
	 *
	 *
	 * @param object $tdto
	 *
	 * @return void
	 */
	public function render(object &$tdto): void
	{
		echo $this->render_buffer($tdto);
	}
}
