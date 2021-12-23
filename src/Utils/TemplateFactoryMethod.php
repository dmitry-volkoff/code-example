<?php declare(strict_types=1);
namespace CRM\Utils;

/**
 * Class: TemplateFactoryMethod
 *
 * @see TemplateFactory
 */
class TemplateFactoryMethod implements TemplateFactory
{
	private $template_file;

	/**
	 * __construct
	 *
	 *
	 * @param mixed $template_file
	 *
	 * @return
	 */
	public function __construct($template_file)
	{
		$this->template_file = $template_file;
	}

	/**
	 * createTemplate
	 *
	 *
	 *
	 * @return Template_Interface
	 */
	public function createTemplate(): TemplateInterface
	{
		return new Template($this->template_file);
	}
}
